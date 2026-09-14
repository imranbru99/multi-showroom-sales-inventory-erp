@extends('admin.layouts.master')

@section('title')
    <title>{{ $title }}</title>
@endsection

@section('custom_css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('public/admin-elite/dist/css/dashboard.css') }}" rel="stylesheet" />
@endsection

@section('content')
 





@endsection

