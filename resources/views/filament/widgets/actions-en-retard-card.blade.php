<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            ⚠️ Actions en retard
        </x-slot>
        <x-slot name="description">
            Actions qui auraient dû être effectuées
        </x-slot>

        @php
            $actions = $this->getActionsEnRetard();
        @endphp

        @if($actions->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p class="font-medium">✅ Aucune action en retard</p>
                <p class="text-sm mt-1">Excellent ! Toutes vos actions sont à jour.</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($actions as $prospect)
                    <a href="{{ \App\Filament\Resources\ProspectResource::getUrl('view', ['record' => $prospect]) }}" 
                       class="block p-3 bg-red-50 dark:bg-red-900/10 rounded-lg border border-red-200 dark:border-red-800 hover:border-red-400 dark:hover:border-red-600 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="font-semibold text-red-900 dark:text-red-100 text-sm">
                                    {{ "{$prospect->prenom} {$prospect->nom}" }}
                                </p>
                                <p class="text-xs text-red-700 dark:text-red-300 mt-1 line-clamp-1">
                                    {{ $prospect->prochaine_action }}
                                </p>
                            </div>
                            <span class="text-xs text-red-600 dark:text-red-400 font-medium">
                                {{ $prospect->date_prochaine_action->diffForHumans() }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
