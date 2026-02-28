<x-filament-widgets::widget class="fi-wi">
    <x-filament::section>
        <x-slot name="heading">
            ✨ Contenus publiés récemment
        </x-slot>
        <x-slot name="description">
            Les 5 derniers contenus publiés
        </x-slot>

        @php
            $contenus = $this->getContenus();
        @endphp

        @if($contenus->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p class="font-medium">Aucun contenu publié</p>
                <p class="text-sm mt-1">Vos contenus publiés apparaîtront ici.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($contenus as $contenu)
                    <a href="{{ \App\Filament\Resources\ContenuResource::getUrl('view', ['record' => $contenu]) }}" 
                       class="block p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                        {{ $contenu->date_publication_reelle->format('d/m/Y') }}
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
                                @if($contenu->url_publication)
                                    <a href="{{ $contenu->url_publication }}" target="_blank" 
                                       class="text-xs text-primary-600 dark:text-primary-400 hover:underline mt-1 inline-block"
                                       onclick="event.stopPropagation();">
                                        Voir le contenu →
                                    </a>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
