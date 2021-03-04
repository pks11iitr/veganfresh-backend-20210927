<table>
    <thead>
    <tr>
        <th>OrderID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Amount</th>
        <th>Payment Mode</th>
        <th>Order Date</th>
        <th>Delivery Date</th>
        <th>Time</th>
        <th>Delivered At</th>
        <th>Status</th>
        <th>Store</th>
        <th>Rider</th>
        <th>Delivery Address</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $order)
        <tr>
            <td>{{ ($order->refid??'') }}</td>
            <td>{{ ($order->customer->name??'') }}</td>
            <td>{{ $order->customer->email??'' }}</td>
            <td>{{ $order->customer->mobile??'' }}</td>
            <td>{{ $order->total_cost+$order->delivery_charge }}</td>
            <td>{{ $order->payment_mode }}</td>
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
            <td>{{ isset($order->deliveryaddress)?implode(', ', $order->deliveryaddress->only('first_name', 'last_name', 'mobile_no', 'email', 'house_no', 'appertment_name', 'street', 'landmark', 'area', 'city', 'pincode')):''}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
