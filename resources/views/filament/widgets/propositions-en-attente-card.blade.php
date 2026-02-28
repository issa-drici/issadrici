<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            📄 Propositions en attente
        </x-slot>
        <x-slot name="description">
            Propositions envoyées en attente de réponse
        </x-slot>

        @php
            $propositions = $this->getPropositions();
        @endphp

        @if($propositions->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p class="font-medium">Aucune proposition en attente</p>
                <p class="text-sm mt-1">Envoyez vos premières propositions depuis les fiches prospects.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($propositions as $prospect)
                    <a href="{{ \App\Filament\Resources\ProspectResource::getUrl('view', ['record' => $prospect]) }}" 
                       class="block p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white text-sm mb-1">
                                    {{ "{$prospect->prenom} {$prospect->nom}" }}
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                    {{ $prospect->societe }}
                                </p>
                                <div class="flex items-center gap-3 text-xs">
                                    @if($prospect->montant_proposition)
                                        <span class="text-gray-700 dark:text-gray-300">
                                            <strong>{{ number_format($prospect->montant_proposition, 0, ',', ' ') }} €</strong>
                                        </span>
                                    @endif
                                    @if($prospect->duree_proposition)
                                        <span class="text-gray-500 dark:text-gray-400">
                                            {{ $prospect->duree_proposition }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $prospect->updated_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
