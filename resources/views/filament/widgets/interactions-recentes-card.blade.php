<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            💬 Interactions récentes
        </x-slot>
        <x-slot name="description">
            Les 5 dernières interactions
        </x-slot>

        @php
            $interactions = $this->getInteractionsRecentes();
        @endphp

        @if($interactions->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p class="font-medium">Aucune interaction</p>
                <p class="text-sm mt-1">Les interactions apparaîtront ici.</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($interactions as $interaction)
                    <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">
                                    {{ $interaction->prospect ? "{$interaction->prospect->prenom} {$interaction->prospect->nom}" : '-' }}
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    {{ ucfirst(str_replace('_', ' ', $interaction->type)) }}
                                    @if($interaction->resume)
                                        - {{ \Illuminate\Support\Str::limit($interaction->resume, 40) }}
                                    @endif
                                </p>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $interaction->date->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
