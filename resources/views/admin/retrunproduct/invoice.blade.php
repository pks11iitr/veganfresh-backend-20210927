<table>
    <thead>
    <tr>
        <th>OrderID</th>
        <th>Store</th>
        <th>Rider</th>
        <th>Item</th>
        <th>Size</th>
        <th>Cost</th>
        <th>Returned Quantity</th>
        <th>Reason</th>
        <th>Date Time</th>
    </tr>
    </thead>
    <tbody>
    @foreach($returnproducts as $products)
        <tr>
            <td>{{ $products->order->refid??'' }}</td>
            <td>{{ $products->storename->name??'' }}</td>
            <td>{{ $products->rider->name??'' }}</td>
            <td>{{ $products->name }}</td>
            <td>{{ $products->size->size??'' }}</td>
            <td>{{ $products->price }}</td>
            <td>{{ $products->quantity }}</td>
            <td>{{ $products->reason }}</td>
            <td>{{ $products->created_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
