@extends('analytics::layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">{{ $site->name }}</h1>
            <p class="text-sm text-gray-500">{{ $site->domain }} &middot; <code class="bg-gray-100 px-1 rounded text-xs">{{ $site->tracking_id }}</code></p>
        </div>

        {{-- Date range switcher --}}
        <div class="flex gap-2">
            @foreach(['today' => 'Today', '7d' => '7 days', '30d' => '30 days', '90d' => '90 days'] as $key => $label)
                <a href="{{ request()->fullUrlWithQuery(['range' => $key]) }}"
                   class="px-3 py-1 rounded text-sm font-medium {{ $range === $key ? 'bg-indigo-600 text-white' : 'bg-white border text-gray-600 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Overview cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['Pageviews',        $overview['totalVisits'],    ''],
            ['Unique Visitors',  $overview['uniqueVisitors'], ''],
            ['Sessions',         $overview['uniqueSessions'], ''],
            ['Bounce Rate',      $overview['bounceRate'].'%', ''],
        ] as [$label, $value])
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ $label }}</p>
                <p class="text-3xl font-bold mt-1 text-gray-900">{{ number_format($value) }}</p>
            </div>
        @endforeach
    </div>

    {{-- Visits-by-day chart (simple CSS bar chart — replace with Chart.js if desired) --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Traffic over time</h2>
        @php
            $maxVisits = max(array_column($visitsByDay, 'visits') ?: [1]);
        @endphp
        <div class="flex items-end gap-1 h-32 overflow-x-auto">
            @forelse($visitsByDay as $day)
                @php $pct = round(($day['visits'] / $maxVisits) * 100); @endphp
                <div class="flex flex-col items-center flex-1 min-w-[20px] group relative" title="{{ $day['date'] }}: {{ $day['visits'] }} visits">
                    <div class="w-full bg-indigo-500 rounded-t transition-all group-hover:bg-indigo-400"
                         style="height: {{ max($pct, 2) }}%"></div>
                </div>
            @empty
                <p class="text-gray-400 text-sm">No data yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Top pages & referrers side by side --}}
    <div class="grid md:grid-cols-2 gap-4">

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Top Pages</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 text-xs uppercase">
                        <th class="pb-2 font-medium">Path</th>
                        <th class="pb-2 font-medium text-right">Views</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topPages as $page)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 truncate max-w-xs font-mono text-xs">{{ $page['path'] }}</td>
                            <td class="py-2 text-right font-semibold">{{ number_format($page['views']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="py-4 text-gray-400 text-center">No data yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Top Referrers</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 text-xs uppercase">
                        <th class="pb-2 font-medium">Domain</th>
                        <th class="pb-2 font-medium text-right">Visits</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topReferrers as $ref)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 text-xs font-mono">{{ $ref['referrer_domain'] }}</td>
                            <td class="py-2 text-right font-semibold">{{ number_format($ref['visits']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="py-4 text-gray-400 text-center">No referrers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- Device breakdown --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Devices</h2>
        <div class="flex gap-6">
            @php $totalDevices = array_sum(array_column($devices, 'count')) ?: 1; @endphp
            @foreach($devices as $d)
                @php $pct = round(($d['count'] / $totalDevices) * 100); @endphp
                <div class="flex items-center gap-3">
                    <div class="w-24 bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-sm capitalize">{{ $d['device_type'] }}</span>
                    <span class="text-sm text-gray-400">{{ $pct }}%</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Plugin Widgets --}}
    @if(count($widgets))
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($widgets as $widget)
                <div class="{{ $widget['width'] ?? 'col-span-1' }} bg-white rounded-xl border border-gray-200 p-6">
                    @if(isset($widget['label']))
                        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">{{ $widget['label'] }}</h2>
                    @endif
                    @include($widget['view'], $widget['viewData'])
                </div>
            @endforeach
        </div>
    @endif

    {{-- Tracking snippet --}}
    <details class="bg-gray-50 rounded-xl border border-gray-200 p-6">
        <summary class="cursor-pointer text-sm font-semibold text-gray-600">Tracking Snippet</summary>
        <p class="mt-3 text-sm text-gray-500">Add this before <code>&lt;/body&gt;</code> on every page:</p>
        <pre class="mt-2 bg-white border rounded p-4 text-xs overflow-x-auto"><code>&lt;script src="{{ url('/lm.js') }}" data-tid="{{ $site->tracking_id }}" async&gt;&lt;/script&gt;</code></pre>
        <p class="mt-3 text-sm text-gray-500">Custom events:</p>
        <pre class="mt-2 bg-white border rounded p-4 text-xs overflow-x-auto"><code>window.lm('event', 'Form', 'Submit', 'contact-form');</code></pre>
    </details>

</div>
@endsection
