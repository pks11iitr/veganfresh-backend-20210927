<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Isactive</th>
        <th>storeID</th>
    </tr>
    </thead>
    <tbody>
    @foreach($areas as $area)
        <tr>
            <td>{{ $area->name??'' }}</td>
            <td>{{ $area->isactive??'' }}</td>
            <td>{{ $area->store_id??'' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
