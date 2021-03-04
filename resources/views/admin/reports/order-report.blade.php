<table>
    <thead>
    <tr>
        <th>OrderID</th>
        <th>Product Name</th>
        <th>Size</th>
        <th>Quantity</th>
        <th>Price/Unit</th>
        <th>Total Price</th>
        <th>Order Date</th>
        <th>Delivery Date</th>
        <th>Delivery Time</th>
        <th>Delivered Time</th>
        <th>Status</th>
        <th>Store</th>
        <th>Rider</th>
        <th>Delivery Address</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $order)
        @foreach($order->details as $details)
        <tr>
            <td>{{ ($order->refid??'') }}</td>
            <td>{{ $details->entity->name??'' }}</td>
            <td>{{ $details->size->size??'' }}</td>
            <td>{{ $details->quantity??'' }}</td>
            <td>{{ $details->size->price??0 }}</td>
            <td>{{ ($details->size->price??0)*($details->quantity) }}</td>
            <td>{{ date('d/m/Y h:ia', strtotime($order->created_at??'')) }}</td>
            <td>@if($order->is_express_delivery)
                    Express Delivery
                @else
                    {{date('d/m/Y', strtotime($order->delivery_date))}}
                @endif
            </td>
            <td>@if($order->is_express_delivery)
                    Express Delivery
                @else
                    {{ $order->timeslot->name??'' }}
                @endif
            </td>
            <td>@if($order->delivered_at){{date('d/m/Y h:ia', strtotime($order->delivered_at))}}</td>
            <td>{{ $order->status??'' }}</td>
            <td>{{ $order->storename->name??'' }}</td>
            <td>{{ $order->rider->name??'' }}</td>
            <td>{{ isset($order->deliveryaddress)?implode(', ', $order->deliveryaddress->only('first_name', 'last_name', 'mobile_no', 'email', 'house_no', 'appertment_name', 'street', 'landmark', 'area', 'city', 'pincode')):'' }}</td>
        </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
