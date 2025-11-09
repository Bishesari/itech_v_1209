<?php

use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')]
class extends Component {
    public $roles = [];
    public string $role_id = '';

    public function mount(): void
    {
        $this->roles = Auth::user()->getAllRolesWithInstitutes();
    }

    public function setRole($roleId, $instituteId): void
    {
        $this->role_id = $roleId;
        session([
            'active_role_id' => $roleId,
            'active_institute_id' => $instituteId ?? '',
        ]);
    }

    public function dashboard(): void
    {
        if (empty($this->role_id)) {
            $this->addError('role_id', 'لطفاً یک نقش انتخاب کنید.');
            return;
        }
        // ✅ همه‌چیز اوکیه، هدایت به داشبورد
        $this->redirectRoute('dashboard');
    }

}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('انتخاب نقش کاربری')"
        :description="__('برای ورود نقشی را انتخاب کنید.')"
    />

    <flux:radio.group variant="buttons" class="flex-col gap-y-4">
        @forelse($roles as $r)
            <flux:radio icon="light-bulb" class="cursor-pointer"
                        wire:click="setRole({{ $r->role_id }}, {{ $r->institute_id ?? 'null' }})">
                <strong>{{ $r->role_name }}</strong>
                <span>{{ ' - ' . $r->institute_name  ?? '' }}</span>

            </flux:radio>
        @empty
            <p>شما هیچ نقشی ندارید.</p>
        @endforelse
    </flux:radio.group>

    @error('role_id')
        <p class="text-red-500 text-sm text-center">{{ $message }}</p>
    @enderror

    <flux:button wire:click="dashboard" variant="primary" color="sky" class="cursor-pointer"
                 size="sm">{{__('ادامه با نقش انتخابی')}}</flux:button>

</div>
