@extends('layouts.app')
@section('title', 'Home')
@section('content')
  <div class="container-fluid">
    <div class="alert alert-primary mt-5" role="alert">
      Selamat datang, {{ \Illuminate\Support\Facades\Auth::user()->name }}
    </div>
  </div>
@stop
