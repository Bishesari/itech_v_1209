<?php

use App\Models\Institute;
use App\Models\Profile;
use App\Models\Role;
use App\Rules\NCode;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {

    public string $n_code = '';
    public ?Profile $profile = null;
    public string $f_name_fa = '';
    public string $l_name_fa = '';
    public array $mobiles = [];

    public function open_create_user_modal(): void
    {
        $this->n_code = '';
        $this->modal('create-user')->show();

    }

    public function profile_existence(): void
    {
        $this->validate([
            'n_code' => ['required', 'digits:10', new NCode],
        ]);
        $profile = Profile::where('n_code', $this->n_code)->first();
        if ($profile) {
            $this->profile = $profile;
            $this->f_name_fa = $profile->f_name_fa ?? '';
            $this->l_name_fa = $profile->l_name_fa ?? '';
            $this->mobiles = $profile->user->contacts->pluck('mobile_nu')->toArray();
        }

        $this->modal('create-user')->close();
        $this->modal('create-user-profile')->show();
    }

    public $roleId = null;
    #[Computed]
    public function shouldShowInstitute()
    {
        if (!$this->roleId) return true;

        $role = Role::find($this->roleId);

        return !in_array($role->name_en, ['SuperAdmin', 'Newbie']);
    }

}; ?>

<div>
    {{--    Create User Modal   --}}
    <flux:button variant="primary" color="sky" size="sm" class="cursor-pointer"
                 wire:click="open_create_user_modal">{{__('جدید')}}</flux:button>
    <flux:modal name="create-user" :show="$errors->isNotEmpty()" focusable class="w-80 md:w-96"
                :dismissible="false">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('کد ملی را وارد کنید') }}</flux:heading>
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

    <flux:modal name="create-user-profile" :show="$errors->isNotEmpty()" focusable class="w-80 md:w-96"
                :dismissible="false">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('تکمیل اطلاعات کاربر جدید') }}</flux:heading>
                <flux:text class="mt-2">{{ __('نقش کاربر را در آموزشگاه مشخص کنید.') }}</flux:text>
            </div>
            <form wire:submit="create_role" class="flex flex-col gap-5" autocomplete="off">
                <flux:input readonly wire:model="n_code" :label="__('کدملی:')" type="text" class:input="text-center"
                            maxlength="10" style="direction:ltr"/>

                <flux:select wire:model.live="roleId" variant="listbox" :label="__('انتخاب نقش:')" placeholder="یک نقش انتخاب کنید..." searchable>
                    @foreach (Role::orderBy('name_fa')->get() as $role)
                        <flux:select.option value="{{ $role->id }}" wire:key="{{ $role->id }}">
                            {{ $role->name_fa }}
                        </flux:select.option>
                    @endforeach
                </flux:select>

                @if ($this->shouldShowInstitute)
                    <flux:select wire:model="instituteId" variant="listbox" :label="__('انتخاب آموزشگاه:')" placeholder="یک آموزشگاه انتخاب کنید..." searchable>
                        @foreach (Institute::orderBy('short_name')->get() as $institute)
                            <flux:select.option value="{{ $institute->id }}" wire:key="{{ $institute->id }}">
                                {{ $institute->short_name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                @endif


                <flux:input wire:model="mobile_nu" :label="__('شماره موبایل:')" type="text"
                            class:input="text-center"
                            maxlength="11"/>

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
