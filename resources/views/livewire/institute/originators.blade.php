<?php

use App\Models\Institute;
use App\Models\InstituteRoleUser;
use App\Models\Profile;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new class extends Component {
    #[Locked]
    public Institute $institute;
    public $user_id;
    public string $n_code;
    public string $f_name_fa;
    public string $l_name_fa;
    public array $mobiles;
    public string $mobile = '';

    public function profile_existence(): void
    {
        $profile = Profile::where('n_code', $this->n_code)->first();
        if ($profile) {
            $this->user_id = $profile->user->id;
            $this->f_name_fa = $profile['f_name_fa'];
            $this->l_name_fa = $profile['l_name_fa'];
            foreach ($profile->user->mobiles as $mobile) {
                $this->mobiles[] = $mobile->mobile_nu;
            }
            $this->modal('create_originator')->close();
            $this->modal('create_originator_profile')->show();
        }
    }

    public function create_originator_role(): void
    {
        InstituteRoleUser::create([
            'institute_id' => $this->institute->id,
            'user_id' => $this->user_id,
            'role_id' => '2'
        ]);
    }


}; ?>

<div>
    <div class="bg-zinc-100 dark:bg-zinc-600 dark:text-zinc-300 py-3 relative">
        <p class="font-semibold text-center">{{__('لیست موسسان آموزشگاه ')}} {{$institute['short_name']}}</p>
        <section class="absolute left-1 top-2">
            <flux:tooltip content="آموزشگاهها" position="right">
                <flux:button href="{{route('institutes')}}" variant="ghost" size="sm" class="cursor-pointer"
                             wire:navigate>
                    <flux:icon.arrow-up-circle class="text-blue-500 size-6"/>
                </flux:button>
            </flux:tooltip>
        </section>

        <section class="absolute left-10 top-2">
            <flux:modal.trigger name="create_originator">
                <flux:tooltip content="درج موسس جدید" position="right">
                    <flux:button x-on:click.prevent="$dispatch('open-modal', 'create_originator')" variant="ghost"
                                 size="sm"
                                 class="cursor-pointer">
                        <flux:icon.plus-circle class="text-green-500"/>
                    </flux:button>
                </flux:tooltip>
            </flux:modal.trigger>

            <flux:modal name="create_originator" :show="$errors->isNotEmpty()" focusable class="w-80 md:w-96"
                        :dismissible="false">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('کد ملی موسس را وارد کنید') }}</flux:heading>
                        <flux:text class="mt-2">{{ __('اگر پروفایل موجود باشد، نمایش داده خواهد شد.') }}</flux:text>
                    </div>
                    <form wire:submit="profile_existence" class="flex flex-col gap-6" autocomplete="off">
                        <flux:input wire:model="n_code" :label="__('کدملی:')" type="text" class:input="text-center"
                                    maxlength="10" required autofocus style="direction:ltr"/>


                        <div class="flex justify-between space-x-2 rtl:space-x-reverse flex-row-reverse">
                            <flux:button variant="primary" color="violet" type="submit"
                                         class="cursor-pointer">{{ __('ادامه') }}</flux:button>
                            <flux:modal.close>
                                <flux:button variant="filled" class="cursor-pointer">{{ __('انصراف') }}</flux:button>
                            </flux:modal.close>
                        </div>
                    </form>
                </div>
            </flux:modal>
        </section>


        <flux:modal name="create_originator_profile" :show="$errors->isNotEmpty()" focusable class="w-80 md:w-96"
                    :dismissible="false">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('اطلاعات پروفایل بازیابی شد.') }}</flux:heading>
                    <flux:text class="mt-2">{{ __('امکان افزودن شماره موبایل وجود دارد.') }}</flux:text>
                </div>
                <form wire:submit="create_originator_role" class="flex flex-col gap-5" autocomplete="off">
                    <flux:input readonly wire:model="n_code" :label="__('کدملی:')" type="text" class:input="text-center"
                                maxlength="10" style="direction:ltr"/>
                    <flux:input readonly wire:model="f_name_fa" :label="__('نام:')" type="text"
                                class:input="text-center"
                                maxlength="30" required/>
                    <flux:input readonly wire:model="l_name_fa" :label="__('نام خانوادگی:')" type="text"
                                class:input="text-center"
                                maxlength="40" required/>
                    <flux:input wire:model="mobile" :label="__('شماره موبایل:')" type="text" class:input="text-center"
                                maxlength="11" autofocus/>
                    <flux:separator text="شماره های موجود"/>
                    <div class="flex justify-around">
                        @foreach($mobiles as $mobile)
                            <flux:badge variant="pill">{{$mobile}}</flux:badge>
                        @endforeach
                    </div>


                    <div class="flex justify-between space-x-2 rtl:space-x-reverse flex-row-reverse">
                        <flux:button variant="primary" color="green" type="submit"
                                     class="cursor-pointer">{{ __('ثبت') }}</flux:button>
                        <flux:modal.close>
                            <flux:button variant="filled" class="cursor-pointer">{{ __('انصراف') }}</flux:button>
                        </flux:modal.close>
                    </div>
                </form>
            </div>
        </flux:modal>
    </div>


</div>
