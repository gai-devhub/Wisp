@props(['id', 'title', 'centerValue', 'centerLabel', 'labels', 'values', 'colors'])

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col h-full">
    <h3 class="font-bold text-slate-800 text-[13px] mb-4">{{ $title }}</h3>
    
    <div class="relative w-24 h-24 mx-auto mb-6 flex-shrink-0">
        <canvas id="{{ $id }}"></canvas>
        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-1">
            <div class="text-[15px] font-extrabold text-slate-800 leading-none mb-1">{{ $centerValue }}</div>
            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $centerLabel }}</div>
        </div>
    </div>

    <div class="mt-auto w-full flex flex-col gap-2.5">
        @foreach(explode(',', $labels) as $index => $label)
            <div class="flex justify-between items-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                <div class="flex items-center gap-2">
                    <span class="block w-2 h-2 rounded-full" style="background-color: {{ explode(',', $colors)[$index] }};"></span>
                    {{ trim($label) }}
                </div>
                <span class="text-slate-800 text-[11px]">{{ explode(',', $values)[$index] }}</span>
            </div>
        @endforeach
    </div>
</div>
