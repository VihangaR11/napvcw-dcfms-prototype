@extends('layouts.app')







@section('title', __('cases.all_cases'))







@section('page-title', __('cases.case_registry'))







@section(



    'page-description',



    __('cases.registry_description')



)







@section('content')







<div



    class="rounded-2xl



           border border-slate-800



           bg-[#111A2E]



           overflow-hidden"



>







    {{-- =========================================================



         FILTER / SEARCH BAR



    ========================================================== --}}







    <div



        class="border-b



               border-slate-800



               p-5"



    >







        <form



            method="GET"



            action="{{ route('cases.index') }}"



            class="grid grid-cols-1



                   gap-3



                   md:grid-cols-2



                   xl:grid-cols-12"



        >







            {{-- Search --}}



            <div class="md:col-span-2 xl:col-span-4">







                <label



                    for="search"



                    class="sr-only"



                >



                    {{ __('cases.search_cases') }}



                </label>







                <input



                    id="search"



                    type="text"



                    name="search"



                    value="{{ request('search') }}"



                    placeholder="{{ __('cases.search_placeholder') }}"



                    class="w-full



                           rounded-xl



                           border border-slate-700



                           bg-[#0D172A]



                           px-4 py-2.5



                           text-sm



                           text-white



                           placeholder-slate-600



                           outline-none



                           transition



                           focus:border-blue-500



                           focus:ring-2



                           focus:ring-blue-500/20"



                >







            </div>











            {{-- {{ __('cases.status') }} --}}



            <div class="xl:col-span-2">







                <label



                    for="status"



                    class="sr-only"



                >



                    {{ __('cases.filter_status') }}



                </label>







                <select



                    id="status"



                    name="status"



                    class="w-full



                           rounded-xl



                           border border-slate-700



                           bg-[#0D172A]



                           px-4 py-2.5



                           text-sm



                           text-white



                           outline-none



                           transition



                           focus:border-blue-500



                           focus:ring-2



                           focus:ring-blue-500/20"



                >



                    <option value="">



                        {{ __('cases.all_statuses') }}



                    </option>







                    @foreach([



                        'Registered',



                        'Routed',



                        'Under Processing',



                        'Awaiting Decision',



                        'Closed',



                    ] as $statusOption)







                        <option



                            value="{{ match($statusOption) {
                                'Registered' => __('cases.statuses.registered'),
                                'Routed' => __('cases.statuses.routed'),
                                'Under Processing' => __('cases.statuses.under_processing'),
                                'Awaiting Decision' => __('cases.statuses.awaiting_decision'),
                                'Closed' => __('cases.statuses.closed'),
                                default => $statusOption,
                            } }}"



                            @selected(request('status') === $statusOption)



                        >



                            {{ match($statusOption) {
                                'Registered' => __('cases.statuses.registered'),
                                'Routed' => __('cases.statuses.routed'),
                                'Under Processing' => __('cases.statuses.under_processing'),
                                'Awaiting Decision' => __('cases.statuses.awaiting_decision'),
                                'Closed' => __('cases.statuses.closed'),
                                default => $statusOption,
                            } }}



                        </option>







                    @endforeach



                </select>







            </div>











            {{-- {{ __('cases.division') }} --}}



            <div class="xl:col-span-2">







                <label



                    for="division"



                    class="sr-only"



                >



                    {{ __('cases.filter_division') }}



                </label>







                <select



                    id="division"



                    name="division"



                    class="w-full



                           rounded-xl



                           border border-slate-700



                           bg-[#0D172A]



                           px-4 py-2.5



                           text-sm



                           text-white



                           outline-none



                           transition



                           focus:border-blue-500



                           focus:ring-2



                           focus:ring-blue-500/20"



                >



                    <option value="">



                        {{ __('cases.all_divisions') }}



                    </option>







                    @foreach([



                        'Law and Law Enforcement',



                        'Protection Services',



                        'Police Protection',



                        'Assistance Services',



                    ] as $divisionOption)







                        <option



                            value="{{ match($divisionOption) {
                                'Law and Law Enforcement' => __('cases.divisions.law_enforcement'),
                                'Protection Services' => __('cases.divisions.protection_services'),
                                'Police Protection' => __('cases.divisions.police_protection'),
                                'Assistance Services' => __('cases.divisions.assistance_services'),
                                default => $divisionOption,
                            } }}"



                            @selected(request('division') === $divisionOption)



                        >



                            {{ match($divisionOption) {
                                'Law and Law Enforcement' => __('cases.divisions.law_enforcement'),
                                'Protection Services' => __('cases.divisions.protection_services'),
                                'Police Protection' => __('cases.divisions.police_protection'),
                                'Assistance Services' => __('cases.divisions.assistance_services'),
                                default => $divisionOption,
                            } }}



                        </option>







                    @endforeach



                </select>







            </div>











            {{-- {{ __('cases.urgency') }} --}}



            <div class="xl:col-span-2">







                <label



                    for="urgency"



                    class="sr-only"



                >



                    {{ __('cases.filter_urgency') }}



                </label>







                <select



                    id="urgency"



                    name="urgency"



                    class="w-full



                           rounded-xl



                           border border-slate-700



                           bg-[#0D172A]



                           px-4 py-2.5



                           text-sm



                           text-white



                           outline-none



                           transition



                           focus:border-blue-500



                           focus:ring-2



                           focus:ring-blue-500/20"



                >



                    <option value="">



                        {{ __('cases.all_urgencies') }}



                    </option>







                    @foreach([



                        'Critical',



                        'Urgent',



                        'Normal',



                    ] as $urgencyOption)







                        <option



                            value="{{ match($urgencyOption) {
                                'Critical' => __('cases.urgencies.critical'),
                                'Urgent' => __('cases.urgencies.urgent'),
                                'Normal' => __('cases.urgencies.normal'),
                                default => $urgencyOption,
                            } }}"



                            @selected(request('urgency') === $urgencyOption)



                        >



                            {{ match($urgencyOption) {
                                'Critical' => __('cases.urgencies.critical'),
                                'Urgent' => __('cases.urgencies.urgent'),
                                'Normal' => __('cases.urgencies.normal'),
                                default => $urgencyOption,
                            } }}



                        </option>







                    @endforeach



                </select>







            </div>











            {{-- Threat {{ __('cases.status') }} --}}



            <div class="xl:col-span-2">







                <label



                    for="threat_status"



                    class="sr-only"



                >



                    {{ __('cases.filter_threat_status') }}



                </label>







                <select



                    id="threat_status"



                    name="threat_status"



                    class="w-full



                           rounded-xl



                           border border-slate-700



                           bg-[#0D172A]



                           px-4 py-2.5



                           text-sm



                           text-white



                           outline-none



                           transition



                           focus:border-blue-500



                           focus:ring-2



                           focus:ring-blue-500/20"



                >



                    <option value="">



                        {{ __('cases.all_threat_levels') }}



                    </option>







                    @foreach([



                        'Very High',



                        'High',



                        'Low',



                        'Very Low',



                    ] as $threatOption)







                        <option



                            value="{{ match($threatOption) {
                                'Very High' => __('cases.threat_levels.very_high'),
                                'High' => __('cases.threat_levels.high'),
                                'Low' => __('cases.threat_levels.low'),
                                'Very Low' => __('cases.threat_levels.very_low'),
                                default => $threatOption,
                            } }}"



                            @selected(request('threat_status') === $threatOption)



                        >



                            {{ match($threatOption) {
                                'Very High' => __('cases.threat_levels.very_high'),
                                'High' => __('cases.threat_levels.high'),
                                'Low' => __('cases.threat_levels.low'),
                                'Very Low' => __('cases.threat_levels.very_low'),
                                default => $threatOption,
                            } }}



                        </option>







                    @endforeach



                </select>







            </div>











            {{-- Buttons --}}



            <div



                class="flex flex-wrap gap-3



                       md:col-span-2



                       xl:col-span-12"



            >







                <button



                    type="submit"



                    class="rounded-xl



                           bg-blue-600



                           px-5 py-2.5



                           text-sm



                           font-medium



                           text-white



                           transition



                           hover:bg-blue-500"



                >



                    {{ __('cases.search_filter') }}



                </button>







                @if(



                    request()->filled('search')



                    || request()->filled('status')



                    || request()->filled('division')



                    || request()->filled('urgency')



                    || request()->filled('threat_status')



                )







                    <a



                        href="{{ route('cases.index') }}"



                        class="inline-flex



                               items-center



                               justify-center



                               rounded-xl



                               border border-slate-700



                               bg-[#0D172A]



                               px-5 py-2.5



                               text-sm



                               font-medium



                               text-slate-300



                               transition



                               hover:bg-[#152238]



                               hover:text-white"



                    >



                        {{ __('cases.clear_filters') }}



                    </a>







                @endif







            </div>







        </form>







    </div>











    {{-- =========================================================



         CASE TABLE



    ========================================================== --}}







    <div class="overflow-x-auto">







        <table class="w-full min-w-[1350px] text-left">







            <thead



                class="border-b



                       border-slate-800



                       bg-[#0D172A]"



            >







                <tr



                    class="text-xs



                           uppercase



                           tracking-wider



                           text-slate-500"



                >







                    <th class="px-5 py-4">



                        {{ __('cases.case_number') }}



                    </th>







                    <th class="px-5 py-4">



                        {{ __('cases.category') }}



                    </th>







                    <th class="px-5 py-4">



                        {{ __('cases.division') }}



                    </th>







                    <th class="px-5 py-4">



                        {{ __('cases.status') }}



                    </th>







                    <th class="px-5 py-4">



                        {{ __('cases.urgency') }}



                    </th>







                    <th class="px-5 py-4">



                        Threat {{ __('cases.status') }}



                    </th>







                    <th class="px-5 py-4">



                        {{ __('cases.case_aging_activity') }}



                    </th>







                    <th class="px-5 py-4">



                        {{ __('cases.received') }}



                    </th>







                    <th class="px-5 py-4 text-right">



                        {{ __('cases.action') }}



                    </th>







                </tr>







            </thead>











            <tbody>







                @forelse($cases as $case)







                    @php



                        /*



                        |--------------------------------------------------------------------------



                        | Case {{ __('cases.status') }} Badge



                        |--------------------------------------------------------------------------



                        */







                        $statusBadge = match(



                            $case->current_status



                        ) {



                            'Registered' =>



                                'border-slate-600 bg-slate-500/10 text-slate-300',







                            'Routed' =>



                                'border-blue-500/20 bg-blue-500/10 text-blue-300',







                            'Under Processing' =>



                                'border-yellow-500/20 bg-yellow-500/10 text-yellow-300',







                            'Awaiting Decision' =>



                                'border-purple-500/20 bg-purple-500/10 text-purple-300',







                            'Closed' =>



                                'border-green-500/20 bg-green-500/10 text-green-300',







                            default =>



                                'border-slate-700 bg-slate-500/10 text-slate-300',



                        };











                        /*



                        |--------------------------------------------------------------------------



                        | {{ __('cases.urgency') }} Badge



                        |--------------------------------------------------------------------------



                        */







                        $urgencyBadge = match(



                            $case->urgency



                        ) {



                            'Critical' =>



                                'border-red-500/20 bg-red-500/10 text-red-300',







                            'Urgent' =>



                                'border-orange-500/20 bg-orange-500/10 text-orange-300',







                            default =>



                                'border-slate-700 bg-slate-500/10 text-slate-300',



                        };











                        /*



                        |--------------------------------------------------------------------------



                        | Threat Assessment Badge



                        |--------------------------------------------------------------------------



                        */







                        $threatBadge = match(



                            $case->threat_assessment_status



                        ) {



                            'Very High' =>



                                'border-red-500/30 bg-red-500/10 text-red-300',







                            'High' =>



                                'border-orange-500/30 bg-orange-500/10 text-orange-300',







                            'Low' =>



                                'border-yellow-500/30 bg-yellow-500/10 text-yellow-300',







                            'Very Low' =>



                                'border-green-500/30 bg-green-500/10 text-green-300',







                            default =>



                                'border-slate-700 bg-slate-500/10 text-slate-400',



                        };











                        /*

                        |--------------------------------------------------------------------------

                        | {{ __('cases.case_aging_activity') }} Badge

                        |--------------------------------------------------------------------------

                        */



                        $aging =

                            $case->aging_summary

                            ?? null;



                        $activityBadge = match(

                            $aging['activity_status']

                            ?? 'active'

                        ) {

                            'follow_up' =>

                                'border-orange-500/30 bg-orange-500/10 text-orange-300',



                            'closed' =>

                                'border-slate-700 bg-slate-500/10 text-slate-400',



                            default =>

                                'border-green-500/30 bg-green-500/10 text-green-300',

                        };



                    
                        $statusLabel = match($case->current_status) {
                            'Registered' => __('cases.statuses.registered'),
                            'Routed' => __('cases.statuses.routed'),
                            'Under Processing' => __('cases.statuses.under_processing'),
                            'Awaiting Decision' => __('cases.statuses.awaiting_decision'),
                            'Closed' => __('cases.statuses.closed'),
                            default => $case->current_status,
                        };

                        $urgencyLabel = match($case->urgency) {
                            'Critical' => __('cases.urgencies.critical'),
                            'Urgent' => __('cases.urgencies.urgent'),
                            'Normal' => __('cases.urgencies.normal'),
                            default => $case->urgency,
                        };

                        $threatLabel = match($case->threat_assessment_status) {
                            'Very High' => __('cases.threat_levels.very_high'),
                            'High' => __('cases.threat_levels.high'),
                            'Low' => __('cases.threat_levels.low'),
                            'Very Low' => __('cases.threat_levels.very_low'),
                            default => $case->threat_assessment_status,
                        };

                        $divisionLabel = match($case->primary_division) {
                            'Law and Law Enforcement' => __('cases.divisions.law_enforcement'),
                            'Protection Services' => __('cases.divisions.protection_services'),
                            'Police Protection' => __('cases.divisions.police_protection'),
                            'Assistance Services' => __('cases.divisions.assistance_services'),
                            null => __('cases.not_routed'),
                            default => $case->primary_division,
                        };

                        $activityLabel = match($aging['activity_status'] ?? null) {
                            'follow_up' => __('cases.activity.follow_up_review_suggested'),
                            'closed' => __('cases.activity.closed'),
                            'active' => __('cases.activity.active'),
                            default => $aging['label'] ?? __('cases.not_available'),
                        };

@endphp











                    <tr



                        class="border-b



                               border-slate-800/70



                               transition



                               hover:bg-[#152238]"



                    >







                        {{-- {{ __('cases.case_number') }} --}}



                        <td class="px-5 py-4">







                            <a



                                href="{{ route(



                                    'cases.show',



                                    $case



                                ) }}"



                                class="text-sm



                                       font-semibold



                                       text-blue-300



                                       transition



                                       hover:text-blue-200"



                            >



                                {{ $case->case_number }}



                            </a>







                        </td>











                        {{-- {{ __('cases.category') }} --}}



                        <td



                            class="px-5 py-4



                                   text-sm



                                   text-slate-300"



                        >



                            {{ $case->complaint_category }}



                        </td>











                        {{-- Primary {{ __('cases.division') }} --}}



                        <td



                            class="px-5 py-4



                                   text-sm



                                   text-slate-400"



                        >



                            {{ $divisionLabel }}



                        </td>











                        {{-- Current {{ __('cases.status') }} --}}



                        <td class="px-5 py-4">







                            <span



                                class="inline-flex



                                       rounded-full



                                       border



                                       px-3 py-1



                                       text-xs



                                       font-medium



                                       {{ $statusBadge }}"



                            >



                                {{ $statusLabel }}



                            </span>







                        </td>











                        {{-- {{ __('cases.urgency') }} --}}



                        <td class="px-5 py-4">







                            <span



                                class="inline-flex



                                       rounded-full



                                       border



                                       px-2.5 py-1



                                       text-[10px]



                                       font-medium



                                       {{ $urgencyBadge }}"



                            >



                                {{ $urgencyLabel }}



                            </span>







                        </td>











                        {{-- Threat Assessment {{ __('cases.status') }} --}}



                        <td class="px-5 py-4">







                            @if($case->threat_assessment_status)







                                <span



                                    class="inline-flex



                                           items-center



                                           gap-1.5



                                           rounded-full



                                           border



                                           px-2.5 py-1



                                           text-[10px]



                                           font-medium



                                           {{ $threatBadge }}"



                                >







                                    <span



                                        class="h-1.5 w-1.5



                                               rounded-full



                                               bg-current"



                                    ></span>







                                    {{ $threatLabel }}







                                </span>







                            @else







                                <span



                                    class="inline-flex



                                           rounded-full



                                           border border-slate-800



                                           bg-slate-500/[0.05]



                                           px-2.5 py-1



                                           text-[10px]



                                           text-slate-600"



                                >



                                    {{ __('cases.not_assessed') }}



                                </span>







                            @endif







                        </td>











                        {{-- {{ __('cases.case_aging_activity') }} --}}

                        <td class="px-5 py-4">



                            @if($aging)



                                <div class="space-y-1.5">



                                    <span

                                        class="inline-flex

                                               items-center

                                               rounded-full

                                               border

                                               px-2.5 py-1

                                               text-[10px]

                                               font-semibold

                                               {{ $activityBadge }}"

                                    >

                                        {{ $activityLabel }}

                                    </span>



                                    <p class="text-[10px] text-slate-500">

                                        {{ __('cases.case_age') }}:

                                        {{ $aging['age_days'] }}

                                        {{ $aging['age_days'] === 1 ? __('cases.day') : __('cases.days') }}

                                    </p>



                                    @if($aging['last_activity_at'])



                                        <p class="text-[10px] text-slate-500">

                                            {{ __('cases.last_activity') }}:

                                            {{ $aging['last_activity_at']->format('d M Y, h:i A') }}

                                        </p>



                                    @endif



                                    @if($aging['requires_follow_up'])



                                        <p

                                            class="inline-flex

                                                   items-center

                                                   gap-1

                                                   text-[10px]

                                                   font-medium

                                                   text-orange-300"

                                        >

                                            <span>⚠</span>



                                            {{ __('cases.no_recorded_activity_for') }}

                                            {{ $aging['inactive_days'] }}

                                            {{ $aging['inactive_days'] === 1 ? __('cases.day') : __('cases.days') }}

                                        </p>



                                    @endif



                                </div>



                            @else



                                <span class="text-xs text-slate-600">

                                    {{ __('cases.not_available') }}

                                </span>



                            @endif



                        </td>





                        {{-- {{ __('cases.received') }} Date --}}



                        <td



                            class="px-5 py-4



                                   text-sm



                                   text-slate-400



                                   whitespace-nowrap"



                        >



                            {{ $case->received_date?->format('d M Y') ?? '—' }}



                        </td>











                        {{-- {{ __('cases.action') }} --}}



                        <td



                            class="px-5 py-4



                                   text-right"



                        >







                            <a



                                href="{{ route(



                                    'cases.show',



                                    $case



                                ) }}"



                                class="inline-flex



                                       items-center



                                       gap-2



                                       rounded-lg



                                       border border-blue-500/20



                                       bg-blue-500/10



                                       px-3 py-2



                                       text-xs



                                       font-medium



                                       text-blue-300



                                       transition



                                       hover:bg-blue-500/20"



                            >



                                {{ __('cases.view') }}







                                <span>



                                    →



                                </span>







                            </a>







                        </td>







                    </tr>







                @empty







                    <tr>







                        <td



                            colspan="9"



                            class="px-5



                                   py-14



                                   text-center"



                        >







                            <div



                                class="mx-auto



                                       flex h-12 w-12



                                       items-center



                                       justify-center



                                       rounded-xl



                                       border border-slate-800



                                       bg-[#0D172A]



                                       text-slate-500"



                            >



                                ▤



                            </div>











                            <p



                                class="mt-4



                                       text-sm



                                       font-medium



                                       text-slate-300"



                            >



                                {{ __('cases.no_cases_found') }}



                            </p>











                            <p



                                class="mt-1



                                       text-xs



                                       text-slate-500"



                            >







                                @if(



                                    request()->filled('search')



                                    || request()->filled('status')



                                    || request()->filled('division')



                                    || request()->filled('urgency')



                                    || request()->filled('threat_status')



                                )







                                    {{ __('cases.no_cases_match_filters') }}







                                @else







                                    {{ __('cases.no_cases_registered') }}







                                @endif







                            </p>







                        </td>







                    </tr>







                @endforelse







            </tbody>







        </table>







    </div>











    {{-- =========================================================



         PAGINATION



    ========================================================== --}}







    @if($cases->hasPages())







        <div



            class="border-t



                   border-slate-800



                   p-5"



        >



            {{ $cases->links() }}



        </div>







    @endif







</div>







@endsection