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
        <flux:text color="blue" size="lg" class="my-2">{{ __('لیست تمام آزمونها') }}</flux:text>
        <flux:separator variant="subtle"/>
    </div>
    <div class="flex gap-x-3">
        @foreach($exams as $exam)

            <flux:card size="sm" class="hover:bg-zinc-50 dark:hover:bg-zinc-700 w-72">
                <flux:heading>{{$exam->title}}</flux:heading>
                <flux:text class="mt-2">{{$exam->standard->name_fa}}</flux:text>
                    <flux:text class="mt-2">Stay up to date with our latest insights, tutorials, and product updates.
                    </flux:text>
                </flux:card>
            </a>
        @endforeach
    </div>

</section>
