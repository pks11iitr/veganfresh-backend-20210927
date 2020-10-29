<table>
    <thead>
    <tr>
        <th>OrderID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Amount</th>
        <th>Payment Mode</th>
        <th>Date</th>
        <th>Time</th>
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
            <td>{{ $order->delivery_date??'' }}</td>
            <td>{{ $order->timeslot->name??'' }}</td>
            <td>{{ $order->status??'' }}</td>
            <td>{{ $order->storename->name??'' }}</td>
            <td>{{ $order->rider->name??'' }}</td>
            <td>{{ isset($order->deliveryaddress)?implode(', ', $order->deliveryaddress->only('first_name', 'last_name', 'mobile_no', 'email', 'house_no', 'appertment_name', 'street', 'landmark', 'area', 'city', 'pincode')):''}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
