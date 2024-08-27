<?php

namespace App\Integrations\Moadian;

use App\Exceptions\SystemException\UserNotFoundOnMainAppException;
use App\Integrations\MainApp\MainAppAPIService;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\MoadianLog;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Jooyeshgar\Moadian\Invoice as MoadianInvoice;
use Jooyeshgar\Moadian\InvoiceHeader;
use Jooyeshgar\Moadian\InvoiceItem as MoadianInvoiceItem;
use Jooyeshgar\Moadian\Payment as MoadianPayment;

class MoadianFactory
{
    private MoadianInvoice $moadianInvoice;
    private Collection $services;
    private Collection $domains;

    public function createMoadianInvoiceDTO(Invoice $invoice): MoadianInvoice
    {
        // # Refunded invoice need source invoice for:
        // # 1. Tax id required to create refund invoice
        // # 2. Item type(sstid) must match with source invoice item type.
        $refunded_invoice_source = $invoice->status == Invoice::STATUS_REFUNDED ? Invoice::query()->find($invoice->source_invoice) : null;

        self::createInvoiceHeader($invoice, $refunded_invoice_source)
            ->createInvoiceBody($invoice, $refunded_invoice_source)
            ->creatInvoicePayments($invoice);

        return $this->moadianInvoice;
    }

    private function createInvoiceHeader(Invoice $invoice, ?Invoice $refunded_invoice_source = null): self
    {

        $date = str_pad(Carbon::parse($invoice->paid_at)->timestamp, 13, 0, STR_PAD_RIGHT);
        $header = new InvoiceHeader(config('moadian.username'));
        $header->setTaxID(Carbon::parse($invoice->paid_at), $invoice->id);
        $header->indatim = $date;
        $header->indati2m = $date;
        $header->inty = 1;
        $header->inno = str_pad($invoice->id, 10, 0, STR_PAD_LEFT);
        $header->irtaxid = !empty($refunded_invoice_source) ? MoadianLog::query()->where('invoice_id', $refunded_invoice_source->id)->first()?->tax_id : null;
        $header->inp = 1; //invoice pattern
        $header->ins = $invoice->status == Invoice::STATUS_REFUNDED ? 4 : 1; // invoice type
        $header->tins = '10103421620';
        $client = data_get(MainAppAPIService::getClients($invoice->profile_id), 0);
        if (empty($client)) {
            throw UserNotFoundOnMainAppException::make($invoice->id);
        }
        $invoice->client = $client;
        $header->tob = $invoice->client->is_legal == 1 ? 2 : 1;
        $header->bid = ($invoice->client->is_legal == 1 && $invoice->client->company_national_code) ?
            $invoice->client->company_national_code :
            str_pad($invoice->client->national_code, 10, 0, STR_PAD_LEFT);
        $header->tinb = ($invoice->client->is_legal == 1 && $invoice->client->company_national_code) ?
            $invoice->client->company_national_code :
            str_pad($invoice->client->national_code, 10, 0, STR_PAD_LEFT);

        if ($invoice->client->is_legal == 0)
            $header->tinb = str_pad($header->tinb, 14, 0, STR_PAD_LEFT);

        $postal_code = ($invoice->client->is_legal == 1 && $invoice->client->company_postal_code) ?
            preg_replace("/[^0-9]/", "", fa2en($invoice->client->company_postal_code)) :
            preg_replace("/[^0-9]/", "", fa2en($invoice->client->postal_code));
        $header->bpc = (isset($postal_code) && strlen($postal_code) == 10) ? $postal_code : '1234567890';

        $tax = 0;
        $itemSum = 0;
        foreach ($invoice->items->all() as $item) {
            $tax += floor(floor($item->amount) * ($invoice->tax_rate / 100));
            $itemSum += floor($item->amount);
        }

        $negativeItems = abs($invoice->items->where('amount', '<', 0)->sum('amount'));

        // sum price before discount
        $header->tprdis = floor($itemSum);

        // sum discounts
        $header->tdis = $negativeItems;

        // sum price after discount
        $header->tadis = floor($itemSum) - $negativeItems;


        $header->tvam = $tax;
        $header->todam = 0;
        $header->tbill = $tax + $itemSum;

        if ($invoice->status == Invoice::STATUS_COLLECTIONS) { // Collection Invoices
            $header->setm = 2;
            $header->cap = floor($invoice->tax) + floor($invoice->sub_total);
        } else { // Paid Invoices
            $header->setm = 1;
            $header->insp = floor($invoice->tax) + floor($invoice->sub_total);
        }

        $this->moadianInvoice = new MoadianInvoice($header);

        return $this;
    }

