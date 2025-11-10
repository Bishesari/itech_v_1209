<?php

use App\Models\Chapter;
use App\Models\Question;
use App\Models\Standard;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {

    use WithPagination;

    public $standard_id = 0;
    public $chapter_id = 0;
    public $filterApplied = false;

    public $standards = [];
    public $chapters = [];

    public function mount()
    {
        $this->standards = Standard::all();
        $this->chapters = Chapter::all(); // برای شروع می‌تونی همه فصل‌ها رو نمایش بدی
    }

    public function updatedStandardId()
    {
        // وقتی استاندارد تغییر کرد، فصل‌ها ریست می‌شوند
        $this->chapter_id = 0;

        // اگر بخوای، می‌تونی فقط فصل‌های آن استاندارد را لود کنی:
        $this->chapters = Chapter::where('standard_id', $this->standard_id)->get();
    }

    public function applyFilter()
    {
        $this->filterApplied = true;
        $this->resetPage(); // بازگشت به صفحه 1 در pagination
    }

    public function with(): array
    {
        $query = Question::query();

        if ($this->standard_id != 0 && $this->chapter_id == 0) {
            $query->where('standard_id', $this->standard_id);
        }
        elseif ($this->standard_id != 0 && $this->chapter_id != 0) {
            $query->where('standard_id', $this->standard_id)
                ->where('chapter_id', $this->chapter_id);
        }


        return [
            'questions' => $query->latest()->paginate(10),
        ];
    }


}; ?>
<section class="w-full">
    <div class="relative w-full mb-2">
        <flux:heading size="xl" level="1">{{ __('بانک کلی سوالات') }}</flux:heading>
        <flux:text size="lg" class="my-2">{{ __('بخش فیلتر سوال') }}</flux:text>
        <flux:separator variant="subtle"/>
    </div>



    {{-- فیلترها --}}
    <div class="flex gap-4 items-end">
        {{-- استاندارد --}}
        <div>
            <label class="block mb-1 text-sm font-medium">استاندارد</label>
            <select wire:model="standard_id" class="border rounded p-2">
                <option value="0">همه استانداردها</option>
                @foreach($standards as $standard)
                    <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- فصل --}}
        <div>
            <label class="block mb-1 text-sm font-medium">فصل</label>
            <select wire:model="chapter_id" class="border rounded p-2">
                <option value="0">همه فصل‌ها</option>
                @foreach($chapters as $chapter)
                    <option value="{{ $chapter->id }}">{{ $chapter->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- دکمه فیلتر --}}
        <button wire:click="applyFilter" class="bg-blue-600 text-white px-4 py-2 rounded">
            فیلتر
        </button>
    </div>



    @foreach($questions as $question)
        <flux:callout color="zinc">
            <flux:callout.heading>#{{$question->id}} - {{$question->text}}</flux:callout.heading>
            <flux:text size="sm">{{__('فصل')}} {{$question->chapter->number}}
                : {{$question->chapter->title}} {{'( ' . $question->chapter->standard->name_fa . ' )'}} {{'( '. $question->maker->profile->l_name_fa . ' )'}}</flux:text>
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
                <flux:callout variant="{{$var}}" heading="{!! $option->text !!}" dir="{{$option->dir}}"
                              icon='{{$icon}}'/>
            @endforeach
        </div>
    @endforeach

</section>
