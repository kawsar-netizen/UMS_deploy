@extends('master.master')

@section('breadcrumb')
    <div class="row wrapper border-bottom white-bg page-heading"
        style="background-color: #a3b0c2; color: white; font-family: serif;">
        <div class="col-lg-10">
            <h2><b align="center">System Domain Edit</b></h2>
            <ol class="breadcrumb" style="background-color: #a3b0c2">
                <li class="breadcrumb-item">
                    <a href=""><b style="color: white">System Domain Edit</b></a>
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

            <div class="col-lg-10">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>System Domain Edit</h5>
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

                        <form action="{{route('systemDomainupdate',$systemDomainEdit->id)}}" method="POST">
                            @csrf

                            {{-- <div class="form-group row">

                                <label for="system_id" class="col-lg-2 col-form-label"><b>System Id</b></label>

                                <div class="col-lg-10">

                                    <select class="form-control select2" required="" name="system_id" id="system_id">
                                        <option value="">--select--</option>
                                        @foreach ($systemData as $item)
                                        <option value="{{$item->sys_id}}" {{ $item->sys_id == $systemDomainEdit->system_id ? 'selected': ''}}>{{$item->sys_id}}</option>
                                        @endforeach
                                        @error('system_id')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </select>

                                </div>

                            </div> --}}
                            
                            <div class="form-group row">

                                <label for="system_name" class="col-lg-2 col-form-label"><b>System Name</b></label>

                                <div class="col-lg-10">
                                    <select class="form-control select2" required="" name="system_name" id="system_name">
                                        <option value="">--select--</option>
                                        @foreach ($systemData as $item)
                                        <option value="{{$item->system_name}}" {{ $item->system_name == $systemDomainEdit->system_name ? 'selected': ''}}>{{$item->system_name}}</option>
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
                                        <option value="Domain" {{ $systemDomainEdit->domain_status == 'Domain' ? 'selected':'' }}>Domain</option>
                                        <option value="Non-domain" {{ $systemDomainEdit->domain_status == 'Non-domain' ? 'selected':'' }}>Non-domain</option>
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
                                    <input type="submit"class="btn btn-sm btn-success insert_btn" value="Update">
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