    public function createInvoiceBody(Invoice $invoice, ?Invoice $refunded_invoice_source = null): self
    {
        $items = $invoice->items;

        $negativeItems = abs($items->where('amount', '<', 0)->sum('amount'));

        $positiveItems = $items->where('amount', '>', 0);

        $this->getProductsAndDomainsList($positiveItems);

        /** @var Item $item */
        foreach ($positiveItems->all() as $item) {

            $amount = $item->amount;
            $priceAfterDiscount = $amount;

            $discount = 0;
            if ($negativeItems && $negativeItems > 0) {

                if ($amount > $negativeItems) {
                    $discount = $negativeItems;
                    $amount = $amount - $negativeItems;
                    $priceAfterDiscount = $amount;
                    $negativeItems = 0;
                } else {
                    $negativeItems -= $amount;
                    $discount = $amount;
                    $priceAfterDiscount = 0;
                }
            }

            $body = new MoadianInvoiceItem();

            if (empty($refunded_invoice_source)) {
                [$sstid, $sstt] = self::getMappedProductId($item);
            } else {
                [$sstid, $sstt] = self::getMappedProductId($refunded_invoice_source->items->first());
            }

            $body->sstid = $sstid;
            $body->sstt = $sstt;
            $body->am = '1';
            $body->mu = 1627;
            $body->fee = floor($amount);
            // sum price before discount
            $body->prdis = floor($item->amount);
            // discount
            $body->dis = $discount;
            // price after discount
            $body->adis = floor($priceAfterDiscount);
            $body->vra = $invoice->tax_rate;
            //$body->vam = round(config('payment.tax.total') * $item->amount / 100); // or directly calculate here like floor($body->adis * $body->vra / 100)
            $body->vam = floor($body->adis * $body->vra / 100);
            $body->tsstam = round($body->fee + $body->vam);
            $this->moadianInvoice->addItem($body);
        }

        return $this;
    }

    private function creatInvoicePayments(Invoice $invoice): self
    {

        foreach ($invoice->transactions()
                     ->where('amount', '>', 0)
                     ->whereIn('status', [
                         Transaction::STATUS_SUCCESS,
                     ])
                     ->get() as $transaction) {
            $payment = new MoadianPayment();
            $payment->trn = $transaction->id;
            $payment->trn = str_replace('CREDIT_', '', $payment->trn);
            $payment->trn = str_pad($payment->trn, 9, 0, STR_PAD_LEFT);
            $payment->pdt = Carbon::parse($transaction->created_at)->timestamp * 1000;
            $payment->pmt = 5;
            $this->moadianInvoice->addPayment($payment);
        }

        return $this;
    }

