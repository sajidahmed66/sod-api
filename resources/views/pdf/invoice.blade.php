<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="{{ public_path('css/invoice/style.css') }}" media="all" />
    <style>
        body {
            font-family: 'siyamrupali', 'arial', 'roboto', sans-serif;
        }
    </style>
</head>
<body>
<header class="clearfix">
    <div id="logo">
        @if (config('app.env') !== 'local' && $order->vendor->settings->logo)
            <img height="100px" src="{{ Storage::url($order->vendor->settings->logo) }}">
        @endif
    </div>
    <h1>INVOICE #{{ $order->order_no }}</h1>

    <table style="background: white">
        <tr style="background: white">
            <td width="50%" style="background: white; text-align: left">
                <div id="company" class="clearfix">
                    <div style="font-weight: bold">{{ $order->vendor->name }}</div>
                    <div style="font-weight: normal; font-size: 14px">{{ $order->vendor->settings->host }}</div>
                    <div style="font-weight: normal; font-size: 14px">Phone: {{ $order->vendor->settings->phone }}</div>
                    <div style="font-weight: normal; font-size: 14px">Address: {{ $order->vendor->settings->address }}</div>
                    <div style="font-weight: normal; font-size: 14px">Order Date: {{ $order->created_at->format('M j, Y h:i A') }}</div>
                    @if($shippingDate)
                        <div style="font-weight: normal; font-size: 14px">Shipping Date: {{ $shippingDate }}</div>
                    @endif
                </div>
            </td>
            <td width="50%" style="background: white">
                <div id="company" class="clearfix">
                    <div><b>{{ $order->name }}</b></div>
                    <div style="font-weight: normal; font-size: 14px">{{ $order->address }}<br /> {{ $order->area->name_bn }}, {{ $order->city->name_bn }}</div>
                    <div style="font-weight: normal; font-size: 14px">(+88) {{ $order->mobile }}</div>
                </div>
            </td>
        </tr>
    </table>

</header>
<main>
    <table>
        <thead>
        <tr>
            <th class="service" style="text-align: left">NAME</th>
            <th class="desc" style="text-align: left">DESCRIPTION</th>
            <th style="text-align: right">PRICE</th>
            <th style="text-align: right">QTY</th>
            <th style="text-align: right">TOTAL</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
        <tr>
            @if($item->product_price_name)
                <td class="service">{{ $item->product_name }} &nbsp; ({{ $item->product_price_name }})</td>
            @else <td class="service">{{ $item->product_name }}</td>
            @endif

            <td class="desc">{{ $item->product_sub_text }}</td>
            <td class="unit">
                ৳{{ $item->original_unit_price }}
            </td>
            <td class="qty">{{ $item->quantity }}</td>
            <td class="total">৳{{ $item->quantity * $item->original_unit_price }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="4">SUBTOTAL</td>
            <td class="total">৳{{ ($order->sub_total - $order->shipping_cost)}}</td>
        </tr>
        <tr>
            <td colspan="4">SHIPPING COST</td>
            <td class="total">৳{{ $order->shipping_cost }}</td>
        </tr>
        <tr>
            <td colspan="4">DISCOUNT</td>
            <td class="total">৳{{ $order->discount }}</td>
        </tr>
        <tr>
            <td colspan="4" class="grand total">GRAND TOTAL</td>
            <td class="grand total">৳{{ $order->total }}</td>
        </tr>
        <tr>
            <td colspan="4">PAID</td>
            <td class="total">৳{{ $order->paid }}</td>
        </tr>
        <tr>
            <td colspan="4">DUE</td>
            <td class="total">৳{{ $order->due }}</td>
        </tr>
        </tbody>
    </table>
</main>
<footer>
    Invoice was created on a computer and is valid without the signature and seal.
</footer>
</body>
</html>
