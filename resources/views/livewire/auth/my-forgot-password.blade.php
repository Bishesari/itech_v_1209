<?php

use App\Jobs\SendOtp;
use App\Jobs\SendResetPass;
use App\Models\OtpLog;
use App\Models\Profile;
use App\Services\ParsGreenService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')]
class extends Component {
    public int $step = 1;
    public string $n_code = '';
    public string $mobile_nu = '';
    public array $mobiles = [];
    public string $u_otp = '';
    public int $timer = 0;
    public string $otp_log_check_err = '';

    protected function rules(): array
    {
        return [
            'n_code' => ['required'],
            'mobile_nu' => ['required'],
            'u_otp' => ['required', 'digits:6']
        ];
    }

    public function check_n_code(): void
    {
        $this->validateOnly('n_code');
        $profile = Profile::where('n_code', $this->n_code)->first();
        if (!$profile) {
            $this->addError('n_code', 'کد ملی یافت نشد.');
            return;
        }
        $user = $profile->user;
        $this->mobiles = $user->contacts->pluck('mobile_nu')->toArray();
        if (empty($this->mobiles)) {
            $this->addError('n_code', 'هیچ شماره موبایلی برای این کد ملی ثبت نشده است.');
            return;
        }
        if (count($this->mobiles) == 1) {
            $this->mobile_nu = $this->mobiles[0];
        }
        $this->log_check();
        $this->u_otp = '';
        $this->step = 2;
    }

    public function otp_send(): void
    {
        $this->validateOnly('mobile_nu');
        if ($this->log_check()) {
            $otp = NumericOTP();
            SendOtp::dispatch($this->mobile_nu, $otp);
            OtpLog::create([
                'ip' => request()->ip(),
                'n_code' => $this->n_code,
                'mobile_nu' => $this->mobile_nu,
                'otp' => $otp,
                'otp_next_try_time' => time() + 120,
                'created' => j_d_stamp_en(),
            ]);
            $this->timer = 120;
            $this->dispatch('set_timer');
        }
        $this->js("setTimeout(() => {window.dispatchEvent(new CustomEvent('focus-otp-input'))}, 100);");
    }

    public function log_check(): bool
    {
        $this->timer = 0;
        $ip = request()->ip();
        $n_code = $this->n_code;
        $oneDayAgo = now()->subDay(); // 24 ساعت قبل

        // 3️⃣ آخرین رکورد برای n_code در 24 ساعت گذشته
        $latest_n_code = DB::table('otp_logs')
            ->where('n_code', $n_code)
            ->where('created_at', '>=', $oneDayAgo)
            ->latest('id')
            ->first();

        // 1️⃣ تعداد کدملی‌های یکتا برای این IP در 24 ساعت گذشته
        $unique_n_codes_for_ip = DB::table('otp_logs')
            ->where('ip', $ip)
            ->where('created_at', '>=', $oneDayAgo)
            ->distinct('n_code')
            ->count('n_code');

        // 2️⃣ تعداد کل رکوردهای n_code در 24 ساعت گذشته
        $total_n_code_count = DB::table('otp_logs')
            ->where('n_code', $n_code)
            ->where('created_at', '>=', $oneDayAgo)
            ->count();

        if ($latest_n_code) {
            if ($latest_n_code->otp_next_try_time - time() > 0) {
                $this->timer = $latest_n_code->otp_next_try_time - time();
                $this->dispatch('set_timer');
                $this->otp_log_check_err = '';
                return True;
            }
            if ($total_n_code_count < 5) {
                $this->otp_log_check_err = '';
                return True;
            } else {
                $this->otp_log_check_err = 'محدودیت کد ملی تا 24 ساعت';
                return false;
            }
        } else {
            if ($unique_n_codes_for_ip < 3) {
                $this->otp_log_check_err = '';
                return True;
            } else {
                $this->otp_log_check_err = 'محدودیت آی پی تا 24 ساعت';
                return false;
            }
        }
    }

    public function otp_verify(): void
    {
        $latest_otp = DB::table('otp_logs')
            ->where('n_code', $this->n_code)
            ->where('mobile_nu', $this->mobile_nu)
            ->latest('id')
            ->first();

        if ($latest_otp->otp == $this->u_otp and time() < $latest_otp->otp_next_try_time) {
            $this->validateOnly('u_otp');
            $pass = simple_pass(8);
            $user = Profile::where('n_code', $this->n_code)->first()->user;
            $user->password = $pass;
            $user->save();

            $this->dispatch('stop_timer');

            DB::table('otp_logs')->where('n_code', $this->n_code)->where('mobile_nu', $this->mobile_nu)->delete();

            SendResetPass::dispatch($this->mobile_nu, $user->user_name, $pass);
//            $sms = new ParsGreenService();
//            $sms->sendResetPassword($this->mobile_nu, $user->user_name, $pass);

            $this->otp_log_check_err = '';
            $this->redirect(route('login', absolute: false));
        }
        if ($latest_otp->otp != $this->u_otp) {
            $this->otp_log_check_err = 'کد پیامکی اشتباه است.';
            return;
        }
        if ($latest_otp->otp_next_try_time) {
            $this->otp_log_check_err = 'کد منقضی شده است.';
        }
    }


}; ?>

