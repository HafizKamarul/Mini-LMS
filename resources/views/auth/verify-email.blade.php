<x-guest-layout>
    <h2 class="h5 mb-3">Verify Email</h2>
    <p class="text-muted small">Thanks for signing up. Please verify your email address before continuing.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">A new verification link has been sent to your email address.</div>
    @endif

    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-fill">
            @csrf
            <button class="btn btn-primary w-100" type="submit">Resend Verification Email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="flex-fill">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100">Log Out</button>
        </form>
    </div>
</x-guest-layout>
