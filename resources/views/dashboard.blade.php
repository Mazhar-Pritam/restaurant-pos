@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<h1 class="h4 mb-3">Welcome, {{ auth()->user()->name }}</h1>

<div class="row g-3">
    @can('pos.use')
        <div class="col-md-3"><div class="card card-body">POS and billing</div></div>
    @endcan
    @can('orders.create')
        <div class="col-md-3"><div class="card card-body">Orders</div></div>
    @endcan
    @can('kitchen.view')
        <div class="col-md-3"><div class="card card-body">Kitchen display</div></div>
    @endcan
    @can('inventory.manage')
        <div class="col-md-3"><div class="card card-body">Inventory</div></div>
    @endcan
    @can('users.manage')
        <div class="col-md-3"><div class="card card-body">Users</div></div>
    @endcan
</div>
@endsection
