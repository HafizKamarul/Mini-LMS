<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Profile</h1></x-slot>

    <div class="row g-3">
        <div class="col-lg-4"><div class="card shadow-sm"><div class="card-body">@include('profile.partials.update-profile-information-form')</div></div></div>
        <div class="col-lg-4"><div class="card shadow-sm"><div class="card-body">@include('profile.partials.update-password-form')</div></div></div>
        <div class="col-lg-4"><div class="card shadow-sm"><div class="card-body">@include('profile.partials.delete-user-form')</div></div></div>
    </div>
</x-app-layout>
