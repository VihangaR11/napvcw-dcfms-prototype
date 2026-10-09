<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\Models\User;

use App\Services\AuditLogService;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;



class UserAccountController extends Controller

{

    use AuthorizesRequests;



    public function __construct(

        private readonly AuditLogService $audit

    ) {

    }





    /*

    |--------------------------------------------------------------------------

    | User Administration Dashboard

    |--------------------------------------------------------------------------

    */



    public function index(Request $request)

    {

        $this->authorize(

            'viewAny',

            User::class

        );





        $status = $request->get(

            'status',

            'pending'

        );





        $allowedStatuses = [

            'pending',

            'active',

            'rejected',

            'suspended',

        ];





        if (

            !in_array(

                $status,

                $allowedStatuses,

                true

            )

        ) {

            $status = 'pending';

        }





        /*

        |--------------------------------------------------------------------------

        | User List

        |--------------------------------------------------------------------------

        */



        $users = User::query()

            ->where(

                'account_status',

                $status

            )

            ->where(

                'role',

                '!=',

                'system_admin'

            )

            ->latest()

            ->paginate(15)

            ->withQueryString();





        /*

        |--------------------------------------------------------------------------

        | Dashboard Counts

        |--------------------------------------------------------------------------

        */



        $counts = [



            'pending' =>

                User::query()

                    ->where(

                        'account_status',

                        'pending'

                    )

                    ->where(

                        'role',

                        '!=',

                        'system_admin'

                    )

                    ->count(),



            'active' =>

                User::query()

                    ->where(

                        'account_status',

                        'active'

                    )

                    ->where(

                        'role',

                        '!=',

                        'system_admin'

                    )

                    ->count(),



            'rejected' =>

                User::query()

                    ->where(

                        'account_status',

                        'rejected'

                    )

                    ->where(

                        'role',

                        '!=',

                        'system_admin'

                    )

                    ->count(),



            'suspended' =>

                User::query()

                    ->where(

                        'account_status',

                        'suspended'

                    )

                    ->where(

                        'role',

                        '!=',

                        'system_admin'

                    )

                    ->count(),

        ];





        return view(

            'admin.users.index',

            compact(

                'users',

                'counts',

                'status'

            )

        );

    }





    /*

    |--------------------------------------------------------------------------

    | Approve Pending Account

    |--------------------------------------------------------------------------

    */



    public function approve(User $user)

    {

        $this->authorize(

            'approve',

            $user

        );





        abort_if(

            $user->role ===

                'system_admin',

            403

        );





        abort_unless(

            $user->account_status ===

                'pending',

            422,

            __('messages.user_accounts.only_pending_can_be_approved')

        );





        /*

        |--------------------------------------------------------------------------

        | Snapshot Previous Values

        |--------------------------------------------------------------------------

        */



        $oldValues = [

            'account_status' =>

                $user->account_status,



            'is_active' =>

                $user->is_active,



            'approved_by' =>

                $user->approved_by,



            'approved_at' =>

                $user->approved_at

                    ? $user->approved_at->toDateTimeString()

                    : null,



            'rejected_at' =>

                $user->rejected_at

                    ? $user->rejected_at->toDateTimeString()

                    : null,

        ];





        /*

        |--------------------------------------------------------------------------

        | Approve Account

        |--------------------------------------------------------------------------

        */



        DB::transaction(

            function () use ($user) {



                $user->update([

                    'account_status' =>

                        'active',



                    'is_active' =>

                        true,



                    'approved_by' =>

                        auth()->id(),



                    'approved_at' =>

                        now(),



                    'rejected_at' =>

                        null,



                    'rejection_reason' =>

                        null,

                ]);

            }

        );





        /*

        |--------------------------------------------------------------------------

        | Structured Audit Log

        |--------------------------------------------------------------------------

        */



        $user->refresh();



        $this->audit->record(

            'USER_APPROVED',

            $user,

            $oldValues,

            [

                'account_status' =>

                    $user->account_status,



                'is_active' =>

                    $user->is_active,



                'approved_by' =>

                    $user->approved_by,



                'approved_at' =>

                    $user->approved_at

                        ? $user->approved_at->toDateTimeString()

                        : null,



                'rejected_at' =>

                    $user->rejected_at

                        ? $user->rejected_at->toDateTimeString()

                        : null,

            ],

            "User account approved for {$user->name}.",

            'User Administration'

        );





        return redirect()

            ->back()

            ->with(

                'success',

                __('messages.user_accounts.approved', [
                    'name' => $user->name,
                ])

            );

    }





    /*

    |--------------------------------------------------------------------------

    | Reject Account Request

    |--------------------------------------------------------------------------

    */



