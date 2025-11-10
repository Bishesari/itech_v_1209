<flux:navlist.group :heading="__('سکوی توسعه')" class="grid">
    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('داشبرد طراح سوال') }}</flux:navlist.item>
</flux:navlist.group>

<flux:navlist.group :heading="__('بانک سوال')" class="grid" expandable :expanded="request()->routeIs(['questions', 'create_question'])" >
    <flux:navlist.item icon="user-group" href="{{URL::signedRoute('questions', ['sid'=>0, 'cid'=>0] )}}" :current="request()->routeIs('questions')" wire:navigate>{{ __('کل سوالات') }}</flux:navlist.item>
    <flux:navlist.item icon="user-group" href="{{URL::signedRoute('create_question', ['sid'=>0, 'cid'=>0] )}}" :current="request()->routeIs('create_question')" wire:navigate>{{ __('درج سوال') }}</flux:navlist.item>
</flux:navlist.group>
