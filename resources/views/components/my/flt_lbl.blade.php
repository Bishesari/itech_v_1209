@props(['name'=>'', 'label'=>'', 'readonly'=>''])
<!-- floating input (RTL) -->
<div class="relative">
    <input id="{{$name}}" name="{{$name}}" wire:model="{{$name}}" {{$readonly}} placeholder=" " {{ $attributes->merge(['class' => 'peer w-full appearance-none border-2 rounded-lg
     border-gray-300 pt-3.5 pb-2.5 focus:outline-none focus:border-blue-700 transition text-center text-gray-600']) }}/>

    <label for="{{$name}}" class="absolute right-3 top-3 transition-all duration-150 text-gray-400 text-base px-2
    peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-blue-700 peer-focus:bg-accent-foreground
    peer-[:not(:placeholder-shown)]:bg-accent-foreground
    peer-[:not(:placeholder-shown)]:-top-2.5
    peer-[:not(:placeholder-shown)]:right-2.5 peer-[:not(:placeholder-shown)]:text-sm

    ">{{$label}}</label>
    @error($name)<div class="mt-0.5 text-sm text-red-600 text-center">{{$message}}</div>@enderror
</div>
