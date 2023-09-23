<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "Order",
    "merchant": {
        "@type": "Organization",
        "name": "Hostiran.com"
    },
    "orderNumber": "{{$invoice->id}}",
    "orderStatus": "http://schema.org/OrderDelivered",
    "priceCurrency": "IRR",
    "price": "{{$invoice->total}}",
    "acceptedOffer": [
        @foreach($invoice->items as $index => $item)
        {
            "@type": "Offer",
            "itemOffered": {
                "@type": "Product",
                "name": "{{$item->invoiceable_type}}"
            },
            "price": "{{$item->amount}}",
            "priceCurrency": "IRR",
            "eligibleQuantity": {
                "@type": "QuantitativeValue",
                "value": "1"
            }
        }
        @if($index != count($invoice->items) - 1)
            ,
        @endif
        @endforeach
    ],
    "url": "https://hostiran.net/profile/panel/finance/invoice/{{$invoice->id}}",
    "potentialAction": {
        "@type": "ViewAction",
        "url": "https://hostiran.net/profile/panel/finance/invoice/{{$invoice->id}}"
    }
}
</script>
Invoice #{{$invoice->id}} processed
</body>
</html>
