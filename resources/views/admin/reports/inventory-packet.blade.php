<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Size</th>
        <th>Quantity</th>
        <th>Cost</th>
    </tr>
    </thead>
    <tbody>
    @foreach($areas as $area)
        <tr>
            <td>{{ $data->product->name??'' }}</td>
            <td>{{ $data->size??'' }}</td>
            <td>{{ $data->stock??'' }}</td>
            <td>{{ $data->price*$data->stock??'' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
