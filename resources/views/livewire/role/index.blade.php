<?php

use App\Models\Role;
use Flux\Flux;

use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $sortBy = 'id';
    public $sortDirection = 'desc';

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    #[On('role-created')]
    public function roles()
    {
        return Role::query()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(10);

    }

    #[On('role-created')]
    public function reset_page(): void
    {
        $this->resetPage();
    }

    public string $name_fa = '';
    public string $name_en = '';

    public int $editing_id = 0;

    public function edit(Role $role): void
    {
        $this->editing_id = $role['id'];
        $this->name_fa = $role['name_fa'];
        $this->name_en = $role['name_en'];
        $this->modal('edit-role')->show();
    }

    public function update(): void
    {
        $editing_role = Role::find($this->editing_id);

        if ( ($editing_role['name_fa'] != $this->name_fa) and ($editing_role['name_en'] != $this->name_en) ){
            $validated = $this->validate([
                'name_fa' => 'required|unique:roles|min:2',
                'name_en' => 'required|unique:roles|min:3',
            ]);
            $editing_role->update($validated);
        }
        elseif ( ($editing_role['name_fa'] != $this->name_fa) and ($editing_role['name_en'] == $this->name_en) ){
            $validated = $this->validate([
                'name_fa' => 'required|unique:roles|min:2',
                'name_en' => 'required|min:3',
            ]);
            $editing_role->update($validated);
        }
        elseif ( ($editing_role['name_fa'] == $this->name_fa) and ($editing_role['name_en'] != $this->name_en) ){
            $validated = $this->validate([
                'name_fa' => 'required|min:2',
                'name_en' => 'required|unique:roles|min:3',
            ]);
            $editing_role->update($validated);
        }
        $this->modal('edit-role')->close();
        Flux::toast(
            heading: 'انجام شد.',
            text: 'نقش کاربری با موفقیت ویرایش شد.',
            variant: 'success'
        );
    }

    public function reset_edit(): void
    {
        $this->editing_id = 0;
    }

}; ?>

<section class="w-full">
    <div class="relative w-full mb-2">
        <flux:heading size="xl" level="1">{{ __('نقشهای کاربری') }}</flux:heading>
        <flux:subheading size="lg" class="mb-2">{{ __('بخش مدیریت نقشهای کاربری') }}</flux:subheading>
        <flux:separator variant="subtle"/>
    </div>
    <div class="mb-2">
        <livewire:role.create/>
        <flux:separator class="mt-2" variant="subtle"/>
    </div>
    <flux:table :paginate="$this->roles" class="text-center inline">
        <flux:table.columns>
            <flux:table.column align="center" sortable :sorted="$sortBy === 'id'" :direction="$sortDirection"
                               wire:click="sort('id')">
                {{__('#')}}
            </flux:table.column>

            <flux:table.column align="center" sortable :sorted="$sortBy === 'name_fa'" :direction="$sortDirection"
                               wire:click="sort('name_fa')">
                {{__('عنوان فارسی')}}
            </flux:table.column>


            <flux:table.column align="center" sortable :sorted="$sortBy === 'name_en'" :direction="$sortDirection"
                               wire:click="sort('name_en')">
                {{__('عنوان لاتین')}}
            </flux:table.column>
            <flux:table.column align="center">{{__('عملیات')}}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->roles as $role)
                @php($ed = '')
                @if($role->id == $editing_id)
                    @php($ed = 'bg-amber-100')
                @endif

                <flux:table.row class="hover:bg-green-50 {{$ed}}" :key="$role->id">
                    <flux:table.cell class="whitespace-nowrap">{{ $role->id }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $role->name_fa }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $role->name_en }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:button wire:click="edit({{$role}})" variant="ghost" size="sm" class="cursor-pointer">
                            <flux:icon.pencil-square variant="solid" class="text-amber-500 dark:text-amber-300 size-5"/>
                        </flux:button>
                        <flux:button href="{{URL::signedRoute('show_role', ['role'=>$role->id])}}" variant="ghost"
                                     size="sm" class="cursor-pointer" wire:navigate>
                            <flux:icon.eye class="text-blue-500 size-5"/>
                        </flux:button>

                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <!-- Edit Modal -->
    <flux:modal @close="reset_edit" variant="flyout" position="left" name="edit-role" :show="$errors->isNotEmpty()" focusable class="w-80 md:w-96" :dismissible="false">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('فرم ویرایش نقش') }}</flux:heading>
                <flux:text class="mt-2">{{ __('توجه کنید این نقش را قبلا تعریف نکرده باشید.') }}</flux:text>
            </div>
            <form wire:submit="update" class="flex flex-col gap-6" autocomplete="off">
                <flux:input wire:model="name_fa" :label="__('عنوان فارسی')" type="text" class:input="text-center"
                            maxlength="35" required autofocus/>
                <flux:input wire:model="name_en" :label="__('عنوان لاتین')" type="text" class:input="text-center"
                            maxlength="35" required style="direction:ltr"/>
                <div class="flex justify-between space-x-2 rtl:space-x-reverse flex-row-reverse">
                    <flux:button variant="primary" color="orange" type="submit"
                                 class="cursor-pointer">{{ __('ویرایش') }}</flux:button>
                    <flux:modal.close>
                        <flux:button variant="filled" class="cursor-pointer">{{ __('انصراف') }}</flux:button>
                    </flux:modal.close>
                </div>
            </form>
        </div>
    </flux:modal>
</section>


