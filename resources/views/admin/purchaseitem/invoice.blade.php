<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Mrp</th>
        <th>Quantity</th>
        <th>Date</th>
        <th>Expiry</th>
        <th>Manufacturing</th>
        <th>Vendor</th>
        <th>Remarks</th>
    </tr>
    </thead>
    <tbody>
    @foreach($purchases as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->price }}</td>
            <td>{{ $user->mrp }}</td>
            <td>{{ $user->quantity }}</td>
            <td>{{ $user->create_date }}</td>
            <td>{{ $user->expiry }}</td>
            <td>{{ $user->manufacturer }}</td>
            <td>{{ $user->vendor }}</td>
            <td>{{ $user->remarks }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
