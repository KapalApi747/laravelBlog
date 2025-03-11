@extends('layouts.backend')
@section('title', 'Dashboard Overzicht')

@section('breadcrumb')
    @include('layouts.partials.breadcrumbs')
@endsection

@section('cards')
    @include('layouts.partials.cards')
@endsection

@section('content')
    @yield('cards')
@endsection
