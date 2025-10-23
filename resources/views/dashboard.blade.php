@extends('layouts.app')

@section('header')
    <div class="container mt-3">
        <h2 class="h4">{{ __('Dashboard') }}</h2>
    </div>
@endsection

@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-body">
                {{ __("You're logged in!") }}
            </div>
        </div>
    </div>
@endsection
