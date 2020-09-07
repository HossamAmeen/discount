@extends('back-end.layout.app')


@section('content')
<table class="table dataTable my-0" id="dataTable">
    <thead>
       
        <tr>
            {{-- <th>image</th> --}}
            <th>user name</th>
            <th>name</th>
            <th>phone</th>
            <th>email</th>
            <th>role</th>
            <th>user</th>
            <th>action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $key => $value)
            <tr>
                <td><img class="rounded-circle mr-2" width="30" height="30"
                        src="{{asset('assets/img/avatars/avatar1.jpeg')}}">{{$value->user_name}}</td>
             
                <td>{{$value->name}}</td>
                <td>{{$value->phone}}</td>
                <td>{{$value->email}}</td>
                <td>{{$value->role == 1 ? "admin" : "employer"}}</td>
                <td>{{$value->user->name??" not found"}}</td>
                <td>
                    @include('back-end.shared.buttons.delete')
                   
                </td>
                
            </tr>
        @endforeach
        
        
    </tbody>
    <tfoot>
        <tr>
            <th>user name</th>
            <th>name</th>
            <th>phone</th>
            <th>email</th>
            <th>role</th>
            <th>user</th>
            <th></th>
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
@endpush