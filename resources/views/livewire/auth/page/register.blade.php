<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('ایجاد حساب کاربری')" :description="__('جهت ایجاد حساب، اطلاعات خواسته شده را وارد نمایید.')" />
        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />
        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <x-my.flt_lbl />
        </form>
    </div>
</div>
