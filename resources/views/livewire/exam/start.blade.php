<?php

use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public Exam $exam;

    public function beginExam()
    {
        $user = Auth::user();

        // بررسی اینکه کاربر قبلاً شرکت نکرده
        if ($user->exams()->where('exam_id', $this->exam->id)->exists()) {
            return redirect()->route('exam.take', $this->exam->id);
        }

        // ترتیب تصادفی سوالات
        $questionIds = $this->exam->questions()->pluck('questions.id')->shuffle()->toArray();

        // ثبت شروع آزمون
        $user->exams()->attach($this->exam->id, [
            'started_at' => now(),
            'question_order' => json_encode($questionIds),
        ]);

        return redirect()->route('exam.take', $this->exam->id);
    }


}; ?>

<section class="w-full">
    <div class="mb-2">
        <flux:heading size="xl" level="1">{{ __('آزمونها') }}</flux:heading>
        <flux:text color="blue" size="lg" class="my-2">{{ __('لیست آزمونهای کتبی') }}</flux:text>
        <flux:separator variant="subtle"/>
    </div>
    <div class="flex gap-x-3 justify-center">

    </div>

    <h1>آزمون: {{ $exam->title }}</h1>
    <p>مدت زمان: {{ $exam->exam_time }} دقیقه</p>
    <p>تعداد سوالات: {{ $exam->question_count }}</p>

    @php
        $examUser = auth()->user()->exams()->where('exam_id', $exam->id)->first()?->pivot;
    @endphp

    @if($examUser && $examUser->is_finished)
        <p>شما قبلاً این آزمون را به پایان رسانده‌اید.</p>
    @elseif($examUser)
        <p>آزمون شما در حال انجام است.
            <flux:link href="{{ route('exam.take', $exam->id) }}">ادامه آزمون</flux:link>
        </p>
    @else
        <form wire:submit.prevent="beginExam">
            <flux:button type="submit">شروع آزمون</flux:button>
        </form>
    @endif
</section>
