<section>
    <h2 class="h5">Delete Account</h2>
    <p class="text-muted small">This permanently deletes your account and data.</p>

    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">Delete Account</button>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf @method('delete')
                    <div class="modal-header"><h5 class="modal-title">Confirm Account Deletion</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                    <div class="modal-body">
                        <p class="small text-muted">Enter your password to confirm deletion.</p>
                        <input id="password" name="password" type="password" class="form-control" placeholder="Password">
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-danger" type="submit">Delete Account</button></div>
                </form>
            </div>
        </div>
    </div>
</section>
