<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.destroy') }}" onsubmit="return confirm('{{ __('Are you sure you want to delete your account? This action cannot be undone.') }}')">
        @csrf
        @method('delete')

        <p class="mb-3 small text-muted">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>

        <div class="mb-3">
            <label for="password" class="form-label visually-hidden">{{ __('Password') }}</label>
            <input id="password" name="password" type="password" class="form-control" placeholder="{{ __('Password') }}" required>
            @if($errors->userDeletion->has('password'))
                <div class="invalid-feedback d-block">{{ $errors->userDeletion->first('password') }}</div>
            @endif
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('profile.edit') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
        </div>
    </form>
</section>
