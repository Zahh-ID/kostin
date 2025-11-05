<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 mb-0 text-dark">{{ __('Profil Akun') }}</h1>
            <small class="text-muted">{{ __('Atur informasi dasar, kata sandi, dan kontrol akun Anda.') }}</small>
        </div>
    </x-slot>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="mb-4">
                    @livewire('profile.update-profile-information-form')
                </div>

                <div class="mb-4">
                    @livewire('profile.update-password-form')
                </div>

                <div class="mb-4">
                    @livewire('profile.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
