@extends('master.master')

@section('breadcrumb')
    <div class="row wrapper border-bottom white-bg page-heading"
        style="background-color: #a3b0c2; color: white; font-family: serif;">
        <div class="col-lg-10">
            <h2><b align="center">UBS User Unlock</b></h2>
            <ol class="breadcrumb" style="background-color: #a3b0c2">
                <li class="breadcrumb-item">
                    <a href=""><b style="color: white">UBS User Unlock</b></a>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
@endsection

@section('content')
    @if (Session::get('message'))
        <script>
            alert('{{ Session::get('message') }}')
        </script>
    @endif
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">

            <div class="col-lg-6">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>UBS User Unlock</h5>
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

                        <form action="{{ route('UBSunlockStore') }}" method="POST">
                            @csrf
                            <div class="form-group row">

                                <label for="" class="col-lg-2 col-form-label"><b> UBS Id</b></label>
                                <div class="col-lg-10">
                                    <input type="text" class="form-control" name="req_name" readonly
                                        value="{{ $name }}">
                                    @error('')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                @if($name != "")
                                <div class="offset-lg-2 col-lg-10">
                                    <input type="submit"class="btn btn-sm btn-success insert_btn" value="Unlock Request">
                                </div>
                                @elseif($name == "")
                                <font color='red' size='5pt'>*** Please map the UBS ID ***</font>
                                @endif
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
