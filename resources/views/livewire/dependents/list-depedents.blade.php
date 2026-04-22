<section class="w-full space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Dependentes</flux:heading>
            <flux:subheading>Gerencie os usuários vinculados à sua conta</flux:subheading>
        </div>
        <flux:button variant="primary" icon="user-plus" href="{{ route('dependents.create') }}" wire:navigate>
            Novo dependente
        </flux:button>
    </div>

    {{-- Tabela --}}
    <flux:card>
        <flux:table :paginate="$this->dependents" :empty="'Nenhum dependente cadastrado ainda.'">

            <flux:table.columns>
                <flux:table.column>Usuário</flux:table.column>
                <flux:table.column>E-mail</flux:table.column>
                <flux:table.column>Cadastrado em</flux:table.column>
                <flux:table.column align="end">Ações</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->dependents as $dependent)
                    <flux:table.row :key="$dependent->id">

                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <flux:avatar size="sm" name="{{ $dependent->name }}" />
                                <div>
                                    <p class="text-sm font-medium">{{ $dependent->name }}</p>
                                    <p class="text-xs text-zinc-500">#{{ $dependent->id }}</p>
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="text-sm text-zinc-600">{{ $dependent->email }}</span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="text-sm text-zinc-500">
                                {{ $dependent->created_at->format('d/m/Y') }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <div class="flex items-center justify-end gap-1">
                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    icon="trash"
                                    class="text-red-500 hover:text-red-600"
                                    wire:click="delete({{ $dependent->id }})"
                                    wire:confirm="Tem certeza que deseja excluir {{ $dependent->name }}?"
                                />
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye" wire:click="view({{ $dependent->id }})">
                                            Ver detalhes
                                        </flux:menu.item>
                                        <flux:menu.item icon="arrow-right-end-on-rectangle" wire:click="impersonate({{ $dependent->id }})">
                                            Entrar como
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </div>
                        </flux:table.cell>

                    </flux:table.row>
                @endforeach
            </flux:table.rows>

        </flux:table>
    </flux:card>

</section>
