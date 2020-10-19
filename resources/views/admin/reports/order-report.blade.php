<table>
    <thead>
    <tr>
        <th>OrderID</th>
        <th>Product Name</th>
        <th>Size</th>
        <th>Quantity</th>
        <th>Price/Unit</th>
        <th>Total Price</th>
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
        @foreach($order->details as $details)
        <tr>
            <td>{{ ($order->refid??'') }}</td>
            <td>{{ $details->entity->name??'' }}</td>
            <td>{{ $details->size->size??'' }}</td>
            <td>{{ $details->quantity??'' }}</td>
            <td>{{ $details->size->price??0 }}</td>
            <td>{{ ($details->size->price??0)*($details->quantity) }}</td>
            <td>{{ $order->delivery_date??'' }}</td>
            <td>{{ $order->timeslot->name??'' }}</td>
            <td>{{ $order->status??'' }}</td>
            <td>{{ $order->storename->name??'' }}</td>
            <td>{{ $order->rider->name??'' }}</td>
            <td>{{ isset($order->deliveryaddress)?implode(', ', $order->deliveryaddress->only('first_name', 'last_name', 'mobile_no', 'email', 'house_no', 'appertment_name', 'street', 'landmark', 'area', 'city', 'pincode')):'' }}</td>
        </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
