<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $user_name = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->user_name = Auth::user()->user_name;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([

            'user_name' => [
                'required',
                'string',
                'max:30',
                Rule::unique(User::class)->ignore($user->id)
            ],
        ]);

        $user->fill($validated);
        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('نام کاربری')" :subheading="__('نام کاربری خود را ویرایش کنید.')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6" autocomplete="off">
            <div>
                <div class="grid grid-cols-2 gap-4 items-end">
                    <flux:input wire:model="user_name" :label="__('نام کاربری')" type="text" required dir="ltr" class:input="text-center" maxlength="30"/>
                    <div class="grid grid-cols-2 items-center">
                        <flux:button variant="ghost" type="submit" class="cursor-pointer" data-test="update-profile-button">
                            {{ __('ذخیره تغییرات') }}
                        </flux:button>
                        <x-action-message class="me-3 text-green-500" on="profile-updated">
                            {{ __('ذخیره شد.') }}
                        </x-action-message>

                    </div>

                </div>
            </div>
        </form>

{{--        <livewire:settings.delete-user-form />--}}
    </x-settings.layout>
</section>
