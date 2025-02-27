@extends('layouts.admin.app')

@section('title', "Dashboard")

@push('css_or_js')
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="stylesheet" src="{{asset('assets/admin')}}/vendor/apex/apexcharts.css"></link>
@endpush

@section('content')
<div class="content container-fluid">
    <div>
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title c1">Analisis & Statistik Usaha</h1>
            </div>
        </div>
    </div>
@endsection