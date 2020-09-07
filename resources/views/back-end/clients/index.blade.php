@extends('back-end.layout.app')


@section('content')


<table class="table dataTable my-0" id="dataTable">
    <thead>

        <tr>
            <th>Name</th>
          
            <th>Email</th>
            <th>Phone</th>
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
          
            <td>{{$value->user->name??" not found"}}</td>
            <td>
                @include('back-end.shared.buttons.delete')

            </td>

        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Name</th>
          
            <th>Email</th>
            <th>Phone</th>
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
@endpush