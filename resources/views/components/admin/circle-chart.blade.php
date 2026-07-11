@props(['id', 'title', 'centerValue', 'centerLabel', 'labels', 'values', 'colors'])

<div class="card circle-chart-card" style="border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: none;">
    <div class="card-header" style="border-bottom: none; padding: 20px 20px 10px 20px; background: transparent;">
        <h3 style="font-weight: 700; color: #1e293b; font-size: 18px; margin: 0;">{{ $title }}</h3>
    </div>
    <div class="card-body" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 20px 20px 20px;">
        <div style="position: relative; width: 130px; height: 130px;">
            <canvas id="{{ $id }}"></canvas>
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                <div style="font-size: 24px; font-weight: 800; color: #1e293b; line-height: 1.2;">{{ $centerValue }}</div>
                <div style="font-size: 10px; font-weight: 700; color: #94a3b8; letter-spacing: 1px; text-transform: uppercase;">{{ $centerLabel }}</div>
            </div>
        </div>
        <div class="circle-legend" style="flex: 1; padding-left: 20px;">
            @foreach(explode(',', $labels) as $index => $label)
                <div style="text-align: right; margin-bottom: 12px;">
                    <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 4px;">
                        <span style="font-size: 11px; font-weight: 700; color: #64748b; letter-spacing: 1px; text-transform: uppercase; margin-right: 8px;">{{ trim($label) }}</span>
                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: {{ explode(',', $colors)[$index] }};"></span>
                    </div>
                    <div style="font-size: 28px; font-weight: 700; color: #1e293b; line-height: 1;">{{ explode(',', $values)[$index] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined') {
        const ctx = document.getElementById('{{ $id }}');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_map('trim', explode(',', $labels))) !!},
                    datasets: [{
                        data: {!! json_encode(array_map('trim', explode(',', $values))) !!},
                        backgroundColor: {!! json_encode(array_map('trim', explode(',', $colors))) !!},
                        borderWidth: 0,
                        hoverOffset: 4,
                        cutout: '75%',
                        borderRadius: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    }
                }
            });
        }
    }
});
</script>
