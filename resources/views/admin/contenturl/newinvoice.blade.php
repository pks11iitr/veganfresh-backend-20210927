<html>
<head>
    <title>Invoice</title>
    <style type="text/css">
        #page-wrap {
            width: 700px;
            margin: 0 auto;
        }
        .center-justified {
            text-align: justify;
            margin: 0 auto;
            width: 30em;
        }
        table.outline-table {
            border: 1px solid;
            border-spacing: 0;
        }
        tr.border-bottom td, td.border-bottom {
            border-bottom: 1px solid;
        }
        tr.border-top td, td.border-top {
            border-top: 1px solid;
        }
        tr.border-right td, td.border-right {
            border-right: 1px solid;
        }
        tr.border-right td:last-child {
            border-right: 0px;
        }
        tr.center td, td.center {
            text-align: center;
            vertical-align: text-top;
        }
        td.pad-left {
            padding-left: 5px;
        }
        tr.right-center td, td.right-center {
            text-align: right;
            padding-right: 50px;
        }
        tr.right td, td.right {
            text-align: right;
        }
        .grey {
            background:#edebeb;
        }
        .black {
            background:black;
        }
    </style>
</head>
<body>
<div id="page-wrap">
    <table width="100%">
        <tbody>
        <tr>
        @if($invoice->image)
            <td width="50%">
                 <img style="height:100px;width: 100px;margin-left:250px" src="data:image/jpeg;base64,{{base64_encode(file_get_contents($invoice->image))}}">
            </td>
             
            <td width="50%">
                <img style="height:100px;width: 100px;margin-left:250px" src="data:image/jpeg;base64,{{base64_encode(file_get_contents($invoice->image))}}">
            </td>
            @endif
        </tr>
        </tbody>
    </table>
    <table width="100%" class="outline-table" style="margin-bottom: 10px;">
        <tbody>
        <tr class="black">
            <td colspan="5" style="color: white"><strong>Invoice No # {{$orders->invoice_number}}</strong><br>Pan/GST: {{$invoice->pan_gst??''}}</td>
            <td colspan="1" style="color: white">Date: {{date('d/m/Y h:ia', strtotime($orders->created_at))}}<br>Status: {{strtoupper($orders->status)}}</td>
        </tr>
        <tr class="black">
            <td colspan="6" style="color: white"><strong>Delivery Date: @if($orders->is_express_delivery){{'60 Min Express Delivery'}}@else{{date('D d ,Y', strtotime($orders->delivery_date))}} ({{$orders->timeslot->name??''}})@endif</strong></td>
            <td></td>
        </tr>
        </tbody>
    </table>
    <table width="100%" class="outline-table" style="margin-bottom: 10px;">
        <tbody>
        <tr class="border-bottom border-right grey">
            <td colspan="6"><strong>Sold to:</strong></td>
            <td colspan="6"><strong>Seller:</strong></td>
        </tr>

        <tr class="border-right">
            <td colspan="6">{{$orders->deliveryaddress->first_name??''}} {{$orders->deliveryaddress->last_name??''}}<br>
                <span>{{$orders->deliveryaddress->mobile_no??''}}, {{$orders->deliveryaddress->email??''}}</span><br>
                <span>{{$orders->deliveryaddress->house_no??''}}, {{$orders->deliveryaddress->appertment_name??''}}</span><br>
                @if(!empty($orders->deliveryaddress->street) || !empty($orders->deliveryaddress->landmark))
                <span>{{!empty($orders->deliveryaddress->street)?$orders->deliveryaddress->street.', ':''}}{{$orders->deliveryaddress->landmark??''}},</span><br>
                @endif
{{--                <span>{{$orders->deliveryaddress->address_type??''}} , {{$orders->deliveryaddress->other_text??''}},</span><br>--}}
                <span>{{$orders->deliveryaddress->area??''}}</span>
                <span>{{$orders->deliveryaddress->city??''}}, {{$orders->deliveryaddress->pincode??''}}</span>

            </td>
            <td colspan="6">
{{--            {{$orders->deliveryaddress->first_name??''}} {{$orders->deliveryaddress->last_name??''}}<br>--}}
{{--                <span>{{$orders->deliveryaddress->house_no??''}}, {{$orders->deliveryaddress->appertment_name??''}}</span><br>--}}
{{--                @if(!empty($orders->deliveryaddress->street) || !empty($orders->deliveryaddress->landmark))--}}
{{--                    <span>{{!empty($orders->deliveryaddress->street)?$orders->deliveryaddress->street.', ':''}}{{$orders->deliveryaddress->landmark??''}},</span><br>--}}
{{--                @endif--}}
{{--                --}}{{--                <span>{{$orders->deliveryaddress->address_type??''}} , {{$orders->deliveryaddress->other_text??''}},</span><br>--}}
{{--                <span>{{$orders->deliveryaddress->area??''}}</span>--}}
{{--                <span>{{$orders->deliveryaddress->city??''}}, {{$orders->deliveryaddress->pincode??''}}</span>--}}


                {{$invoice->organization_name??''}}<br>
                {{$invoice->address??''}}

            </td>
        </tr>
        </tbody>
    </table>
    <table width="100%" class="outline-table" style="margin-bottom: 10px;">
        <tbody>
        <tr class="border-bottom border-right grey">
            <td colspan="6"><strong>Payment Method:</strong></td>
            <td colspan="6"><strong>Shipping Method:</strong></td>
        </tr>

        <tr class="border-right">
            <td colspan="6">@if($orders->payment_mode=='COD'){{'Cash On Delivery'}}@else{{'Net Banking'}}@endif</td>
            <td colspan="6">@if($orders->delivery_charge==0){{'Free'}}@else{{'Paid'}}@endif
            <br> @if($orders->delivery_charge==0){{'(Total Shipping Charges Rs. 0.00)'}}@else{{'(Total Shipping Charges Rs. '.$orders->delivery_charge.')'}}@endif</td>
        </tr>

        </tbody>
    </table>
    <table width="100%" class="outline-table">
        <tbody>
        <tr class="border-bottom border-right grey">
            <td colspan="1"><strong>Product</strong></td>
            <td colspan="1"><strong>Qty</strong></td>
            <td colspan="1"><strong>Price</strong></td>
            <td colspan="1"><strong>Sale Price</strong></td>
            <td colspan="1"><strong>Saving</strong></td>
            <td colspan="1"><strong>CGST</strong></td>
            <td colspan="1"><strong>SGST</strong></td>
            <td colspan="1"><strong>Cess</strong></td>
            <td colspan="1"><strong>Subtotal</strong></td>
        </tr>
        @php
        $cgst=0;$sgst=0;
        $subtotal=0;$grand_total=0;
        @endphp
        @foreach($orders->details as $product)
        <tr class="border-right">
            <td colspan="1">{{$product->entity->company}}--{{$product->name}}-{{$product->size->size??''}}</td>
            <td colspan="1">{{$product->quantity}}</td>
            <td colspan="1">{{$product->cut_price}}</td>
            <td colspan="1">Rs. {{$product->price}}</td>
            <td colspan="1">Rs. {{$product->cut_price - $product->price}}</td>
            <td colspan="1">Rs. {{round($product->price*($product->size->cgst??'0')/100,2)}}</td>
            <td colspan="1">Rs. {{round($product->price*($product->size->sgst??'0')/100,2)}}</td>
            <td colspan="1">Rs. 0.0</td>
            <td colspan="1">Rs. {{round(($product->price - $product->price*(($product->size->cgst??0)+($product->size->sgst??0))/100)*$product->quantity, 2)}}</td>
            @php
                $subtotal=$subtotal+($product->price - $product->price*(($product->size->cgst??0)+($product->size->sgst??0))/100)*$product->quantity;
                $cgst=$cgst+$product->price*($product->size->cgst??'0')/100*$product->quantity;
                $sgst=$sgst+$product->price*($product->size->sgst??'0')/100*$product->quantity;
                $grand_total=$grand_total+$product->price*$product->quantity;
            @endphp
        </tr>
        @endforeach
        </tbody>
    </table>

    <table width="100%" class="outline-table">
        <tbody>

        <tr class="border-right">
            <td rowspan="8" width="60%">{{$invoice->t_n_c??''}}</td>
            <td  style="padding-left: 20px;"><strong>SubTotal</strong></td>
            <td style="padding-right: 20px;">Rs. {{round($subtotal,2)}}</td>
        </tr>
        <tr class="border-right">
            <td  style="padding-left: 20px;"><strong>Coupon Discount</strong></td>
            <td style="padding-right: 20px;">Rs. {{$orders->coupon_discount}}</td>
        </tr>
        <tr class="border-bottom border-right">
            <td  style="padding-left: 20px;"><strong>Shipping Charge</strong></td>
            <td style="padding-right: 20px;">Rs. {{$orders->delivery_charge}}</td>
        </tr>
        <tr class="border-bottom border-right">
            <td  style="padding-left: 20px;"><strong>CGST</strong></td>
            <td style="padding-right: 20px;">Rs. {{round($cgst,2)}}</td>
        </tr>
        <tr class="border-bottom border-right">
            <td  style="padding-left: 20px;"><strong>SGST</strong></td>
            <td style="padding-right: 20px;">Rs. {{round($sgst,2)}}</td>
        </tr>
        <tr class="border-bottom border-right">
            <td  style="padding-left: 20px;"><strong>Cess</strong></td>
            <td style="padding-right: 20px;">Rs. 0.0</td>
        </tr>
        <tr class="border-bottom border-right">
            <td  style="padding-left: 20px;"><strong>Grand Total</strong></td>
            <td style="padding-right: 20px;">Rs. {{round($grand_total+ $orders->delivery_charge - $orders->coupon_discount,2)}}</td>
        </tr>
        <tr class="border-bottom border-right">
            <td  style="padding-left: 20px;"><strong>Total(round-off)</strong></td>
            <td style="padding-right: 20px;">Rs. {{round($subtotal + $cgst + $sgst+ $orders->delivery_charge - $orders->coupon_discount)}}</td>
        </tr>
        </tbody>
    </table>
{{--    <p>&nbsp;</p>--}}

    {{--<table width="100%">
        <tbody>
        <tr>
            <td width="50%">
                <div class="center-justified"><strong>To make a payment:</strong><br>
                    Your payment options<br>
                    <strong>ST Reg no:</strong> Your service tax number<br>
                    <strong>Service Category:</strong> Service tax category<br>
                    <strong>Service category code:</strong> Service tax code<br>
                </div>
            </td>
            <td width="50%">
                <div class="center-justified">
                    <strong>Address</strong><br>
                    Foo Baar<br>
                    Dubai<br>
                    Dubai Main Road<br>
                    Vivekanandar Street<br>
                </div>
            </td>
        </tr>
        </tbody>
    </table>--}}
</div>
</body>
</html>

