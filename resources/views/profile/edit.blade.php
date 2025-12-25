@extends('layouts.user')

@section('title', 'Edit Profil')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-3">
                <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Profil</h1>
                    <p class="text-gray-600 mt-1">Kelola informasi profil Anda</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Profile Information Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informasi Profil
                    </h2>
                </div>
                <div class="p-6 md:p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Password Update Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        Ubah Password
                    </h2>
                </div>
                <div class="p-6 md:p-8">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account Card - Less Prominent -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                <div class="p-6 md:p-8">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
