<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Mobile</th>
        <th>Email</th>
        <th>DOB</th>
        <th>Membership</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $customer)
        <tr>
            <td>{{ $customer->id??'' }}</td>
            <td>{{ $customer->name??'' }}</td>
            <td>{{ $customer->mobile??'' }}</td>
            <td>{{ $customer->email??'' }}</td>
            <td>{{ $customer->dob??'' }}</td>
            <td>@if($customer->isMembershipActive()){{$customer->membership->name??'--'}}@endif</td>
        </tr>
    @endforeach
    </tbody>
</table>
