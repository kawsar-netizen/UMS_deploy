@extends('master.master')

@section('breadcrumb')
    <div class="row wrapper border-bottom white-bg page-heading"
        style="background-color: #a3b0c2; color: white; font-family: serif;">
        <div class="col-lg-10">
            <h2><b align="center">System Domain</b></h2>
            <ol class="breadcrumb" style="background-color: #a3b0c2">
                <li class="breadcrumb-item">
                    <a href=""><b style="color: white">System Domain</b></a>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
@endsection

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">

            <div class="col-lg-5">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>System Domain</h5>
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

                        <form action="{{ route('systemDomainStore') }}" method="POST">
                            @csrf

                            <div class="form-group row">

                                <label for="system_id" class="col-lg-2 col-form-label"><b>System Id</b></label>

                                <div class="col-lg-10">

                                    <select class="form-control select2" required="" name="system_id" id="system_id">
                                        <option value="">--select--</option>
                                        @foreach ($systemData as $item)
                                            <option value="{{ $item->sys_id }}">{{ $item->sys_id }}</option>
                                        @endforeach
                                        @error('system_id')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </select>

                                </div>

                            </div>

                            <div class="form-group row">

                                <label for="system_name" class="col-lg-2 col-form-label"><b>System Name</b></label>

                                <div class="col-lg-10">
                                    <select class="form-control select2" required="" name="system_name" name="system_name">
                                        <option value="">--select--</option>
                                        @foreach ($systemData as $item)
                                            <option value="{{ $item->system_name }}">{{ $item->system_name }}</option>
                                        @endforeach
                                        @error('system_name')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </select>
                                </div>

                            </div>
                            <div class="form-group row">

                                <label for="domain_status" class="col-lg-2 col-form-label"><b>Domain Status</b></label>

                                <div class="col-lg-10">
                                    <select class="form-control select2" required="" name="domain_status" name="domain_status">
                                        <option value="">--select--</option>
                                        <option value="Domain">Domain</option>
                                        <option value="Non-domain">Non-domain</option>
                                       
                                        @error('domain_status')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </select>
                                </div>

                            </div>

                            <div class="form-group row">
                                <div class="offset-lg-2 col-lg-10">
                                    <input type="submit"class="btn btn-sm btn-success insert_btn" value="Add">
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>System Domain Table</h5>
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
                                    <th scope="col" style="color: black">System Id</th>
                                    <th scope="col" style="color: black">System Name</th>
                                    <th scope="col" style="color: black">System Domain</th>
                                    <th scope="col" style="color: black;width: 22%;">Action</th>
                                </tr>
                            </thead>


                            <tbody>
                                @foreach ($systemDomainGet as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->system_id }}</td>
                                        <td>{{ $item->system_name }}</td>

                                        <td>
                                            @if ($item->domain_status == 'Domain')
                                                <div class="mb-2 mr-2 badge badge-secondary">Domain</div>
                                            @else
                                                <div class="mb-2 mr-2 badge badge-warning">Non-domain</div>
                                            @endif
                                        </td>
                                        <td style="width: 22%;text-align: center;">
                                            <a href="{{ route('systemDomainedit', $item->id) }}">
                                                <button type="button" class="btn btn-info btn-sm">Edit</button>
                                            </a>

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
