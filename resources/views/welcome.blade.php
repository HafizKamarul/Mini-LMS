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
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" name="password" type="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Sign In</button>
    </form>

    <div class="d-flex justify-content-between mt-3">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
        @endif
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="small">Create account</a>
        @endif
    </div>
</x-guest-layout>
