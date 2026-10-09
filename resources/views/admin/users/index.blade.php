@extends('layouts.app')



@section('title', __('admin_users.title'))



@section('page-title', __('admin_users.page_title'))



@section(

    'page-description',

    __('admin_users.page_description')

)



@section('content')



{{-- =========================================================

     STATUS SUMMARY

\========================================================= --}}

<div

    class="grid grid-cols-2

           lg:grid-cols-4

           gap-4

           mb-6"

>



    <a

        href="{{ route('admin.users.index', ['status' => 'pending']) }}"

        class="rounded-2xl

               border

               {{ $status === 'pending'

                    ? 'border-yellow-500/30 bg-yellow-500/10'

                    : 'border-slate-800 bg-[#111A2E]'

               }}

               p-5

               transition

               hover:border-yellow-500/30"

    >

        <p

            class="text-[10px]

                   uppercase

                   tracking-wider

                   text-slate-500"

        >

            {{ __('admin_users.statuses.pending') }}

        </p>



        <p

            class="mt-2

                   text-3xl

                   font-semibold

                   text-yellow-300"

        >

            {{ $counts['pending'] }}

        </p>



        <p class="mt-2 text-xs text-slate-500">

            {{ __('admin_users.awaiting_review') }}

        </p>

    </a>





    <a

        href="{{ route('admin.users.index', ['status' => 'active']) }}"

        class="rounded-2xl

               border

               {{ $status === 'active'

                    ? 'border-green-500/30 bg-green-500/10'

                    : 'border-slate-800 bg-[#111A2E]'

               }}

               p-5

               transition

               hover:border-green-500/30"

    >

        <p

            class="text-[10px]

                   uppercase

                   tracking-wider

                   text-slate-500"

        >

            {{ __('admin_users.statuses.active') }}

        </p>



        <p

            class="mt-2

                   text-3xl

                   font-semibold

                   text-green-300"

        >

            {{ $counts['active'] }}

        </p>



        <p class="mt-2 text-xs text-slate-500">

            {{ __('admin_users.authorized_users') }}

        </p>

    </a>





    <a

        href="{{ route('admin.users.index', ['status' => 'rejected']) }}"

        class="rounded-2xl

               border

               {{ $status === 'rejected'

                    ? 'border-red-500/30 bg-red-500/10'

                    : 'border-slate-800 bg-[#111A2E]'

               }}

               p-5

               transition

               hover:border-red-500/30"

    >

        <p

            class="text-[10px]

                   uppercase

                   tracking-wider

                   text-slate-500"

        >

            {{ __('admin_users.statuses.rejected') }}

        </p>



        <p

            class="mt-2

                   text-3xl

                   font-semibold

                   text-red-300"

        >

            {{ $counts['rejected'] }}

        </p>



        <p class="mt-2 text-xs text-slate-500">

            {{ __('admin_users.rejected_requests') }}

        </p>

    </a>





    <a

        href="{{ route('admin.users.index', ['status' => 'suspended']) }}"

        class="rounded-2xl

               border

               {{ $status === 'suspended'

                    ? 'border-orange-500/30 bg-orange-500/10'

                    : 'border-slate-800 bg-[#111A2E]'

               }}

               p-5

               transition

               hover:border-orange-500/30"

    >

        <p

            class="text-[10px]

                   uppercase

                   tracking-wider

                   text-slate-500"

        >

            {{ __('admin_users.statuses.suspended') }}

        </p>



        <p

            class="mt-2

                   text-3xl

                   font-semibold

                   text-orange-300"

        >

            {{ $counts['suspended'] }}

        </p>



        <p class="mt-2 text-xs text-slate-500">

            {{ __('admin_users.access_disabled') }}

        </p>

    </a>



</div>





{{-- =========================================================

     USER TABLE

\========================================================= --}}

