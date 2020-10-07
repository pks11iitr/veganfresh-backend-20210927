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
            <td width="30%">
{{--                <img src="http://exotel.in/wp-content/uploads/2013/03/exotel.png">--}}
                <img style="height:150px;width: 150px;margin-left:250px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAbcAAAIACAYAAAABsCK7AAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5AoBCgE6YY3h1QAAS9NJREFUeNrt3XmcW1d9///XudLsY4/3PXa8xI6d1dlDQghkYQmktAQo65e2QIECyRdoC7RAKaX9QldomwIFylLoyo9CCy2QEAikCWQldhzH+26P7fHsq6R7fn98NLY88TIjXeleSe/n4zEZ2xlprmak+9bn3M85B0REREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREJCou7gMQkWfrvjs9/scASE3yZi3ACOAn8bUeyALMfEc27ocrEjmFm0gF5UMrBTTn/2kB0IGFzRxgLSe/LtcAS5hcYM0A+oHcJL42CzwB9BZ8vz5gY/7/OaALOJr/3iNAqCCUaqFwE4lYQdXVDkwHVgCNWHDNB+ZxIsQWAzOxAGkE2ojvdZnFwtHnj+Ew0AmEwAagJ///fwGMAbuwQOwHMgo+SRKFm0iR8iHmsKHDmcAyYDlwAVaRnQcswgIsjYVXupjvlSAhMJr/fAAYADZjVd4GYC+wBejGwjAEDX1K5SncRCYpH2YBVl0twaqvS4DzgZXAUizkgvxHPfFYkI0BR7Dg24QNfe7CAvAAMIyGN6UCFG4ip1EwvNiGVWTnA8/DAm0lMBtoivs4q8Awdu1uKzak+Qvg58A+rPLzCjuJmsJNJK8gzBww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7sWPDvpwbJhgpJ/ccJ8fHOmnc2yInZlRnsGzCdgM7ACOAsNx/9xExincpN41YdfkLgWuBi4DVmMLNjdRLa+R/ILBVtGduAZ2pg7Nwnl63lsY+hAfhgz7HD3AIWA3FmBb8x/7gCPYAte6WCaJVR0vXJHKacequPOxocsLgfOwTsyZVFPgnZ4HssAg0AN0AnuBPViA7cJC7SjQC4zFfcAiU1XtL1KRcktja1kuAM7Fqrrl+Y/FWINKBxZ6jcT/mvJADmvcGAX6sQA7BhwADnIiyA7lP7qBAdTsITUk7heiSLVqxqq8OVhjynxseHNW/t/mY5VeW/7r2oAWLCxT+Y+g4KPQ+OSAsOAjh1Vbo9i1rSGs8hoG+rChwi6s0jqCVWPd+b935792JH8fIjVP4SYSPYeFWBqr6FqwMByv7gr/PP514wE3Xnl5rJIay3+MTvjzSP4jw4ng0zUwEREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREREpr/8fUk3+G99Qk4UAAAAldEVYdGRhdGU6Y3JlYXRlADIwMjAtMTAtMDFUMTA6MDE6NTgrMDA6MDDOUQZuAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDIwLTEwLTAxVDEwOjAxOjU4KzAwOjAwvwy+0gAAAABJRU5ErkJggg==">
            </td>
        </tr>
        </tbody>
    </table>
    <table width="100%" class="outline-table" style="margin-bottom: 10px;">
        <tbody>
        <tr class="black">
            <td colspan="6" style="color: white"><strong>Order # {{$orders->refid}}</strong></td>
        </tr>
        <tr class="black">
            <td colspan="6" style="color: white"><strong>Order Date: {{date('D d ,Y', strtotime($orders->delivery_date))}} ({{$orders->booking_time}})</strong></td>
        </tr>
        </tbody>
    </table>
    <table width="100%" class="outline-table" style="margin-bottom: 10px;">
        <tbody>
        <tr class="border-bottom border-right grey">
            <td colspan="6"><strong>Sold to:</strong></td>
            <td colspan="6"><strong>Ship to:</strong></td>
        </tr>

        <tr class="border-right">
            <td colspan="6">{{$orders->deliveryaddress->first_name??''}} {{$orders->deliveryaddress->last_name??''}}<br><span>{{$orders->deliveryaddress->house_no??''}}, {{$orders->deliveryaddress->appertment_name??''}}</span><br><span>{{$orders->deliveryaddress->street??''}}, {{$orders->deliveryaddress->landmark??''}},</span><br><span>{{$orders->deliveryaddress->address_type??''}} , {{$orders->deliveryaddress->other_text??''}},</span><br><span>{{$orders->deliveryaddress->area??''}}, {{$orders->deliveryaddress->city??''}}, {{$orders->deliveryaddress->pincode??''}}</span></td>

            <td colspan="6">{{$orders->deliveryaddress->first_name??''}} {{$orders->deliveryaddress->last_name??''}}<br><span>{{$orders->deliveryaddress->house_no??''}}, {{$orders->deliveryaddress->appertment_name??''}}</span><br><span>{{$orders->deliveryaddress->street??''}}, {{$orders->deliveryaddress->landmark??''}},</span><br><span>{{$orders->deliveryaddress->address_type??''}} , {{$orders->deliveryaddress->other_text??''}},</span><br><span>{{$orders->deliveryaddress->area??''}}, {{$orders->deliveryaddress->city??''}}, {{$orders->deliveryaddress->pincode??''}}</span></td>

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
            <td colspan="3"><strong>Product</strong></td>
            <td colspan="3"><strong>SKU</strong></td>
            <td colspan="1"><strong>Price</strong></td>
            <td colspan="1"><strong>Sale Price</strong></td>
            <td colspan="1"><strong>Saving</strong></td>
            <td colspan="1"><strong>Qty</strong></td>
            <td colspan="1"><strong>Tax</strong></td>
            <td colspan="1"><strong>Subtotal</strong></td>
        </tr>
        @foreach($orders->details as $product)
        <tr class="border-right">
            <td colspan="3">{{$product->name}}</td>
            <td colspan="3">{{$product->size->size}}</td>
            <td colspan="1">Rs. {{$product->cut_price}}</td>
            <td colspan="1">Rs. {{$product->price}}</td>
            <td colspan="1">Rs. {{$product->cut_price - $product->price}}</td>
            <td colspan="1">{{$product->quantity}}</td>
            <td colspan="1">Rs. 0.00</td>
            <td colspan="1">Rs. {{$product->price * $product->quantity}}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    <table width="100%" class="outline-table">
        <tbody>

        <tr class="border-right">
            <td  style="padding-left: 450px;"><strong>SubTotal</strong></td>
            <td style="padding-right: 70px;">Rs. {{$orders->total_cost}}</td>
        </tr>
        <tr class="border-right">
            <td  style="padding-left: 450px;"><strong>Coupon Discount</strong></td>
            <td style="padding-right: 70px;">Rs. {{$orders->coupon_discount}}</td>
        </tr>
        <tr class="border-bottom border-right">
            <td  style="padding-left: 450px;"><strong>Shipping Charge</strong></td>
            <td style="padding-right: 70px;">Rs. {{$orders->delivery_charge}}</td>
        </tr>
        <tr class="border-bottom border-right">
            <td  style="padding-left: 450px;"><strong>Grand Total</strong></td>
            <td style="padding-right: 70px;">Rs. {{$orders->total_cost + $orders->delivery_charge - $orders->coupon_discount}}</td>
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

