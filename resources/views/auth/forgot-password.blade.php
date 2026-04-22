<x-guest-layout>
    <h2 class="h5 mb-3">Forgot Password</h2>
    <p class="text-muted small">Enter your email and we will send a password reset link.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <button class="btn btn-primary w-100" type="submit">Email Password Reset Link</button>
    </form>
</x-guest-layout>