    public function reject(

        Request $request,

        User $user

    ) {

        $this->authorize(

            'reject',

            $user

        );





        abort_if(

            $user->role ===

                'system_admin',

            403

        );





        abort_unless(

            $user->account_status ===

                'pending',

            422,

            __('messages.user_accounts.only_pending_can_be_rejected')

        );





        $validated =

            $request->validate([

                'rejection_reason' => [

                    'required',

                    'string',

                    'max:1000',

                ],

            ]);





        /*

        |--------------------------------------------------------------------------

        | Snapshot Previous Values

        |--------------------------------------------------------------------------

        */



        $oldValues = [

            'account_status' =>

                $user->account_status,



            'is_active' =>

                $user->is_active,



            'approved_by' =>

                $user->approved_by,



            'approved_at' =>

                $user->approved_at

                    ? $user->approved_at->toDateTimeString()

                    : null,



            'rejected_at' =>

                $user->rejected_at

                    ? $user->rejected_at->toDateTimeString()

                    : null,

        ];





        /*

        |--------------------------------------------------------------------------

        | Reject Account

        |--------------------------------------------------------------------------

        */



        DB::transaction(

            function () use (

                $user,

                $validated

            ) {

                $user->update([

                    'account_status' =>

                        'rejected',



                    'is_active' =>

                        false,



                    'approved_by' =>

                        null,



                    'approved_at' =>

                        null,



                    'rejected_at' =>

                        now(),



                    'rejection_reason' =>

                        $validated[

                            'rejection_reason'

                        ],

                ]);

            }

        );





        /*

        |--------------------------------------------------------------------------

        | Structured Audit Log

        |--------------------------------------------------------------------------

        |

        | Rejection reason is intentionally not duplicated into old/new JSON.

        | It remains stored in the user record and the audit description only

        | records that rejection occurred.

        |

        */



        $user->refresh();



        $this->audit->record(

            'USER_REJECTED',

            $user,

            $oldValues,

            [

                'account_status' =>

                    $user->account_status,



                'is_active' =>

                    $user->is_active,



                'approved_by' =>

                    $user->approved_by,



                'approved_at' =>

                    $user->approved_at

                        ? $user->approved_at->toDateTimeString()

                        : null,



                'rejected_at' =>

                    $user->rejected_at

                        ? $user->rejected_at->toDateTimeString()

                        : null,

            ],

            "User account request rejected for {$user->name}.",

            'User Administration'

        );





        return redirect()

            ->back()

            ->with(

                'success',

                __('messages.user_accounts.rejected', [
                    'name' => $user->name,
                ])

            );

    }





    /*

    |--------------------------------------------------------------------------

    | Suspend Active Account

    |--------------------------------------------------------------------------

    */



    public function suspend(User $user)

    {

        $this->authorize(

            'suspend',

            $user

        );





        abort_if(

            $user->role ===

                'system_admin',

            403

        );





        abort_unless(

            $user->account_status ===

                'active',

            422,

            __('messages.user_accounts.only_active_can_be_suspended')

        );





        /*

        |--------------------------------------------------------------------------

        | Snapshot Previous Values

        |--------------------------------------------------------------------------

        */



        $oldValues = [

            'account_status' =>

                $user->account_status,



            'is_active' =>

                $user->is_active,

        ];





        /*

        |--------------------------------------------------------------------------

        | Suspend Account

        |--------------------------------------------------------------------------

        */



        DB::transaction(

            function () use ($user) {



                $user->update([

                    'account_status' =>

                        'suspended',



                    'is_active' =>

                        false,

                ]);

            }

        );





        /*

        |--------------------------------------------------------------------------

        | Structured Audit Log

        |--------------------------------------------------------------------------

        */



        $user->refresh();



        $this->audit->record(

            'USER_SUSPENDED',

            $user,

            $oldValues,

            [

                'account_status' =>

                    $user->account_status,



                'is_active' =>

                    $user->is_active,

            ],

            "User account suspended for {$user->name}.",

            'User Administration'

        );





        return redirect()

            ->back()

            ->with(

                'success',

                __('messages.user_accounts.suspended', [
                    'name' => $user->name,
                ])

            );

    }





    /*

    |--------------------------------------------------------------------------

    | Reactivate Suspended Account

    |--------------------------------------------------------------------------

    */



    public function reactivate(User $user)

    {

        $this->authorize(

            'reactivate',

            $user

        );





        abort_if(

            $user->role ===

                'system_admin',

            403

        );





        abort_unless(

            $user->account_status ===

                'suspended',

            422,

            __('messages.user_accounts.only_suspended_can_be_reactivated')

        );





        /*

        |--------------------------------------------------------------------------

        | Snapshot Previous Values

        |--------------------------------------------------------------------------

        */



        $oldValues = [

            'account_status' =>

                $user->account_status,



            'is_active' =>

                $user->is_active,



            'approved_by' =>

                $user->approved_by,



            'approved_at' =>

                $user->approved_at

                    ? $user->approved_at->toDateTimeString()

                    : null,

        ];





        /*

        |--------------------------------------------------------------------------

        | Reactivate Account

        |--------------------------------------------------------------------------

        */



        DB::transaction(

            function () use ($user) {



                $user->update([

                    'account_status' =>

                        'active',



                    'is_active' =>

                        true,



                    'approved_by' =>

                        auth()->id(),



                    'approved_at' =>

                        now(),

                ]);

            }

        );





        /*

        |--------------------------------------------------------------------------

        | Structured Audit Log

        |--------------------------------------------------------------------------

        */



        $user->refresh();



        $this->audit->record(

            'USER_REACTIVATED',

            $user,

            $oldValues,

            [

                'account_status' =>

                    $user->account_status,



                'is_active' =>

                    $user->is_active,



                'approved_by' =>

                    $user->approved_by,



                'approved_at' =>

                    $user->approved_at

                        ? $user->approved_at->toDateTimeString()

                        : null,

            ],

            "User account reactivated for {$user->name}.",

            'User Administration'

        );





        return redirect()

            ->back()

            ->with(

                'success',

                __('messages.user_accounts.reactivated', [
                    'name' => $user->name,
                ])

            );

    }

}