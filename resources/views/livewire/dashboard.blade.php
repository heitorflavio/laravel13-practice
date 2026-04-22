<section class="w-full space-y-6">

    {{-- Header --}}
    <div>
        <flux:heading size="xl">Olá, {{ auth()->user()->name }} 👋</flux:heading>
        <flux:subheading>Aqui está um resumo da sua conta</flux:subheading>
    </div>

    {{-- Cards de estatísticas --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">

        <flux:card class="flex items-center gap-4">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-violet-100 dark:bg-violet-900/30">
                <flux:icon name="document-text" class="size-6 text-violet-600 dark:text-violet-400" />
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $this->stats['totalDocuments'] }}</p>
                <p class="text-sm text-zinc-500">Documentos</p>
            </div>
        </flux:card>

        <flux:card class="flex items-center gap-4">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                <flux:icon name="paper-clip" class="size-6 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $this->stats['totalFiles'] }}</p>
                <p class="text-sm text-zinc-500">Arquivos</p>
            </div>
        </flux:card>

        <flux:card class="flex items-center gap-4">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/30">
                <flux:icon name="users" class="size-6 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $this->stats['totalDependents'] }}</p>
                <p class="text-sm text-zinc-500">Dependentes</p>
            </div>
        </flux:card>

        <flux:card class="flex items-center gap-4">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30">
                <flux:icon name="sparkles" class="size-6 text-green-600 dark:text-green-400" />
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $this->stats['filesWithResume'] }}</p>
                <p class="text-sm text-zinc-500">Arquivos resumidos</p>
            </div>
        </flux:card>

        <flux:card class="flex items-center gap-4">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/30">
                <flux:icon name="document-check" class="size-6 text-amber-600 dark:text-amber-400" />
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $this->stats['docsWithResume'] }}</p>
                <p class="text-sm text-zinc-500">Docs com resumo IA</p>
            </div>
        </flux:card>

        <flux:card class="flex items-center gap-4">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-yellow-100 dark:bg-yellow-900/30">
                <flux:icon name="arrow-path" class="size-6 text-yellow-600 dark:text-yellow-400" />
            </div>
            <div>
                <p class="text-2xl font-bold">{{ $this->stats['filesPending'] }}</p>
                <p class="text-sm text-zinc-500">Processando IA</p>
            </div>
        </flux:card>

    </div>

    <div class="grid gap-4 lg:grid-cols-3">

        {{-- Documentos recentes --}}
        <div class="lg:col-span-2">
            <flux:card class="space-y-4">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">Documentos recentes</flux:heading>
                    <flux:button variant="ghost" size="sm" href="{{ route('documents.index') }}" wire:navigate>
                        Ver todos
                    </flux:button>
                </div>

                @if ($this->recentDocuments->isEmpty())
                    <p class="text-sm text-zinc-500">Nenhum documento criado ainda.</p>
                @else
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-700">
                        @foreach ($this->recentDocuments as $doc)
                            <div class="flex items-center gap-3 py-3">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-900/20">
                                    <flux:icon name="document-text" class="size-5 text-violet-500" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium">{{ $doc->name }}</p>
                                    <p class="text-xs text-zinc-500">
                                        {{ $doc->files_count }} {{ Str::plural('arquivo', $doc->files_count) }}
                                        &middot; {{ $doc->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($doc->ia_resume)
                                        <flux:badge color="green" size="sm" icon="sparkles">IA</flux:badge>
                                    @endif
                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        icon="pencil"
                                        href="{{ route('documents.edit', $doc) }}"
                                        wire:navigate
                                    />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>
        </div>

        {{-- Dependentes --}}
        <div>
            <flux:card class="space-y-4">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">Dependentes</flux:heading>
                    <flux:button variant="ghost" size="sm" href="{{ route('dependents.index') }}" wire:navigate>
                        Ver todos
                    </flux:button>
                </div>

                @if ($this->dependents->isEmpty())
                    <p class="text-sm text-zinc-500">Nenhum dependente cadastrado.</p>
                    <flux:button variant="outline" size="sm" icon="user-plus" href="{{ route('dependents.create') }}" wire:navigate class="w-full">
                        Adicionar dependente
                    </flux:button>
                @else
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-700">
                        @foreach ($this->dependents as $dependent)
                            <div class="flex items-center gap-3 py-3">
                                <flux:avatar size="sm" name="{{ $dependent->name }}" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium">{{ $dependent->name }}</p>
                                    <p class="truncate text-xs text-zinc-500">{{ $dependent->email }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <flux:button variant="outline" size="sm" icon="user-plus" href="{{ route('dependents.create') }}" wire:navigate class="w-full">
                        Adicionar dependente
                    </flux:button>
                @endif
            </flux:card>
        </div>

    </div>

</section>
