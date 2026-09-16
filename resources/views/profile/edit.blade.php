@extends('layouts.user')

@section('title', 'Edit Profil')

@section('content')
<div class="mb-6 sm:mb-8">
    <h1 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-news-ink">Edit Profil</h1>
    <p class="text-news-muted mt-1">Kelola informasi akun Anda</p>
</div>

<div class="max-w-3xl space-y-5">
    <div class="bg-white border border-news-line p-4 sm:p-6">
        @include('profile.partials.update-profile-information-form')
    </div>

    <div class="bg-white border border-news-line p-4 sm:p-6">
        @include('profile.partials.update-password-form')
    </div>

    <div class="bg-white border border-news-line border-l-4 border-l-news-accent p-4 sm:p-6">
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