    private function getMappedProductId(Item $item): array
    {
        $code = null;
        $description = '';

        switch ($item->invoiceable_type) {
            case Item::TYPE_HOSTING:
            case Item::TYPE_PRODUCT_SERVICE:
            case Item::TYPE_PRODUCT_SERVICE_UPGRADE:
                $service = $this->services->where('id', $item->invoiceable_id)->first();
		$product = data_get($service, 'product');
                $productGroup = data_get($product, 'product.group.name');
                if (Str::contains($product['name'], ['نمایندگی'])) {
                    $code = 2330001496167;
                    $description = 'پنل نمايندگي هاست وب سايت';
                    break;
                }
                if ($productGroup == 'transactional-email') {
                    $code = 2330001496259;
                    $description = 'سرويس هاي ايميل هاست وب سايت';
                    break;
                }
                if ($product['name'] == 'Backup Storage') {
                    $code = 2330001496174;
                    $description = 'پشتيبان گيري از زيرساخت ابري';
                    break;
                }
                if (Str::contains($productGroup, ['ssl-certificate', 'License'])) {
                    $code = 2330001496181;
                    $description = 'فروش لايسنس و گواهي هاي نرم افزار';
                    break;
                }
                if (Str::contains($product['name'], ['Co-Location', 'Co Location'])) {
                    $code = 2330001496198;
                    $description = 'فروش فضاي اشتراكي ديتا سنتر به منظور هاست وب سايت';
                    break;
                }
                if (Str::contains($product['name'], ['سرویس مانیتورینگ'])) {
                    $code = 2330001496020;
                    $description = 'فروش مجوز لايسنس مانيتورينگ';
                    break;
                }
                if (Str::contains($product['name'], ['فروش ترافیک'])) {
                    $code = 2330001496242;
                    $description = 'تخصيص ترافيك IXP';
                    break;
                }
                if (Str::contains($product['name'], ['Transmission', 'Radio'])) {
                    $code = 2330001496051;
                    $description = 'تخصيص پهناي باند انتقال نقطه به نقطه';
                    break;
                }
                if (Str::contains($productGroup, ['Windows-host'])) {
                    $code = 2330001496044;
                    $description = 'هاست وب سايت ويندوز';
                    break;
                }
                if (Str::contains($product['name'], ['internet', 'Internet', 'Internet BW'])) {
                    $code = 2330001496334;
                    $description = 'خدمات تخصيص پهناي باند و برقراري ارتباط اينترنتي';
                    break;
                }
                if (Str::contains($productGroup, ['Host-backup-ir'])) {
                    $code = 2330001496082;
                    $description = 'هاست وب سايت بك آپ';
                    break;
                }
                if (Str::contains($productGroup, ['Host-Download'])) {
                    $code = 2330001496013;
                    $description = 'هاست دانلود';
                    break;
                }
                if (Str::contains($productGroup, ['Host-Linux'])) {
                    $code = 2330001496129;
                    $description = 'هاست وب سايت لينوكس';
                    break;
                }
                if (Str::contains($productGroup, ['wordpress', 'WordPress'])) {
                    $code = 2330001496068;
                    $description = 'هاست وب سايت ورد پرس';
                    break;
                }
                if (Str::contains($productGroup, ['anycast'])) {
                    $code = 2330001496099;
                    $description = 'هاست وب سايت Anycast';
                    break;
                }
                if (Str::contains($productGroup, ['dedicate'])) {
                    $code = 2330001496327;
                    $description = 'اجاره سرور اختصاصي';
                    break;
                }
                if (Str::contains($productGroup, ['Host-Framework-IR'])) {
                    $code = 2330001496310; // TODO double check
                    $description = 'هاست وب سايت لاراول';
                    break;
                }
                if (Str::contains($productGroup, ['Digital-Content'])) {
                    $code = 2330001496341;
                    $description = 'خدمات تخصيص پهناي باند و برقراري ارتباط اينترنتي';
                    break;
                }
                if (Str::contains($productGroup, ['software', 'Software', 'نرم افزار'])) {
                    $code = 2330001496136;
                    $description = 'خدمات سفارشي سازي نرم افزار';
                    break;
                }
                break;

            case Item::TYPE_DOMAIN_SERVICE:
            case Item::TYPE_REFUND_DOMAIN:
                $domain = $this->domains->where('id', $item->invoiceable_id)->first();
                if (isset($domain) && isset($domain['registrar']) && Str::contains($domain['registrar']['name'], ['irnic', 'Irnic'])) {
                    $code = 2330001496112; // TODO dobuble check دامنه داخلی
                    $description = 'تخصيص و مديريت دامنه هاي داخلي';
                } else {
                    $code = 2330001496266;
                    $description = 'تخصيص و مديريت دامنه بين المللي';
                }
                break;
            case Item::TYPE_MIHAN_NIC_IR:
                $code = 2330001496112; // TODO dobuble check دامنه داخلی
                $description = 'تخصيص و مديريت دامنه هاي داخلي';
            case Item::TYPE_MIHAN_NIC_COM:
                $code = 2330001496266;
                $description = 'تخصيص و مديريت دامنه بين المللي';

            case Item::TYPE_CLOUD:
                $code = 2330001496273; // TODO double check
                $description = 'ارائه زيرساخت سرورهاي ابري';
                break;
            case Item::TYPE_ADMIN_TIME:
                $code = 2330001496204; // TODO خدمات پشتیبانی double check
                $description = 'پشتيباني و نگهداري نرم افزار هاي سرور و وب سايت';
                break;
        }

        if (is_null($code)) {
            info('mapping not found for relId: ' . $item->invoiceable_id . ' - type: ' . $item->invoiceable_type);
            $code = 2330001496129;
            $description = 'خدمات هاست وب سايت';
        }

        return [$code, $description];
    }


    public function getProductsAndDomainsList($positiveItems): void
    {
        $serviceListById = collect();
        $domainListById = collect();

        $positiveItems
            ->whereIn('invoiceable_type', [
                Item::TYPE_HOSTING,
                Item::TYPE_PRODUCT_SERVICE,
                Item::TYPE_PRODUCT_SERVICE_UPGRADE
            ])
            ->each(function (Item $item) use (&$serviceListById) {
                $serviceListById->push($item->invoiceable_id);
            });

        $positiveItems
            ->whereIn('invoiceable_type', [
                Item::TYPE_DOMAIN_SERVICE
            ])
            ->each(function (Item $item) use (&$domainListById) {
                $domainListById->push($item->invoiceable_id);
            });

        $this->services = collect(
            MainAppAPIService::getServices(
                serviceIds: $serviceListById->toArray()
            )
        );

        $this->domains = collect(
            MainAppAPIService::getServices(
                serviceIds: $domainListById->toArray(),
                type: 'domain'
            )
        );
    }
}
