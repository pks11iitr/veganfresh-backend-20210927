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
    @foreach($data as $d)
        <tr>
            <td>{{ $d->product->name??'' }}</td>
            <td>{{ $d->size??'' }}</td>
            <td>{{ $d->stock??'' }}</td>
            <td>{{ $d->price*$d->stock??'' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
