<x-filament-widgets::widget class="fi-wi">
    <x-filament::section>
        <x-slot name="heading">
            📞 Prochains calls prévus
        </x-slot>
        <x-slot name="description">
            Les 5 prochains calls planifiés
        </x-slot>

        @php
            $calls = $this->getCalls();
        @endphp

        @if($calls->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p class="font-medium">Aucun call prévu</p>
                <p class="text-sm mt-1">Planifiez vos premiers calls depuis les fiches prospects.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($calls as $call)
                    <a href="{{ \App\Filament\Resources\CallResource::getUrl('view', ['record' => $call]) }}" 
                       class="block p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $call->prospect ? "{$call->prospect->prenom} {$call->prospect->nom}" : '-' }}
                                    </span>
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $call->date_planifiee->isToday() ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : ($call->date_planifiee->isTomorrow() ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400') }}">
                                        {{ $call->date_planifiee->isToday() ? 'Aujourd\'hui' : ($call->date_planifiee->isTomorrow() ? 'Demain' : $call->date_planifiee->format('d/m')) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                    {{ $call->prospect?->societe }}
                                </p>
                                @if($call->objectif_call)
                                    <p class="text-xs text-gray-500 dark:text-gray-500 line-clamp-1">
                                        {{ \Illuminate\Support\Str::limit($call->objectif_call, 60) }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $call->date_planifiee->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
