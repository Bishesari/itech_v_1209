<flux:navlist.group :heading="__('سکوی توسعه')" class="grid">
    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('داشبرد مدیر آموزشگاه') }}</flux:navlist.item>
</flux:navlist.group>

<flux:navlist.group :heading="__('آزمونها')" class="grid" expandable :expanded="request()->routeIs(['exam_create'])" >
    <flux:navlist.item icon="user-group" href="{{route('exam_create')}}" :current="request()->routeIs('exam_create')" wire:navigate>{{ __('آزمون جدید') }}</flux:navlist.item>
</flux:navlist.group>
