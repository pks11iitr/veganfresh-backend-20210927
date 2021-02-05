<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($purchases as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->price }}</td>
            <td>{{ $user->quantity }}</td>
            <td>{{ $user->create_date }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
