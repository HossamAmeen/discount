@extends('back-end.layout.app')


@section('content')

<table class="table dataTable my-0" id="dataTable">
    <thead>

        <tr>
            <th>Name</th>

            <th>Email</th>
            <th>Phone</th>
            <th>status</th>
            <th>user</th>
            <th>action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $key => $value)
        <tr>
            <td><img class="rounded-circle mr-2" width="30" height="30"
                    src="{{asset('assets/img/avatars/avatar1.jpeg')}}">{{$value->first_name. $value->last_name }}</td>

            <td>{{$value->email}}</td>
            <td>{{$value->phone}}</td>
            <td id="{{$value->id . 'status'}}">{{$value->status}}</td>
            <td>{{$value->user->name??" not found"}}</td>
            <td>
                <form action="{{ route($routeName.'.destroy' ,$value ) }}" method="post">
                    {{ csrf_field() }}
                    {{ method_field('delete') }}
                    <a href="#" class="btn-sm btn-info" onclick="acceptClient( 'accept',{{$value->id}})"
                        style="display:inline-block;">{{-- <i class="far fa-edit f044"></i> --}} accept</a>
                    <a href="#" class="btn-sm btn-danger" onclick="acceptClient( 'blocked',{{$value->id}})"
                        style="display:inline-block;">{{-- <i class="far fa-edit f044"></i> --}} block</a>
                        <a href="{{url('admin/orders/'.$value->id.'?type=clients')}}" class="btn-sm btn-info" onclick="acceptClient( 'accept',{{$value->id}})"
                            style="display:inline-block;">{{-- <i class="far fa-edit f044"></i> --}} orders</a>
                    <button type="submit" rel="tooltip" title="" class="btn-sm btn-danger" onclick="check()"
                        style="display:inline-block;">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>

            </td>

        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Name</th>

            <th>Email</th>
            <th>Phone</th>
            <th>status</th>
            <th>user</th>
            <th>action</th>
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