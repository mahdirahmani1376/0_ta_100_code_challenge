<?php

namespace App\Integrations\Moadian;

use App\Integrations\MainApp\MainAppAPIService;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\MoadianLog;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Jooyeshgar\Moadian\Invoice as MoadianInvoice;
use Jooyeshgar\Moadian\InvoiceHeader;
use Jooyeshgar\Moadian\InvoiceItem as MoadianInvoiceItem;
use Jooyeshgar\Moadian\Payment as MoadianPayment;

class MoadianFactory
{
    private MoadianInvoice $moadianInvoice;

    public function createMoadianInvoiceDTO(Invoice $invoice): MoadianInvoice
    {
        self::createInvoiceHeader($invoice)
            ->createInvoiceBody($invoice)
            ->creatInvoicePayments($invoice);

        return $this->moadianInvoice;
    }

    private function createInvoiceHeader(Invoice $invoice): self
    {
        $date = str_pad(Carbon::parse($invoice->status == Invoice::STATUS_COLLECTIONS ? $invoice->created_at : $invoice->paid_at)->timestamp, 13, 0, STR_PAD_RIGHT);
        $header = new InvoiceHeader(config('moadian.username'));
        $header->setTaxID(Carbon::parse($invoice->status == Invoice::STATUS_COLLECTIONS ? $invoice->created_at : $invoice->paid_at), $invoice->id);
        $header->indatim = $date;
        $header->indati2m = $date;
        $header->inty = 1; //invoice type
        $header->inno = str_pad($invoice->id, 10, 0, STR_PAD_LEFT);
        $header->irtaxid = $invoice->status == Invoice::STATUS_REFUNDED ? MoadianLog::query()->where('invoice_id', $invoice->source_invoice)->first()->tax_id : null; // shomare sorathesabe marja baraye refund ha
        $header->inp = 1; //invoice pattern
        $header->ins = $invoice->status == Invoice::STATUS_REFUNDED ? 4 : 1; // invoice type
        $header->tins = '10103421620';
        $client = MainAppAPIService::getClients($invoice->profile->client_id)[0];
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
            $tax += floor(floor($item->amount) * 9 / 100);
            $itemSum += floor($item->amount);
        }

        $header->tprdis = floor($itemSum);
        $header->tdis = 0;
        $header->tadis = floor($itemSum);
        $header->tvam = $tax;
        $header->todam = 0;
        $header->tbill = floor($tax) + floor($itemSum);

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

    private function createInvoiceBody(Invoice $invoice): self
    {
        $negativeItems = abs($invoice->items->where('amount', '<', 0)->sum('amount'));

        /** @var Item $item */
        foreach ($invoice->items->where('amount', '>', 0)->all() as $item) {

            $amount = $item->amount;


            if ($negativeItems && $negativeItems > 0) {

                if ($amount > $negativeItems) {
                    $amount = $amount - $negativeItems;
                    $negativeItems = 0;
                } else {
                    $negativeItems = $negativeItems - $amount;
                    continue;
                }
            }

            $body = new MoadianInvoiceItem();
            [$sstid, $sstt] = self::getMappedProductId($item);
            $body->sstid = $sstid;
            $body->sstt = $sstt;
            $body->am = '1';
            $body->mu = 1627;
            $body->fee = floor($amount);
            $body->prdis = floor($amount);
            $body->dis = 0;
            $body->adis = floor($amount);
            $body->vra = 9;
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
                         Transaction::STATUS_OPG_PAID,
                         Transaction::STATUS_IPG_PAID,
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
                $product = MainAppAPIService::getProductOrDomain('product', $item->invoiceable_id)['product'];
                if (Str::contains($product['name'], ['نمایندگی'])) {
                    $code = 2330001496167;
                    $description = 'پنل نمايندگي هاست وب سايت';
                    break;
                }
                if ($product['product_group']['name'] == 'transactional-email') {
                    $code = 2330001496259;
                    $description = 'سرويس هاي ايميل هاست وب سايت';
                    break;
                }
                if ($product['name'] == 'Backup Storage') {
                    $code = 2330001496174;
                    $description = 'پشتيبان گيري از زيرساخت ابري';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['ssl-certificate', 'License'])) {
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
                if (Str::contains($product['product_group']['name'], ['Windows-host'])) {
                    $code = 2330001496044;
                    $description = 'هاست وب سايت ويندوز';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['Host-backup-ir'])) {
                    $code = 2330001496082;
                    $description = 'هاست وب سايت بك آپ';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['Host-Download'])) {
                    $code = 2330001496013;
                    $description = 'هاست دانلود';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['Host-linux'])) {
                    $code = 2330001496129;
                    $description = 'هاست وب سايت لينوكس';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['wordpress', 'WordPress'])) {
                    $code = 2330001496068;
                    $description = 'هاست وب سايت ورد پرس';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['anycast'])) {
                    $code = 2330001496099;
                    $description = 'هاست وب سايت Anycast';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['dedicate'])) {
                    $code = 2330001496327;
                    $description = 'اجاره سرور اختصاصي';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['Host-Framework-IR'])) {
                    $code = 2330001496310; // TODO double check
                    $description = 'هاست وب سايت لاراول';
                    break;
                }
                if (Str::contains($product['product_group']['name'], ['Digital-Content'])) {
                    $code = 2330001496341;
                    $description = 'خدمات تخصيص پهناي باند و برقراري ارتباط اينترنتي';
                    break;
                }
                break;
            case Item::TYPE_DOMAIN_SERVICE:
                $domain = MainAppAPIService::getProductOrDomain('domain', $item->invoiceable_id);
                if (isset($domain) && isset($domain['registrar']) && Str::contains($domain['registrar']['name'], ['irnic', 'Irnic'])) {
                    $code = 2330001496112; // TODO dobuble check دامنه داخلی
                    $description = 'تخصيص و مديريت دامنه هاي داخلي';
                } else {
                    $code = 2330001496266;
                    $description = 'تخصيص و مديريت دامنه بين المللي';
                }
                break;
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
            $code = 2330001496013;
            $description = 'خدمات هاست وب سايت';
        }

        return [$code, $description];
    }
}
