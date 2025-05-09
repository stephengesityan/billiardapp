@extends('layouts.app')

@section('content')
<div class="py-12 animated-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-gradient-to-r from-indigo-600 to-blue-500 text-white p-3">
                        <h2 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-user-cog me-2"></i>{{ __('Account Settings') }}
                        </h2>
                    </div>
                    <div class="card-body p-4">

                        @if (session('message'))
                            <div class="alert alert-success d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
                                <div class="me-2">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    {{ session('message') }}
                                </div>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
                                <div class="me-2">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    {{ session('success') }}
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('account.update') }}">
                            @csrf
                            @method('PUT')

                            <!-- Name -->
                            <div class="mb-4">
                                <label for="name" class="form-label fw-medium">
                                    <i class="fas fa-user text-primary me-1"></i>{{ __('Name') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-user-edit"></i>
                                    </span>
                                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                                        required autofocus class="form-control border-start-0"
                                        placeholder="Enter your name">
                                </div>
                                @error('name')
                                    <div class="text-danger mt-1 small"><i
                                            class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label fw-medium">
                                    <i class="fas fa-envelope text-primary me-1"></i>{{ __('Email') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-at"></i>
                                    </span>
                                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                                        required class="form-control border-start-0" placeholder="Enter your email">
                                </div>
                                @error('email')
                                    <div class="text-danger mt-1 small"><i
                                            class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                                @if ($user->email)
                                    <div class="mt-2 small d-flex align-items-center">
                                        @if ($user->hasVerifiedEmail())
                                            <span class="text-success d-flex align-items-center">
                                                <i class="fas fa-check-circle me-1"></i> {{ __('Email verified') }}
                                            </span>
                                        @else
                                            <span class="text-warning d-flex align-items-center">
                                                <i class="fas fa-exclamation-circle me-1"></i> {{ __('Not Verified') }}
                                            </span>
                                            <a href="{{ route('verification.resend') }}"
                                                class="ms-2 text-primary text-decoration-none">
                                                {{ __('Resend verification email') }}
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="card mt-4 mb-4 shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h3 class="mb-0 fs-5 fw-semibold text-primary">
                                        <i class="fas fa-lock me-2"></i>{{ __('Change Password') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <!-- Current Password -->
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label fw-medium">
                                            <i class="fas fa-key text-primary me-1"></i>{{ __('Current Password') }}
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input id="current_password" type="password" name="current_password"
                                                class="form-control border-start-0"
                                                placeholder="Enter current password">
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="toggleCurrentPassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('current_password')
                                            <div class="text-danger mt-1 small"><i
                                                    class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- New Password -->
                                    <div class="mb-3">
                                        <label for="password" class="form-label fw-medium">
                                            <i class="fas fa-lock-open text-primary me-1"></i>{{ __('New Password') }}
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-key"></i>
                                            </span>
                                            <input id="password" type="password" name="password"
                                                class="form-control border-start-0" placeholder="Enter new password">
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="toggleNewPassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="text-danger mt-1 small"><i 
                                                class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Confirm New Password -->
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label fw-medium">
                                            <i class="fas fa-check-double text-primary me-1"></i>{{ __('Confirm New Password') }}
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-key"></i>
                                            </span>
                                            <input id="password_confirmation" type="password" name="password_confirmation"
                                                class="form-control border-start-0" placeholder="Confirm new password">
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="toggleConfirmPassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit"
                                    class="btn btn-primary-custom d-flex align-items-center">
                                    <i class="fas fa-save me-2"></i>{{ __('Update Account') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in-down {
        0% {
            opacity: 0;
            transform: translateY(-10px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-down {
        animation: fade-in-down 0.5s ease-out;
    }
</style>

<script>
    // Toggle password visibility
    document.addEventListener('DOMContentLoaded', function() {
        const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
        const toggleNewPassword = document.getElementById('toggleNewPassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        
        const currentPassword = document.getElementById('current_password');
        const newPassword = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        
        if(toggleCurrentPassword) {
            toggleCurrentPassword.addEventListener('click', function() {
                togglePasswordVisibility(currentPassword, this);
            });
        }
        
        if(toggleNewPassword) {
            toggleNewPassword.addEventListener('click', function() {
                togglePasswordVisibility(newPassword, this);
            });
        }
        
        if(toggleConfirmPassword) {
            toggleConfirmPassword.addEventListener('click', function() {
                togglePasswordVisibility(confirmPassword, this);
            });
        }
        
        function togglePasswordVisibility(input, button) {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            // Toggle icon
            const icon = button.querySelector('i');
            if (type === 'text') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    });
</script>
@endsection