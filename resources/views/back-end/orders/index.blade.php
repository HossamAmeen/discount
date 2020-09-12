@extends('back-end.layout.app')


@section('content')

<table class="table dataTable my-0" id="dataTable">
    <thead>
        {{-- 'price','discount','is_vip', 'status', 'quantity','product_id', 'client_id','cart_id' --}}
        <tr>
            <th>Client Name</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Discount</th>
            <th>Price</th>
            <th>is vip</th>
            <th>status</th>
           
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $key => $value)
        <tr>
            <td>{{$value->client->first_name ??" not found" . $value->client->last_name ??" not found" }}</td>
            <td>{{$value->product->name}}</td>
            <td>{{$value->quantity}}</td>
            <td>{{$value->discount}}</td>
            <td>{{$value->price}}</td>
            <td>{{$value->is_vip ? "vip" : "free"}}</td>
            <td id="{{$value->id . 'status'}}">{{$value->status}}</td>
            
            {{-- <td>
                <form action="{{ route($routeName.'.destroy' ,$value ) }}" method="post">
                    {{ csrf_field() }}
                    {{ method_field('delete') }}
                    <a href="#" class="btn-sm btn-info" onclick="acceptClient( 'accept',{{$value->id}})"
                        style="display:inline-block;">
                       block</a>
                    <button type="submit" rel="tooltip" title="" class="btn-sm btn-danger" onclick="check()"
                        style="display:inline-block;">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>

            </td>  --}}

        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Client Name</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Discount</th>
            <th>Price</th>
            <th>is vip</th>
            <th>status</th>
        </tr>
    </tfoot>
</table>

@endsection
@push('js')
<script type="text/javascript">
    $(document).ready(function(){
            $('#{{$routeName}}').addClass('active');
        });
</script>
<script>
    function acceptClient(status,clientId)
    {
        $.ajax({
                url:"{{url('admin/accept-client')}}"+'/'+status+'/'+clientId,
                type:"get",
               
                contentType: false,
                processData: false,
                success:function(dataBack)
                {

                    console.log("success");
                    // document.getElementById("addForm").reset();
                    document.getElementById(clientId+'status').innerText=status;
                    // showSelectReasonExchange('reasonExchange',  document.getElementById("in_or_out")) ;
                    
                    // $(".cont-data").prepend(dataBack)
                    

                }, error: function (xhr, status, error)
                {

                    console.log("errror " + xhr.responseJSON.errors);
                    $.each(xhr.responseJSON.errors,function(key,item)
                    {

                        // $("#error").html("<li class='alert alert-danger text-center p-1'>"+ item +" </li>");
                    })
                }
            })
    }
     
</script>
@endpush