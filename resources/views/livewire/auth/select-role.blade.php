<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')]
class extends Component {
    public $roles;

    public function mount(): void
    {
        $this->roles = Auth::user()->getAllRolesWithInstitutes();
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('انتخاب نقش کاربری')"
        :description="__('برای ورود نقشی را انتخاب کنید.')"
    />

    <flux:radio.group wire:model="shipping" label="Shipping" variant="cards" class="max-sm:flex-col">
        <flux:radio value="standard" label="Standard" description="4-10 business days" checked />
        <flux:radio value="fast" label="Fast" description="2-5 business days" />
        <flux:radio value="next-day" label="Next day" description="1 business day" />
    </flux:radio.group>


    @forelse($roles as $r)
        <button
            wire:click="selectRole({{ $r->role_id }}, {{ $r->institute_id ?? 'null' }})"
        >
            <strong>{{ $r->role_name }}</strong>  <!-- اینجا نمایش داده میشه -->
            <span>{{ $r->institute_name ?? 'بدون آموزشگاه' }}</span>  <!-- اینجا هم نمایش داده میشه -->
        </button>
    @empty
        <p>شما هیچ نقشی ندارید.</p>
    @endforelse

</div>
