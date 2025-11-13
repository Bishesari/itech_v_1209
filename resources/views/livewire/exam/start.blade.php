<?php

use App\Models\Exam;
use App\Models\ExamUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {

    public Exam $exam;

    public ?ExamUser $examUser;
    public function mount(): void
    {
        $this->examUser = ExamUser::where('exam_id', $this->exam->id)->where('user_id', Auth::id())->first();
    }
    public function examBegin()
    {
        // ترتیب تصادفی سوالات
        $questionIds = $this->exam->questions()->pluck('questions.id')->shuffle()->toArray();
        Auth::user()->exams()->attach($this->exam->id, [
            'started_at' => now(),
            'question_order' => json_encode($questionIds),
        ]);

        $examUser = ExamUser::where('exam_id', $this->exam->id)->where('user_id', Auth::id())->first();
        return redirect()->route('exam.take', ['exam_user' => $examUser->id]);

    }

}; ?>

<section class="w-full">
    <div class="mb-2">
        <flux:heading size="xl" level="1">{{ __('آزمونها') }}</flux:heading>
        <flux:text color="blue" size="lg" class="my-2">{{ __('لیست آزمونهای کتبی') }}</flux:text>
        <flux:separator variant="subtle"/>
    </div>
    @if($examUser)
        @if(now()->greaterThan($examUser->started_at->addMinutes($examUser->exam->exam_time)))
            {{__('مهلت آزمون به پایان رسیده')}}
        @else
            <flux:button href="{{route('exam.take', ['exam_user' => $examUser->id])}}">
                {{__('درحال انجام است، ادامه آزمون.')}}
            </flux:button>
        @endif
    @else
        <flux:button wire:click="examBegin">
            {{__('شروع آزمون')}}
        </flux:button>

    @endif

</section>
