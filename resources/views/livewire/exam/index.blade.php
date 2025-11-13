<?php

use App\Models\Exam;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

new class extends Component {
    public Collection $exams;

    public function mount()
    {
        $this->exams = Exam::all();
    }

}; ?>

<section class="w-full">
    <div class="mb-2">
        <flux:heading size="xl" level="1">{{ __('آزمونها') }}</flux:heading>
        <flux:text color="blue" size="lg" class="my-2">{{ __('لیست آزمونهای کتبی') }}</flux:text>
        <flux:separator variant="subtle"/>
    </div>
    <div class="flex gap-x-3 justify-center">
        @foreach($exams as $exam)

            <flux:card size="sm" class="hover:bg-zinc-50 dark:hover:bg-zinc-700 w-80">
                <flux:heading>{{$exam->standard->name_fa}}</flux:heading>
                <flux:text class="mt-4">{{$exam->title}}</flux:text>
                <flux:text class="mt-4 flex justify-between">
                    <span>{{__('تعداد سوالات: ')}}</span>
                    <span dir="ltr">{{$exam->question_count}}</span>
                    <span>{{__('زمان آزمون: ')}}</span>
                    <span dir="ltr">{{$exam->exam_time}}</span>
                    <span>{{__('دقیقه')}}</span>
                </flux:text>
                <flux:text class="mt-4 flex justify-between"><span>{{__('فعال از: ')}}</span><span dir="ltr">{{$exam->jalali_start_date}}</span></flux:text>
                <flux:text class="mt-4 flex justify-between"><span>{{__('تا: ')}}</span><span dir="ltr">{{$exam->jalali_end_date}}</span></flux:text>

            </flux:card>

        @endforeach
    </div>

</section>
