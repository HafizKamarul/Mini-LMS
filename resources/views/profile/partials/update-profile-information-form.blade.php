<section>
    <h2 class="h5">Profile Information</h2>
    <p class="text-muted small">Update your account profile information and email address.</p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-3">
        @csrf @method('patch')
        <div class="mb-3"><label class="form-label" for="name">Name</label><input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"></div>
        <div class="mb-3"><label class="form-label" for="email">Email</label><input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username"></div>
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="alert alert-warning small">Your email address is unverified. <button form="send-verification" class="btn btn-link p-0 align-baseline">Resend verification email</button></div>
        @endif
        <button class="btn btn-primary" type="submit">Save</button>
    </form>
</section>
