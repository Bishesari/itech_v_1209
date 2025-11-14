<flux:navlist.group :heading="__('سکوی توسعه')" class="grid">
    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('داشبرد موسس') }}</flux:navlist.item>
</flux:navlist.group>

<flux:navlist.group :heading="__('اطلاعات پایه')" class="grid" expandable :expanded="request()->routeIs(['roles', 'institutes', 'users', 'fields', 'standards'])" >
    <flux:navlist.item icon="user-group" :href="route('roles')" :current="request()->routeIs('roles')" wire:navigate>{{ __('نقشهای کاربری') }}</flux:navlist.item>
    <flux:navlist.item icon="user-group" :href="route('institutes')" :current="request()->routeIs('institutes')" wire:navigate>{{ __('آموزشگاهها') }}</flux:navlist.item>
    <flux:navlist.item icon="user-group" :href="route('users')" :current="request()->routeIs('users')" wire:navigate>{{ __('کاربران') }}</flux:navlist.item>

    <flux:navlist.item icon="user-group" :href="route('fields')" :current="request()->routeIs('fields')" wire:navigate>{{ __('رشته های آموزشی') }}</flux:navlist.item>
    <flux:navlist.item icon="user-group" :href="route('standards')" :current="request()->routeIs('standards')" wire:navigate>{{ __('استانداردهای آموزشی') }}</flux:navlist.item>

</flux:navlist.group>


<flux:navlist.group :heading="__('آزمونهای کتبی')" class="grid" expandable :expanded="request()->routeIs(['exams', 'exam_create'])" >
    <flux:navlist.item icon="user-group" href="{{route('exams')}}" :current="request()->routeIs('exams')" wire:navigate>{{ __('آزمونها') }}</flux:navlist.item>
    <flux:navlist.item icon="user-group" href="{{route('exam_create')}}" :current="request()->routeIs('exam_create')" wire:navigate>{{ __('آزمون جدید') }}</flux:navlist.item>
</flux:navlist.group>
