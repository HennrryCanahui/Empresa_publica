@extends('layouts.guest')

@section('content')
    <div class="mb-3 text-muted">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password">
            @if($errors->has('password'))
                <div class="invalid-feedback d-block">{{ $errors->first('password') }}</div>
            @endif
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">{{ __('Confirm') }}</button>
        </div>
    </form>
@endsection
