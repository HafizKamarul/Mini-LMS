<x-guest-layout>
    <h2 class="h4 mb-3 text-center">Login to Mini-LMS</h2>

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

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required autofocus autocomplete="username">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" name="password" type="password" class="form-control" required autocomplete="current-password">
        </div>

        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label for="remember_me" class="form-check-label">Remember me</label>
        </div>

        <button class="btn btn-primary w-100" type="submit">Log In</button>
    </form>

    <div class="d-flex justify-content-between mt-3">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="small">Forgot your password?</a>
        @endif
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="small">Register</a>
        @endif
    </div>
</x-guest-layout>
