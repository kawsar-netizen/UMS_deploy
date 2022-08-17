@extends('master.master')

@section('breadcrumb')
    <div class="row wrapper border-bottom white-bg page-heading"
        style="background-color: #a3b0c2; color: white; font-family: serif;">
        <div class="col-lg-10">
            <h2><b align="center">Authorize List</b></h2>
            <ol class="breadcrumb" style="background-color: #a3b0c2">
                <li class="breadcrumb-item">
                    <a href=""><b style="color: white">Authorize List</b></a>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
@endsection

@section('content')
@if (Session::get('authorize'))
<script>
    alert('{{ Session::get('authorize') }}')
</script>
@endif
@if (Session::get('decline'))
<script>
    alert('{{ Session::get('decline') }}')
</script>
@endif
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Authorize List Table</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="#" class="dropdown-item">Config option 1</a>
                                </li>
                                <li><a href="#" class="dropdown-item">Config option 2</a>
                                </li>
                            </ul>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>

                    <div class="ibox-content">

                        <table id="myTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col" style="color: black">Sl</th>
                                    <th scope="col" style="color: black">Request Name</th>
                                    <th scope="col" style="color: black">Status</th>
                                    <th scope="col" style="color: black">Branch Code</th>
                                    <th scope="col" style="color: black">Maker User Name</th>
                                    <th scope="col" style="color: black">Entry Date</th>
                                    <th scope="col" style="color: black;width: 22%; text-align:center;">Action</th>
                                </tr>
                            </thead>


                            <tbody>
                                @foreach ($authorize_get_data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->req_name }}</td>
                                        <td>
                                            @if ($item->status == '0')
                                                <div class="mb-2 mr-2 badge badge-warning">Initiate</div>
                                            @elseif($item->status == '1')
                                                <div class="mb-2 mr-2 badge badge-info">Authorize</div>
                                            @else
                                                <div class="mb-2 mr-2 badge badge-danger">Declined</div>
                                            @endif
                                        </td>
                                        <td>{{ $item->br_code }}</td>
                                        <td>
                                            @php
                                                $user = DB::table('users')
                                                    ->where('id', '=', $item->maker_user_id)
                                                    ->first();
                                            @endphp
                                            {{ $user->name }}
                                        </td>
                                        <td>{{ $item->entry_date }}</td>
                                        <td style="width: 22%;text-align: center;">

                                                <form action="{{ route('changeStatus_authorize', $item->id) }}" method="POST">
                                                    {{ csrf_field() }}
                                                    <button type="submit" class="btn btn-primary btn-sm" name="status"
                                                        value="1" style="margin-bottom: 5px;">Authorize</button>
                                                </form>
                                                {{-- <br> --}}
                                                <form action="{{ route('changeStatus_decline', $item->id) }}" method="POST">
                                                    {{ csrf_field() }}
                                                    <button type="submit" class="btn btn-danger btn-sm" name="status"
                                                        value="2">Declined</button>
                                                </form>
                                       
                                            {{-- <a href="">
                                                <button type="button" class="btn btn-primary btn-sm">Authorize</button>
                                            </a>
                                            <a href="">
                                                <button type="button" class="btn btn-danger btn-sm">Declined</button>
                                            </a> --}}

                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


        </div>
    </div>
@endsection
