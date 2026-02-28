<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            👤 Prospects récents
        </x-slot>
        <x-slot name="description">
            Les 5 derniers prospects créés
        </x-slot>

        @php
            $prospects = $this->getProspectsRecents();
        @endphp

        @if($prospects->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p class="font-medium">Aucun prospect</p>
                <p class="text-sm mt-1">Créez votre premier prospect pour commencer.</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($prospects as $prospect)
                    <a href="{{ \App\Filament\Resources\ProspectResource::getUrl('view', ['record' => $prospect]) }}" 
                       class="block p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">
                                    {{ "{$prospect->prenom} {$prospect->nom}" }}
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $prospect->societe }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs px-2 py-1 rounded {{ match($prospect->statut) {
                                    'a_contacter' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    'contacte' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                                    'en_discussion' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                                    'call_planifie' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                                    'proposition_envoyee' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
                                    'gagne' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                                    'perdu' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                } }}">
                                    {{ ucfirst(str_replace('_', ' ', $prospect->statut)) }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $prospect->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
