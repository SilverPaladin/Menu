@props(['screen'])

@php
    $screen->loadMissing('columns.collection.items');
@endphp

<div {{ $attributes->merge(['class' => 'aspect-video w-full overflow-hidden rounded-lg bg-zinc-950 border border-zinc-700 flex']) }}>
    @forelse ($screen->columns as $column)
        <div class="flex-1 flex flex-col items-center gap-1 p-2 min-w-0 border-r border-zinc-800 last:border-r-0">
            @if ($column->collection)
                @if ($column->collection->image)
                    <img src="{{ Storage::url($column->collection->image) }}" alt="{{ $column->collection->name }}"
                        class="h-8 w-full object-contain shrink-0">
                @else
                    <div class="h-8 w-full flex items-center justify-center text-[9px] font-bold text-zinc-300 truncate">
                        {{ $column->collection->name }}
                    </div>
                @endif

                @if ($column->collection->puff_count)
                    <div class="text-[8px] font-bold text-zinc-100 leading-tight truncate max-w-full">{{ $column->collection->puff_count }}</div>
                @endif
                @if ($column->collection->volume)
                    <div class="text-[8px] text-zinc-300 leading-tight truncate max-w-full">{!! $column->collection->volume !!}</div>
                @endif

                @php
                    $activeItems = $column->collection->items->where('active', true);
                @endphp
                <div class="flex-1 w-full text-center space-y-0.5 overflow-hidden mt-1">
                    @forelse ($activeItems->take(6) as $item)
                        <div class="text-[8px] text-zinc-200 leading-tight truncate">{{ $item->name }}</div>
                    @empty
                        <div class="text-[9px] font-bold text-red-500">Out of Stock</div>
                    @endforelse
                    @if ($activeItems->count() > 6)
                        <div class="text-[8px] text-zinc-500">+{{ $activeItems->count() - 6 }} more</div>
                    @endif
                </div>
            @else
                <div class="flex-1 w-full flex items-center justify-center border border-dashed border-zinc-700 rounded">
                    <span class="text-[9px] text-zinc-500">No collection</span>
                </div>
            @endif
        </div>
    @empty
        <div class="flex-1 flex items-center justify-center">
            <span class="text-[10px] text-zinc-500">No columns configured</span>
        </div>
    @endforelse
</div>
