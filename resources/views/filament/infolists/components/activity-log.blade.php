@php
    try {
        $record = $getRecord();
    } catch (\Exception $e) {
        $record = null;
    }
    
    if (!$record) {
        $activities = collect();
    } else {
        $activities = \Spatie\Activitylog\Models\Activity::where('subject_type', get_class($record))
            ->where('subject_id', $record->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }
@endphp

@if($activities->isEmpty())
    <div class="text-sm text-gray-500 dark:text-gray-400 p-4 text-center">
        Aucune modification enregistrée
    </div>
@else
    <div class="space-y-4">
        @foreach($activities as $activity)
            <div class="border-l-4 border-gray-300 dark:border-gray-600 pl-4 py-2">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                {{ $activity->description }}
                            </span>
                            @if($activity->causer)
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    par {{ $activity->causer->name ?? 'Système' }}
                                </span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                            {{ $activity->created_at->format('d/m/Y à H:i') }}
                        </div>
                        @if($activity->properties && $activity->properties->has('attributes'))
                            <div class="mt-2 space-y-1">
                                @foreach($activity->properties->get('attributes') as $key => $value)
                                    @php
                                        $oldValue = $activity->properties->get('old')[$key] ?? null;
                                    @endphp
                                    @if($oldValue !== $value)
                                        <div class="text-xs bg-gray-50 dark:bg-gray-800 p-2 rounded">
                                            <div class="font-semibold text-gray-700 dark:text-gray-300">{{ $key }}:</div>
                                            @if($oldValue)
                                                <div class="text-red-600 dark:text-red-400 line-through">
                                                    {{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}
                                                </div>
                                            @endif
                                            <div class="text-green-600 dark:text-green-400">
                                                {{ is_array($value) ? json_encode($value) : $value }}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
