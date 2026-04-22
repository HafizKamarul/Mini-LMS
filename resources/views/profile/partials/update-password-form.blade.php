<section>
    <h2 class="h5">Update Password</h2>
    <p class="text-muted small">Use a long, random password to stay secure.</p>

    <form method="post" action="{{ route('password.update') }}" class="mt-3">
        @csrf @method('put')
        <div class="mb-3"><label class="form-label" for="update_password_current_password">Current Password</label><input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password"></div>
        <div class="mb-3"><label class="form-label" for="update_password_password">New Password</label><input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password"></div>
        <div class="mb-3"><label class="form-label" for="update_password_password_confirmation">Confirm Password</label><input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password"></div>
        <button class="btn btn-primary" type="submit">Save</button>
    </form>
</section>
