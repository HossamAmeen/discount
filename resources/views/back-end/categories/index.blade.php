@extends('back-end.layout.app')
@section('search')

<form action="{{route($routeName.'.index')}}" >
    <div class="text-md-right dataTables_filter" id="dataTable_filter"><label><input
                type="search" class="form-control form-control-sm"
                aria-controls="dataTable" placeholder="Search" name="search" value="{{request('search')?? ''}}" required></label>
                <button type="submit" rel="tooltip" title="" class="btn-sm btn-info" style="display:inline-block;">
                    <i class="fas fa-search"></i>  
            </button>
    </div>
</form>
@endsection
@section('above-table')
<form method="POST" action="{{ route($routeName.'.store') }}"  enctype="multipart/form-data">
    @csrf
    <div class="form-row">
        <div class="col-xs-4">
            @php $inputName = 'name' ; @endphp
          <label ><strong>{{$inputName}}</strong></label>
          <input class="form-control" id="ex1" type="text" name="{{$inputName}}" value="{{Request::old($inputName) ?? " "}}" required>
        </div>
    </div>
    <br>
    <div class="form-group"><button class="btn btn-primary btn-sm"
        type="submit">save </button></div>
</form>
@endsection
@section('content')


<table class="table dataTable my-0" id="dataTable">
    <thead>

        <tr>
            <th>Name</th>
            <th>User</th>
            <th>action</th>
           
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $key => $value)
        <tr>
            <td><img class="rounded-circle mr-2" width="30" height="30"
                    src="{{asset('assets/img/avatars/avatar1.jpeg')}}">{{$value->name }}</td>
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
            <th>User</th>
          
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