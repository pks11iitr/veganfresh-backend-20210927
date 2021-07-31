<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Isactive</th>
        <th>storeID</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $d)
        <tr>
            <td>{{ $d->name??'' }}</td>
            <td>{{ $d->isactive??'' }}</td>
            <td>{{ $d->store->name??'' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
