<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $f_name_fa = '';
    public string $l_name_fa = '';
    public string $user_name = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->f_name_fa = Auth::user()->profile->f_name_fa;
        $this->l_name_fa = Auth::user()->profile->l_name_fa;
        $this->user_name = Auth::user()->user_name;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('پروفایل')" :subheading="__('اطلاعات شخصی خود را بروز کنید.')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="f_name_fa" :label="__('نام')" type="text" required autofocus class:input="text-center"/>
                <flux:input wire:model="l_name_fa" :label="__('نام خانوادگی')" type="text" required autofocus class:input="text-center" />
            </div>

            <div>
                <div class="grid grid-cols-2 gap-4 items-end">
                    <flux:input wire:model="user_name" :label="__('نام کاربری')" type="text" required dir="ltr" class:input="text-center"/>
                    <flux:button variant="ghost" type="submit" class="cursor-pointer" data-test="update-profile-button">
                        {{ __('ذخیره تغییرات') }}
                    </flux:button>
                    <x-action-message class="me-3" on="profile-updated">
                        {{ __('Saved.') }}
                    </x-action-message>
                </div>
            </div>
        </form>

{{--        <livewire:settings.delete-user-form />--}}
    </x-settings.layout>
</section>
