@if(auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isPenulis())
    @extends('layouts.admin-simple')
    @section('page-title', 'Edit Profil')
    @section('page-subtitle', 'Kelola informasi profil Anda')
@else
    @extends('layouts.user')
@endif

@section('title', 'Edit Profil')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Profil</h1>
        <p class="text-gray-600">Kelola informasi profil Anda</p>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
