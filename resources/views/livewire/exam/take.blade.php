<?php

use App\Models\Exam;

use App\Models\ExamAnswer;
use App\Models\ExamUser;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public Exam $exam;
    public ?ExamUser $examUser;

    public array $questionIds = [];


    public ?Question $currentQuestion = null;
    public $selectedOption = '';

    public function mount(Exam $exam): void
    {
        $this->examUser = ExamUser::where('exam_id', $exam->id)->where('user_id', Auth::id())->first();
        $this->questionIds = json_decode($this->examUser->question_order, true);
    }

    public function goToQuestion(int $index): void
    {
        $this->currentQuestion = Question::find($this->questionIds[$index]);

        $selectedOptionId = ExamAnswer::where('exam_user_id', $this->examUser->id)
            ->where('question_id', $this->currentQuestion->id)
            ->value('option_id');

        $this->selectedOption = $selectedOptionId;


    }

    public function submitAnswer(): void
    {

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
    }


}; ?>

<section class="w-full">



    <input type="text" wire:model="selectedOption">
    <div class="mb-2">
        <flux:heading size="xl" level="1">{{$exam->standard->name_fa}}</flux:heading>
        <flux:text color="blue" size="lg" class="my-2">{{$exam->title}}</flux:text>
        <flux:separator variant="subtle"/>
    </div>

    <div class="flex flex-wrap gap-2 justify-evenly">
        @foreach($questionIds as $i => $questionId)
            <flux:button size="sm" variant="primary" color="cyan" class="cursor-pointer"
                         wire:click="goToQuestion({{$i}})">
                {{++$i}}
            </flux:button>
            <button
        @endforeach
    </div>

    <div class="mt-3">
        @if($currentQuestion)
            <flux:callout color="zinc" inline>
                <flux:callout.heading>#{{$currentQuestion->id}} - {{$currentQuestion->text}}</flux:callout.heading>
            </flux:callout>

            <flux:radio.group wire:model="selectedOption" variant="cards" class="max-sm:flex-col my-2">
                @foreach($currentQuestion->options as $option)
                    <flux:radio value="{{ $option->id }}" label="{{ $option->text }}"
                                wire:key="option-{{ $option->id }}" class="cursor-pointer"/>
                @endforeach
            </flux:radio.group>
            <flux:button wire:click="submitAnswer" class="cursor-pointer">ثبت پاسخ</flux:button>
        @else
            <p>تمام سوالات نمایش داده شد.</p>
        @endif
    </div>
</section>
