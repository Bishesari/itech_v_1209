<?php

use App\Models\Exam;

use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public Exam $exam;
    public array $questionIds = [];

    public ?Question $currentQuestion = null;
    public $selectedOption = '';

    public function mount(Exam $exam): void
    {
        $user = Auth::user();
        $examUser = $user->exams()->where('exam_id', $exam->id)->first()->pivot;

        $this->questionIds = json_decode($examUser->question_order, true);
    }

    public function goToQuestion(int $index): void
    {
        $this->selectedOption = '';
        $this->currentQuestion = Question::find($this->questionIds[$index]);
    }


}; ?>

<section class="w-full">
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

            <flux:radio.group wire:model="selectedOption"  variant="cards" class="max-sm:flex-col my-2">
                @foreach($currentQuestion->options as $option)
                    <flux:radio value="{{ $option->id }}" label="{{ $option->text }}" wire:key="option-{{ $option->id }}"/>
                @endforeach

            </flux:radio.group>
        @else
            <p>تمام سوالات نمایش داده شد.</p>
        @endif
    </div>

    <input wire:model="selectedOption">


</section>
