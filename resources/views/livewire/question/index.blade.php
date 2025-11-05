<?php

use App\Models\Chapter;
use App\Models\Question;
use App\Models\Standard;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

new class extends Component {
    public string $standard_id = '';
    public string $chapter_id = '';

    public Collection $questions;

    public function mount($sid, $cid): void
    {
        $this->standard_id = $sid;
        $this->chapter_id = $cid;
        $this->filter();
    }

    public function filter(): void
    {
        if ($this->standard_id == 0 and $this->chapter_id == 0) {
            $this->questions = Question::latest()->get();
        } else if ($this->standard_id != 0 and $this->chapter_id == 0) {
            // کل سوالات استاندارد فیلتر شود
            $standard = Standard::find($this->standard_id);
            $this->questions = $standard->questions()->latest()->get();
        } else if ($this->standard_id == 0 and $this->chapter_id != 0) {
            $chapter = Chapter::find($this->chapter_id);
            $this->standard_id = $chapter->standard->id;
            $this->questions = $chapter->questions->latest()->get();
        } else if ($this->standard_id != 0 and $this->chapter_id != 0) {
            $this->questions = Chapter::find($this->chapter_id)->questions()->latest()->get();
        }

    }

    public function updatedStandardId($st_id): void
    {
        $this->standard_id = $st_id;
        if ($st_id == 0){
            $this->standard_id = 0;
            $this->chapter_id = 0;
        }else{
            $standard = Standard::find($st_id);
            if ($standard->chapters()->exists()){
                $this->chapter_id = $standard->chapters()->value('id');
            }
            else{
                $this->chapter_id = 0;
            }
        }
    }


}; ?>
<section class="w-full">
    <div class="relative w-full mb-2">
        <flux:heading size="xl" level="1">{{ __('بانک کلی سوالات') }}</flux:heading>
        <flux:text size="lg" class="my-2">{{ __('بخش فیلتر سوال') }}</flux:text>
        <flux:separator variant="subtle"/>
    </div>

    <div class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 mb-3">
        <!-- Standard select menu... -->
        <flux:select wire:model.live="standard_id" variant="listbox" placeholder="استانداردی انتخاب کنید ..."
                     searchable size="sm">
            <flux:select.option value="0">{{__('همه استانداردها')}}</flux:select.option>
        @foreach (\App\Models\Standard::all() as $standard)
                <flux:select.option value="{{$standard->id}}">{{ $standard->name_fa }}</flux:select.option>
            @endforeach
        </flux:select>

        <!-- Chapter select menu... -->
        <flux:select wire:model.live="chapter_id" wire:key="{{ $standard_id }}" variant="listbox"
                     placeholder="سرفصل را انتخاب کنید ..." size="sm">
            <flux:select.option value="0">{{__('همه فصلها')}}</flux:select.option>
            @foreach (\App\Models\Chapter::whereStandardId($standard_id)->get() as $chapter)
                <flux:select.option value="{{$chapter->id}}">{{ $chapter->title }}</flux:select.option>
            @endforeach
        </flux:select>
        <div class="flex justify-between">
            <flux:button wire:click="filter" class="cursor-pointer" size="sm">{{__('فیلتر')}}</flux:button>
            <flux:button variant="ghost" size="sm" disabled>{{$questions->count()}} {{__('رکورد')}}</flux:button>
            <flux:button href="{{URL::signedRoute('create_question', ['sid'=>$standard_id, 'cid'=>$chapter_id] )}}" variant="primary" color="sky" size="sm" class="cursor-pointer">{{__('جدید')}}</flux:button>
        </div>
    </div>


    @foreach($questions as $question)
        <flux:callout color="zinc">
            <flux:callout.heading>#{{$question->id}} - {{$question->text}}</flux:callout.heading>
            <flux:text size="sm">{{__('فصل')}} {{$question->chapter->number}}
                : {{$question->chapter->title}} {{__('از ')}}{{$question->chapter->standard->name_fa}}</flux:text>
        </flux:callout>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-1 mt-1 mb-6">
            @foreach($question->options as $option)
                @if($option->is_correct)
                    @php($var = 'success')
                    @php($icon = 'check-circle')
                @else
                    @php($var = 'secondary')
                    @php($icon = '')
                @endif
                    <flux:callout variant="{{$var}}" heading="{!! $option->text !!}" dir="{{$option->dir}}" icon='{{$icon}}' />
            @endforeach
        </div>
    @endforeach

</section>