<div class="flex flex-col gap-6">
    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')"/>
    @if($step === 1)
        <x-auth-header color="text-yellow-600" :title="__('بازگردانی کلمه عبور')"
                       :description="__('مرحله اول: دریافت کد ملی')"/>
        <form method="POST" wire:submit="check_n_code" class="flex flex-col gap-6" autocomplete="off">
            <!-- National Code -->
            <flux:input wire:model="n_code" :label="__('کدملی:')" type="text" maxlength="10" required
                        autofocus :placeholder="__('کد ملی')"
                        class:input="text-center font-semibold placeholder:font-normal"
                        style="direction:ltr"/>
            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" color="yellow" class="w-full cursor-pointer">
                    {{ __('ادامه') }}
                </flux:button>
            </div>
        </form>
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
            <span>{{ __('یا بازگردید به ') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('صفحه ورود') }}</flux:link>
        </div>
    @endif

    @if($step === 2)
        <x-auth-header color="text-yellow-600" :title="__('بازگردانی کلمه عبور')"
                       :description="__('مرحله دوم: انتخاب شماره موبایل و ارسال otp')"/>

        <form method="POST" wire:submit="otp_verify" class="flex flex-col gap-6" autocomplete="off">
            <!-- National Code and Mobile -->
            <div class="grid grid-cols-2 gap-4">
                <flux:text class="mt-2 text-center">{{__('کدملی: ')}}{{$n_code}}</flux:text>
                @if(count($mobiles) > 1)
                    <flux:select wire:model="mobile_nu" variant="listbox" placeholder="انتخاب موبایل">
                        @foreach($mobiles as $mobile)
                            <flux:select.option value="{{$mobile}}"
                                                style="text-align: center">{{mask_mobile($mobile)}}</flux:select.option>
                        @endforeach
                    </flux:select>
                @else
                    <flux:text class="mt-2 text-center">{{__('موبایل: ')}}{{mask_mobile($mobile_nu)}}</flux:text>
                @endif
            </div>

            <div x-data @focus-otp-input.window="$nextTick(() => $refs.otp.focus())"
                 class="grid grid-cols-2 gap-4">

                @if ($timer > 0)
                    <!-- دکمه ارسال پیامک -->
                    <flux:button type="button" variant="filled" disabled>
                        <span id="timer">{{$timer}}</span>{{ __('تا ارسال مجدد') }}
                    </flux:button>
                @else
                    <!-- دکمه ارسال پیامک -->
                    <flux:button type="button" variant="primary" color="teal" class="cursor-pointer"
                                 wire:click="otp_send">
                        {{ __('ارسال پیامک') }}
                    </flux:button>
                @endif

                <!-- 🔹 این input بعد از ارسال پیامک فوکوس می‌گیرد -->
                <flux:input x-ref="otp" wire:model="u_otp" type="text" maxlength="6" required autofocus
                            :placeholder="__('کد پیامکی')"
                            class:input="text-center font-semibold placeholder:font-normal"
                            style="direction:ltr"/>
            </div>
            <flux:text class="text-center" color="rose">{{$otp_log_check_err}}</flux:text>

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" color="yellow" class="w-full cursor-pointer">
                    {{ __('تایید') }}
                </flux:button>
            </div>
        </form>
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
            <flux:link :href="route('password.request')" wire:navigate>{{ __('شروع مجدد') }}</flux:link>
        </div>
    @endif


    @script
    <script>
        let interval;
        let timer;
        Livewire.on('set_timer', () => {
            // مقدار جدید Livewire رو بگیر
            timer = $wire.get('timer');
            // شمارش قبلی رو پاک کن
            if (interval) clearInterval(interval);
            interval = setInterval(() => {
                timer--;
                document.getElementById('timer').innerHTML = timer;
                if (timer <= 0) {
                    clearInterval(interval);
                    interval = null; // cleanup
                    $wire.set('timer', 0);
                }
            }, 1000);
        });
        Livewire.on('stop_timer', () => {
            clearInterval(interval);
        });
    </script>
    @endscript
</div>
