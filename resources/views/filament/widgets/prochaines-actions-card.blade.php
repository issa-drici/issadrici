<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            📋 Prochaines actions
        </x-slot>
        <x-slot name="description">
            Actions à effectuer dans les 7 prochains jours
        </x-slot>

        @php
            $actions = $this->getActions();
        @endphp

        @if($actions->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p class="font-medium">Aucune action prévue</p>
                <p class="text-sm mt-1">Définissez des prochaines actions sur vos prospects ou planifiez vos contenus.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($actions as $action)
                    <a href="{{ $action['url'] }}" 
                       class="block p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 transition-colors">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    @if($action['type'] === 'contenu')
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400">
                                            📝 Contenu
                                        </span>
                                    @else
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                            👤 Prospect
                                        </span>
                                    @endif
                                </div>
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">
                                    {{ $action['titre'] }}
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $action['sous_titre'] }}
                                </p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded {{ $action['date']->isPast() ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : ($action['date']->isToday() ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400') }}">
                                {{ $action['date']->format('d/m') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-700 dark:text-gray-300 line-clamp-2">
                            {{ $action['action'] }}
                        </p>
                    </a>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
