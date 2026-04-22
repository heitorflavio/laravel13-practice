<section class="w-full max-w-lg space-y-6">

    {{-- Header --}}
    <div>
        <flux:heading size="xl">Novo dependente</flux:heading>
        <flux:subheading>Crie um usuário vinculado à sua conta</flux:subheading>
    </div>

    {{-- Formulário --}}
    <flux:card>
        <form wire:submit.prevent="create" class="space-y-5">

            <flux:field>
                <flux:label>Nome completo</flux:label>
                <flux:input wire:model="name" placeholder="Ex: João Silva" required autofocus />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>E-mail</flux:label>
                <flux:input wire:model="email" type="email" placeholder="email@exemplo.com" required />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>Senha</flux:label>
                <flux:input wire:model="password" type="password" viewable required />
                <flux:error name="password" />
            </flux:field>

            <flux:field>
                <flux:label>Confirmar senha</flux:label>
                <flux:input wire:model="password_confirmation" type="password" viewable required />
                <flux:error name="password_confirmation" />
            </flux:field>

            <div class="flex justify-end gap-3 pt-2">
                <flux:button variant="ghost" href="{{ route('dependents.index') }}" wire:navigate>
                    Cancelar
                </flux:button>
                <flux:button
                    variant="primary"
                    type="submit"
                    icon="user-plus"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Criar dependente</span>
                    <span wire:loading>Criando...</span>
                </flux:button>
            </div>

        </form>
    </flux:card>

</section>
