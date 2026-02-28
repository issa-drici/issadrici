<x-filament-widgets::widget class="fi-wi">
    <x-filament::section>
        <x-slot name="heading">
            📅 Contenus planifiés
        </x-slot>
        <x-slot name="description">
            Les 5 prochains contenus à publier
        </x-slot>

        @php
            $contenus = $this->getContenus();
        @endphp

        @if($contenus->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p class="font-medium">Aucun contenu planifié</p>
                <p class="text-sm mt-1">Créez et planifiez vos contenus depuis la section Contenu.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($contenus as $contenu)
                    <a href="{{ \App\Filament\Resources\ContenuResource::getUrl('view', ['record' => $contenu]) }}" 
                       class="block p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $contenu->date_publication_planifiee->isToday() ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : ($contenu->date_publication_planifiee->isTomorrow() ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400') }}">
                                        {{ $contenu->date_publication_planifiee->isToday() ? 'Aujourd\'hui' : ($contenu->date_publication_planifiee->isTomorrow() ? 'Demain' : $contenu->date_publication_planifiee->format('d/m')) }}
                                    </span>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        {{ match($contenu->plateforme) {
                                            'linkedin' => 'LinkedIn',
                                            'instagram' => 'Instagram',
                                            'facebook' => 'Facebook',
                                            'twitter' => 'Twitter/X',
                                            'tiktok' => 'TikTok',
                                            'youtube' => 'YouTube',
                                            default => $contenu->plateforme,
                                        } }}
                                    </span>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-primary-100 text-primary-800 dark:bg-primary-900/20 dark:text-primary-400">
                                        {{ match($contenu->type) {
                                            'post' => 'Post',
                                            'story' => 'Story',
                                            'reel' => 'Reel',
                                            'article' => 'Article',
                                            'video' => 'Vidéo',
                                            'carousel' => 'Carousel',
                                            default => $contenu->type,
                                        } }}
                                    </span>
                                </div>
                                @if($contenu->titre)
                                    <p class="font-semibold text-gray-900 dark:text-white mb-1">
                                        {{ \Illuminate\Support\Str::limit($contenu->titre, 50) }}
                                    </p>
                                @endif
                                @if($contenu->contenu)
                                    <p class="text-xs text-gray-500 dark:text-gray-500 line-clamp-2">
                                        {{ \Illuminate\Support\Str::limit($contenu->contenu, 80) }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $contenu->date_publication_planifiee->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
