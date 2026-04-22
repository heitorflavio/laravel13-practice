<section class="w-full space-y-6" wire:poll.5s="refreshData">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div>
                <flux:heading size="xl">{{ $document->name }}</flux:heading>
                <flux:subheading>Documento #{{ $document->id }}</flux:subheading>
            </div>
            @if ($this->hasPendingWork)
                <flux:badge color="yellow" size="sm" icon="arrow-path" class="animate-spin-slow">
                    Processando IA...
                </flux:badge>
            @endif
        </div>
        <flux:button variant="primary" wire:click="save" wire:loading.attr="disabled" icon="check">
            Salvar documento
        </flux:button>
    </div>

    {{-- Dados do documento --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Informações</flux:heading>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <flux:field>
                <flux:label>Título</flux:label>
                <flux:input wire:model="title" placeholder="Nome do documento" />
            </flux:field>
            <flux:field>
                <flux:label>Conteúdo / observações</flux:label>
                <flux:textarea wire:model="content" rows="3" placeholder="Observações gerais..." />
            </flux:field>
        </div>
    </flux:card>

    {{-- Resumo IA do Documento --}}
    <flux:card class="space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <flux:icon name="sparkles" class="text-violet-500" />
                <flux:heading size="lg">Resumo do Documento (IA)</flux:heading>
            </div>
            <flux:button
                variant="ghost"
                size="sm"
                icon="arrow-path"
                wire:click="summarizeDocument"
                wire:loading.attr="disabled"
                wire:target="summarizeDocument"
            >
                <span wire:loading.remove wire:target="summarizeDocument">Gerar resumo</span>
                <span wire:loading wire:target="summarizeDocument">Gerando...</span>
            </flux:button>
        </div>

        @if ($document->ia_resume)
            <flux:callout variant="info" icon="document-text">
                <flux:callout.text>{{ $document->ia_resume }}</flux:callout.text>
            </flux:callout>
        @else
            <p class="text-sm text-zinc-500">
                Nenhum resumo gerado ainda. Envie arquivos e clique em "Gerar resumo" quando os arquivos forem processados.
            </p>
        @endif
    </flux:card>

    {{-- Upload de arquivos --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Enviar arquivos</flux:heading>

        <flux:field>
            <flux:input type="file" wire:model="files" multiple accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" />
            <flux:description>PDF, Word ou imagens. Máximo 10 MB por arquivo.</flux:description>
        </flux:field>

        @if ($files)
            <div class="divide-y divide-zinc-100 rounded-lg border border-zinc-200">
                @foreach ($files as $index => $file)
                    <div class="flex items-center gap-3 px-4 py-3">
                        <flux:icon name="document" class="shrink-0 text-zinc-400" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $file->getClientOriginalName() }}</p>
                            <p class="text-xs text-zinc-500">
                                .{{ $file->getClientOriginalExtension() }} &middot;
                                {{ number_format($file->getSize() / 1024, 2) }} KB
                            </p>
                        </div>
                        <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="removeFile({{ $index }})" />
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex justify-end">
            <flux:button
                variant="primary"
                wire:click="saveFiles"
                wire:loading.attr="disabled"
                wire:target="saveFiles"
                icon="arrow-up-tray"
            >
                <span wire:loading.remove wire:target="saveFiles">Enviar arquivos</span>
                <span wire:loading wire:target="saveFiles">Enviando...</span>
            </flux:button>
        </div>
    </flux:card>

    {{-- Lista de arquivos --}}
    <flux:card class="space-y-4">
        <flux:heading size="lg">Arquivos vinculados</flux:heading>

        <flux:table :empty="'Nenhum arquivo enviado ainda.'">
            <flux:table.columns>
                <flux:table.column>Arquivo</flux:table.column>
                <flux:table.column>Tipo</flux:table.column>
                <flux:table.column>Enviado em</flux:table.column>
                <flux:table.column>Status IA</flux:table.column>
                <flux:table.column align="end">Ações</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($document->files as $file)
                    <flux:table.row :key="$file->id">

                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <flux:icon name="document" class="shrink-0 text-zinc-400" />
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium max-w-xs">
                                        {{ basename($file->file_path) }}
                                    </p>
                                    @if ($file->ia_resume)
                                        <p class="mt-0.5 truncate text-xs text-zinc-500 max-w-xs">
                                            {{ $file->ia_resume }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge variant="outline" size="sm">
                                {{ $file->mime_type ?? '—' }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <span class="text-sm text-zinc-600">
                                {{ $file->created_at->format('d/m/Y H:i') }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            @php
                                $statusMap = [
                                    'pending'    => ['color' => 'zinc',   'label' => 'Pendente'],
                                    'processing' => ['color' => 'yellow', 'label' => 'Processando'],
                                    'done'       => ['color' => 'green',  'label' => 'Concluído'],
                                    'error'      => ['color' => 'red',    'label' => 'Erro'],
                                ];
                                $s = $statusMap[$file->status] ?? ['color' => 'zinc', 'label' => $file->status];
                            @endphp
                            <flux:badge :color="$s['color']" size="sm">{{ $s['label'] }}</flux:badge>
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <div class="flex items-center justify-end gap-1">
                                @if ($file->file_url)
                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        icon="arrow-down-tray"
                                        :href="$file->file_url"
                                        target="_blank"
                                    />
                                @endif
                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    icon="trash"
                                    class="text-red-500 hover:text-red-600"
                                    wire:click="deleteFile({{ $file->id }})"
                                    wire:confirm="Tem certeza que deseja remover este arquivo?"
                                />
                            </div>
                        </flux:table.cell>

                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>

</section>
