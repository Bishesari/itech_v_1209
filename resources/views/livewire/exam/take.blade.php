<?php


use App\Models\ExamAnswer;
use App\Models\ExamUser;
use App\Models\Option;
use App\Models\Question;
use Livewire\Volt\Component;

new class extends Component {
    public ExamUser $examUser;

    public array $questionIds = [];

    public ?Question $currentQuestion = null;
    public $selectedOption = null;
    public int $current_index;

    public int $user_answered_count = 0;
    public function mount()
    {
        $this->questionIds = json_decode($this->examUser->question_order, true);
        $this->user_answered_count = $this->examUser->answers()->count();
        $this->goToQuestion(0);

    }

    public function goToQuestion(int $index): void
    {
        $this->currentQuestion = Question::find($this->questionIds[$index]);

        $selectedOptionId = ExamAnswer::where('exam_user_id', $this->examUser->id)
            ->where('question_id', $this->currentQuestion->id)
            ->value('option_id');

        $this->selectedOption = $selectedOptionId;
        $this->current_index = $index;
    }

    public function submitAnswer(): void
    {

        if (!is_null($this->selectedOption)) {
            // ذخیره پاسخ
            ExamAnswer::updateOrCreate(
                [
                    'exam_user_id' => $this->examUser->id,
                    'question_id' => $this->currentQuestion->id
                ],
                [
                    'option_id' => $this->selectedOption,
                    'is_correct' => Option::find($this->selectedOption)->is_correct,
                ]
            );
            // پاک کردن انتخاب بعد از ثبت
            $this->selectedOption = null;

            $this->user_answered_count = $this->examUser->answers()->count();
        }
        if ($this->current_index == count($this->questionIds) - 1) {
            $this->goToQuestion(0);

        } else {
            $this->goToQuestion($this->current_index + 1);
        }

    }

    public function finishExam()
    {
        $answered = $this->examUser->answers()->where('is_correct', true)->count();
        $total = count($this->questionIds);


        // ثبت زمان پایان آزمون
        $this->examUser->finished_at = now();
        $this->examUser->score = ($answered / $total) * 100;
        $this->examUser->save();

        // هدایت به صفحه نتایج
        return redirect()->route('exam.results', ['examUser' => $this->examUser->id]);
    }


}; ?>

<section class="w-full">
    <input type="text" wire:model="user_answered_count">
    <div class="mb-2">
        <flux:heading size="xl" level="1">{{$examUser->exam->standard->name_fa}}</flux:heading>
        <flux:text color="blue" size="lg" class="my-2">{{$examUser->exam->title}}</flux:text>
        <flux:separator variant="subtle"/>
    </div>

    <div class="flex flex-wrap gap-2 justify-evenly">
        @foreach($questionIds as $i => $questionId)
            @php
                $isCurrent = $currentQuestion && $currentQuestion->id === $questionId;

                // بررسی اینکه آیا سوال جواب داده شده یا نه
                $answered = \App\Models\ExamAnswer::where('exam_user_id', $examUser->id)
                            ->where('question_id', $questionId)
                            ->exists();

                // تعیین رنگ و استایل دکمه
                if ($isCurrent) {
                    $color = 'yellow';
                    $variant = 'primary';
                } elseif ($answered) {
                    $color = 'sky';
                    $variant = 'primary';
                } else {
                    $color = '';
                    $variant = 'filled';
                }
            @endphp

            <flux:button size="sm" variant="{{ $variant }}" color="{{ $color }}" class="cursor-pointer"
                         wire:click="goToQuestion({{$i}})">
                {{++$i}}
            </flux:button>
            <button
        @endforeach
    </div>

    <div class="mt-3">
        <flux:callout color="zinc" inline>
            <flux:callout.heading>#{{$currentQuestion->id}} - {{$currentQuestion->text}}</flux:callout.heading>
        </flux:callout>

        <flux:radio.group wire:model="selectedOption" variant="cards" class="max-sm:flex-col my-2">
            @foreach($currentQuestion->options as $option)
                <flux:radio value="{{ $option->id }}" label="{{ $option->text }}"
                            wire:key="option-{{ $option->id }}" class="cursor-pointer"/>
            @endforeach
        </flux:radio.group>
        <div class="flex justify-center flex-row">
            <flux:button wire:click="submitAnswer" class="cursor-pointer">{{__('ثبت پاسخ و سوال بعدی')}} {{__('->')}}</flux:button>
            @if($user_answered_count == count($questionIds)) @endif
            <flux:button variant="primary" color="green" wire:click="finishExam" class="cursor-pointer mr-5">{{__('ثبت پایان آزمون')}}</flux:button>
        </div>
    </div>
</section>
