<section class="w-full space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Documentos</flux:heading>
            <flux:subheading>Gerencie os documentos médicos da sua conta</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create" wire:loading.attr="disabled" wire:target="create">
            <span wire:loading.remove wire:target="create">Novo documento</span>
            <span wire:loading wire:target="create">Criando...</span>
        </flux:button>
    </div>

    {{-- Tabela --}}
    <flux:card>
        <flux:table :paginate="$this->documents" :empty="'Nenhum documento encontrado.'">

            <flux:table.columns>
                <flux:table.column>Documento</flux:table.column>
                <flux:table.column>Tipo</flux:table.column>
                <flux:table.column>Arquivos</flux:table.column>
                <flux:table.column>Resumo IA</flux:table.column>
                <flux:table.column>Criado em</flux:table.column>
                <flux:table.column align="end">Ações</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->documents as $document)
                    <flux:table.row :key="$document->id">

                        {{-- Nome --}}
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-violet-50">
                                    <flux:icon name="document-text" class="size-5 text-violet-500" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ $document->name }}</p>
                                    @if ($document->doctor_name)
                                        <p class="text-xs text-zinc-500">Dr. {{ $document->doctor_name }}</p>
                                    @endif
                                </div>
                            </div>
                        </flux:table.cell>

                        {{-- Tipo --}}
                        <flux:table.cell>
                            <flux:badge variant="outline" size="sm">
                                {{ $document->type ?? '—' }}
                            </flux:badge>
                        </flux:table.cell>

                        {{-- Arquivos --}}
                        <flux:table.cell>
                            @php $count = $document->files_count ?? $document->files->count() @endphp
                            @if ($count > 0)
                                <flux:badge color="zinc" size="sm">
                                    {{ $count }} {{ Str::plural('arquivo', $count) }}
                                </flux:badge>
                            @else
                                <span class="text-xs text-zinc-400">Sem arquivos</span>
                            @endif
                        </flux:table.cell>

                        {{-- Resumo IA --}}
                        <flux:table.cell>
                            @if ($document->ia_resume)
                                <flux:badge color="green" size="sm" icon="sparkles">
                                    Disponível
                                </flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">
                                    Pendente
                                </flux:badge>
                            @endif
                        </flux:table.cell>

                        {{-- Data --}}
                        <flux:table.cell>
                            <span class="text-sm text-zinc-500">
                                {{ $document->created_at->format('d/m/Y') }}
                            </span>
                        </flux:table.cell>

                        {{-- Ações --}}
                        <flux:table.cell align="end">
                            <div class="flex items-center justify-end gap-1">
                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    icon="pencil"
                                    href="{{ route('documents.edit', $document) }}"
                                    wire:navigate
                                />
                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    icon="trash"
                                    class="text-red-500 hover:text-red-600"
                                    wire:click="delete({{ $document->id }})"
                                    wire:confirm="Tem certeza que deseja excluir '{{ $document->name }}'?"
                                />
                            </div>
                        </flux:table.cell>

                    </flux:table.row>
                @endforeach
            </flux:table.rows>

        </flux:table>
    </flux:card>

</section>
