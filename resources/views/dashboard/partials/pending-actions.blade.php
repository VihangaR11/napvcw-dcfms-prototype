@if(
    isset($pendingActions) &&
    $pendingActions->isNotEmpty()
)

    <div
        class="mb-6
               rounded-2xl
               border border-slate-800
               bg-[#111A2E]
               overflow-hidden"
    >

        <div
            class="flex items-center justify-between
                   border-b border-slate-800
                   px-5 py-4"
        >

            <div>

                <h3 class="font-semibold text-white">
                    Pending Actions
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Items requiring your attention based on your role.
                </p>

            </div>

            <span
                class="rounded-full
                       border border-blue-500/20
                       bg-blue-500/10
                       px-3 py-1
                       text-xs font-semibold
                       text-blue-300"
            >
                {{ $pendingActions->count() }}
            </span>

        </div>


        <div class="divide-y divide-slate-800">

            @foreach($pendingActions as $item)

                @php
                    $priorityStyle =
                        match($item['priority']) {
                            'critical' =>
                                'border-red-500/20 bg-red-500/[0.06] text-red-300',

                            'high' =>
                                'border-orange-500/20 bg-orange-500/[0.06] text-orange-300',

                            default =>
                                'border-blue-500/20 bg-blue-500/[0.06] text-blue-300',
                        };
                @endphp


                <div
                    class="flex flex-col gap-4
                           px-5 py-4
                           lg:flex-row
                           lg:items-center
                           lg:justify-between"
                >

                    <div class="flex items-start gap-4">

                        <span
                            class="mt-0.5
                                   inline-flex
                                   rounded-full
                                   border
                                   px-2.5 py-1
                                   text-[10px]
                                   font-semibold
                                   uppercase
                                   {{ $priorityStyle }}"
                        >
                            {{ $item['priority'] }}
                        </span>


                        <div>

                            <p class="text-sm font-semibold text-white">
                                {{ $item['title'] }}
                            </p>

                            <p class="mt-1 text-xs leading-relaxed text-slate-400">
                                {{ $item['description'] }}
                            </p>

                            @if($item['case_number'])

                                <p class="mt-2 text-[10px] text-slate-600">
                                    {{ $item['case_number'] }}
                                </p>

                            @endif

                        </div>

                    </div>


                    <a
                        href="{{ $item['action_url'] }}"
                        class="inline-flex shrink-0
                               items-center justify-center
                               rounded-xl
                               border border-blue-500/20
                               bg-blue-500/10
                               px-4 py-2.5
                               text-xs font-semibold
                               text-blue-300
                               transition
                               hover:bg-blue-500/20"
                    >
                        {{ $item['action_label'] }}
                        <span class="ml-2">→</span>
                    </a>

                </div>

            @endforeach

        </div>

    </div>

@endif