<div

    class="overflow-hidden

           rounded-2xl

           border border-slate-800

           bg-[#111A2E]"

>



    <div

        class="flex flex-col gap-3

               border-b border-slate-800

               p-6

               md:flex-row

               md:items-center

               md:justify-between"

    >



        <div>

            <h3 class="text-lg font-semibold text-white">



                @if($status === 'pending')

                    {{ __('admin_users.pending_account_requests') }}



                @elseif($status === 'active')

                    {{ __('admin_users.active_user_accounts') }}



                @elseif($status === 'rejected')

                    {{ __('admin_users.rejected_account_requests') }}



                @else

                    {{ __('admin_users.suspended_user_accounts') }}

                @endif



            </h3>



            <p class="mt-1 text-sm text-slate-500">



                @if($status === 'pending')

                    {{ __('admin_users.pending_desc') }}



                @elseif($status === 'active')

                    {{ __('admin_users.active_desc') }}



                @elseif($status === 'rejected')

                    {{ __('admin_users.rejected_desc') }}



                @else

                    {{ __('admin_users.suspended_desc') }}

                @endif



            </p>

        </div>



        <span

            class="w-fit

                   rounded-full

                   border border-slate-700

                   bg-[#0D172A]

                   px-3 py-1

                   text-xs

                   text-slate-400"

        >

            {{ $users->total() }}

            {{ $users->total() === 1 ? __('admin_users.account_singular') : __('admin_users.account_plural') }}

        </span>



    </div>





    <div class="overflow-x-auto">



        <table class="w-full text-left">



            <thead

                class="border-b border-slate-800

                       bg-[#0D172A]"

            >

                <tr

                    class="text-[10px]

                           uppercase

                           tracking-wider

                           text-slate-500"

                >

                    <th class="px-6 py-4">

                        {{ __('admin_users.employee') }}

                    </th>



                    <th class="px-6 py-4">

                        {{ __('admin_users.designation') }}

                    </th>



                    <th class="px-6 py-4">

                        {{ __('admin_users.division') }}

                    </th>



                    <th class="px-6 py-4">

                        {{ __('admin_users.role') }}

                    </th>



                    <th class="px-6 py-4">

                        {{ __('admin_users.registered') }}

                    </th>



                    <th class="px-6 py-4 text-right">

                        {{ __('admin_users.actions') }}

                    </th>

                </tr>

            </thead>





            <tbody>



                @forelse($users as $account)



                    <tr

                        class="border-b

                               border-slate-800/70

                               align-top"

                    >



                        {{-- Employee --}}

                        <td class="px-6 py-5">



                            <p

                                class="text-sm

                                       font-semibold

                                       text-white"

                            >

                                {{ $account->name }}

                            </p>



                            <p

                                class="mt-1

                                       text-xs

                                       font-medium

                                       text-blue-400"

                            >

                                {{ __('admin_users.epf') }}:

                                {{ $account->employee_number }}

                            </p>



                        </td>





                        {{-- Designation --}}

                        <td

                            class="px-6 py-5

                                   text-sm

                                   text-slate-300"

                        >

                            {{ $account->designation ?? __('admin_users.not_specified') }}

                        </td>





                        {{-- Division --}}

                        <td

                            class="px-6 py-5

                                   text-sm

                                   text-slate-400"

                        >

                            {{ match($account->division) {
                                'Board Secretariat' => __('common.divisions.board_secretariat'),
                                'Law and Law Enforcement' => __('common.divisions.law_enforcement'),
                                'Protection Services' => __('common.divisions.protection_services'),
                                'Police Protection' => __('common.divisions.police_protection'),
                                'Assistance Services' => __('common.divisions.assistance_services'),
                                null => __('admin_users.not_assigned'),
                                default => $account->division,
                            } }}

                        </td>





                        {{-- Role --}}

                        <td class="px-6 py-5">



                            <span

                                class="rounded-full

                                       border border-blue-500/20

                                       bg-blue-500/10

                                       px-2.5 py-1

                                       text-[10px]

                                       text-blue-300"

                            >

                                {{
                                    ($translatedRole = __('common.roles.' . $account->role)) !== 'common.roles.' . $account->role
                                        ? $translatedRole
                                        : ucwords(str_replace('_', ' ', $account->role))
                                }}

                            </span>



                        </td>





                        {{-- Created --}}

                        <td

                            class="px-6 py-5

                                   text-sm

                                   text-slate-400"

                        >

                            {{ $account->created_at->format('d M Y') }}



                            <p class="mt-1 text-[10px] text-slate-600">

                                {{ $account->created_at->format('h:i A') }}

                            </p>

                        </td>





                        {{-- Actions --}}

                        <td class="px-6 py-5">



                            <div

                                class="flex

                                       justify-end

                                       gap-2"

                            >



                                {{-- Pending --}}

                                @if($account->account_status === 'pending')



                                    <form

                                        method="POST"

                                        action="{{ route(

                                            'admin.users.approve',

                                            $account

                                        ) }}"

                                    >

                                        @csrf



                                        <button

                                            type="submit"



                                            onclick="return confirm(

                                                'Approve this account and grant DCFMS access?'

                                            )"



                                            class="rounded-lg

                                                   border border-green-500/20

                                                   bg-green-500/10

                                                   px-3 py-2

                                                   text-xs

                                                   font-medium

                                                   text-green-300

                                                   transition

                                                   hover:bg-green-500/20"

                                        >

                                            {{ __('admin_users.approve') }}

                                        </button>



                                    </form>





                                    <button

                                        type="button"



                                        onclick="

                                            document

                                                .getElementById(

                                                    'reject-modal-{{ $account->id }}'

                                                )

                                                .classList

                                                .remove('hidden')

                                        "



                                        class="rounded-lg

                                               border border-red-500/20

                                               bg-red-500/10

                                               px-3 py-2

                                               text-xs

                                               font-medium

                                               text-red-300

                                               transition

                                               hover:bg-red-500/20"

                                    >

                                        {{ __('admin_users.reject') }}

                                    </button>





                                {{-- Active --}}

                                @elseif($account->account_status === 'active')



                                    <form

                                        method="POST"

                                        action="{{ route(

                                            'admin.users.suspend',

                                            $account

                                        ) }}"

                                    >

                                        @csrf



                                        <button

                                            type="submit"



                                            onclick="return confirm(

                                                'Suspend this user account?'

                                            )"



                                            class="rounded-lg

                                                   border border-orange-500/20

                                                   bg-orange-500/10

                                                   px-3 py-2

                                                   text-xs

                                                   font-medium

                                                   text-orange-300"

                                        >

                                            {{ __('admin_users.suspend') }}

                                        </button>



                                    </form>





                                {{-- Suspended --}}

                                @elseif($account->account_status === 'suspended')



                                    <form

                                        method="POST"

                                        action="{{ route(

                                            'admin.users.reactivate',

                                            $account

                                        ) }}"

                                    >

                                        @csrf



                                        <button

                                            type="submit"



                                            class="rounded-lg

                                                   border border-green-500/20

                                                   bg-green-500/10

                                                   px-3 py-2

                                                   text-xs

                                                   font-medium

                                                   text-green-300"

                                        >

                                            {{ __('admin_users.reactivate') }}

                                        </button>



                                    </form>





                                {{-- Rejected --}}

                                @elseif($account->account_status === 'rejected')



                                    <span

                                        class="rounded-lg

                                               border border-red-500/20

                                               bg-red-500/10

                                               px-3 py-2

                                               text-xs

                                               text-red-300"

                                    >

                                        {{ __('admin_users.statuses.rejected') }}

                                    </span>



                                @endif



                            </div>



                        </td>



                    </tr>





                    {{-- =================================================

                         REJECT MODAL

                    ================================================== --}}

                    @if($account->account_status === 'pending')



                        <div

                            id="reject-modal-{{ $account->id }}"

                            class="fixed inset-0

                                   z-[100]

                                   hidden

                                   bg-black/70

                                   p-5"

                        >



                            <div

                                class="flex min-h-full

                                       items-center

                                       justify-center"

                            >



                                <div

                                    class="w-full

                                           max-w-lg

                                           rounded-2xl

                                           border border-slate-700

                                           bg-[#111A2E]

                                           p-6

                                           shadow-2xl"

                                >



                                    <h3

                                        class="text-lg

                                               font-semibold

                                               text-white"

                                    >

                                        {{ __('admin_users.reject_account_request') }}

                                    </h3>





                                    <p

                                        class="mt-2

                                               text-sm

                                               leading-relaxed

                                               text-slate-500"

                                    >

                                        Account:

                                        <span class="text-slate-300">

                                            {{ $account->name }}

                                            ({{ $account->employee_number }})

                                        </span>

                                    </p>





                                    <form

                                        method="POST"

                                        action="{{ route(

                                            'admin.users.reject',

                                            $account

                                        ) }}"

                                        class="mt-5"

                                    >



                                        @csrf





                                        <label

                                            class="block mb-2

                                                   text-sm

                                                   text-slate-300"

                                        >

                                            {{ __('admin_users.reason_for_rejection') }}

                                        </label>





                                        <textarea

                                            name="rejection_reason"

                                            rows="4"

                                            required



                                            placeholder="{{ __('admin_users.rejection_reason_placeholder') }}"



                                            class="w-full

                                                   rounded-xl

                                                   border border-slate-700

                                                   bg-[#0D172A]

                                                   px-4 py-3

                                                   text-white

                                                   placeholder-slate-600

                                                   outline-none

                                                   focus:border-red-500"

                                        ></textarea>





                                        <div

                                            class="mt-5

                                                   flex

                                                   justify-end

                                                   gap-3"

                                        >



                                            <button

                                                type="button"



                                                onclick="

                                                    document

                                                        .getElementById(

                                                            'reject-modal-{{ $account->id }}'

                                                        )

                                                        .classList

                                                        .add('hidden')

                                                "



                                                class="rounded-xl

                                                       border border-slate-700

                                                       px-4 py-2.5

                                                       text-sm

                                                       text-slate-300"

                                            >

                                                {{ __('admin_users.cancel') }}

                                            </button>





                                            <button

                                                type="submit"



                                                class="rounded-xl

                                                       bg-red-600

                                                       px-4 py-2.5

                                                       text-sm

                                                       font-semibold

                                                       text-white

                                                       hover:bg-red-500"

                                            >

                                                {{ __('admin_users.reject_account') }}

                                            </button>



                                        </div>



                                    </form>



                                </div>



                            </div>



                        </div>



                    @endif



                @empty



                    <tr>

                        <td

                            colspan="6"

                            class="px-6 py-14

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

                                👤

                            </div>



                            <p

                                class="mt-4

                                       text-sm

                                       font-medium

                                       text-slate-300"

                            >

                                {{ __('admin_users.no_accounts_found') }}

                            </p>



                            <p

                                class="mt-1

                                       text-xs

                                       text-slate-500"

                            >

                                {{ __('admin_users.no_accounts_line1') }}

                                {{ __('admin_users.no_accounts_line2') }}

                            </p>



                        </td>

                    </tr>



                @endforelse



            </tbody>



        </table>



    </div>





    @if($users->hasPages())



        <div

            class="border-t

                   border-slate-800

                   p-5"

        >

            {{ $users->links() }}

        </div>



    @endif



</div>



@endsection