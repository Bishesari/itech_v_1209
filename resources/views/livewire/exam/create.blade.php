<?php

use App\Models\Standard;
use Livewire\Volt\Component;

new class extends Component {
    public string $standard_id = '';
    public $chapters = [];

    public function updatedStandardId(): void
    {
        $this->chapters = Standard::find($this->standard_id)?->chapters ?? [];

    }
}; ?>

<section class="w-full">
    <div class="relative w-full mb-2">
        <flux:heading size="xl" level="1">{{ __('آزمونها') }}</flux:heading>
        <flux:text color="blue" size="lg" class="my-2">{{ __('درج آزمون جدید') }}</flux:text>
        <flux:separator variant="subtle"/>
    </div>

    <form wire:submit="add_exam" class="grid mt-5 sm:w-[400px]" autocomplete="off" autofocus>

        <!-- Standard select menu... -->
        <flux:select wire:model.live="standard_id" variant="listbox" placeholder="استانداردی انتخاب کنید ..."
                     label="استاندارد" searchable class="mb-5">
            @foreach (\App\Models\Standard::all() as $standard)
                <flux:select.option value="{{$standard->id}}">{{ $standard->name_fa }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:checkbox.group label="Subscription preferences" variant="cards" class="flex-col">
            @foreach ($chapters as $chapter )
                <flux:checkbox value="newsletter"
                               label="{{$chapter->number.' - '.$chapter->title}}"
                               description="{{__('تعداد سوالات: ')}}{{$chapter->questions->count()}}"/>
            @endforeach
        </flux:checkbox.group>
    </form>

</section>
