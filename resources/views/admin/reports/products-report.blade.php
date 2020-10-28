<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Company</th>
        <th>Description</th>
        <th>Stock Type(packet/quantity)</th>
        <th>Product Stock(required if stock type is quantity)</th>
        <th>Is Offer(0/1)</th>
        <th>Product Active(0/1)</th>
        <th>Category</th>
        <th>Subcategory</th>
        <th>Size</th>
        <th>Price</th>
        <th>Cut Price</th>
        <th>Min Quantity</th>
        <th>Max. Quantity</th>
        <th>Consumed Unit</th>
        <th>Size Active(0/1)</th>
        <th>New Arrival(0/1)</th>
        <th>Hot Deal(0/1)</th>
        <th>Discounted(0/1)</th>
        <th>Image Identifier</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        @foreach($product->sizeprice as $size)
            <tr>
                <td>{{ $product->name??'.' }}</td>
                <td>{{ $product->company??'.' }}</td>
                <td>{{ $product->description??'.' }}</td>
                <td>{{ $product->stock_type??'packet' }}</td>
                <td>{{ $product->stock??0 }}</td>
                <td>{{ $product->is_offer??0 }}</td>
                <td>{{ $product->isactive??0 }}</td>
                <td>@foreach($product->category as $cat){{ $cat->name.',' }}@endif</td>
                <td>@foreach($product->subcategory as $cat){{ $cat->name.',' }}@endif</td>
                <td>{{ $size->size }}</td>
                <td>{{ $size->price??0 }}</td>
                <td>{{ $size->cut_price??0 }}</td>
                <td>{{ $size->min_qty }}</td>
                <td>{{ $size->max_qty }}</td>
                <td>{{ $size->consumed_units }}</td>
                <td>{{ $size->isactive }}</td>
                <td>{{ $product->is_newarrival }}</td>
                <td>{{ $product->is_hotdeal }}</td>
                <td>{{ $product->is_discounted }}</td>
                <td>.</td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
