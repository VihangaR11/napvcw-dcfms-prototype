<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    use AuthorizesRequests;

    /*
    |--------------------------------------------------------------------------
    | Display Audit Log Viewer
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Access Control
        |--------------------------------------------------------------------------
        |
        | Only System Administrator and Director General may view the audit log.
        |
        */

        abort_unless(
            $user &&
            in_array(
                $user->role,
                [
                    'system_admin',
                    'director_general',
                ],
                true
            ),
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Validate Filters
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'action' => [
                'nullable',
                'string',
                'max:150',
            ],

            'module' => [
                'nullable',
                'string',
                'max:100',
            ],

            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'date_from' => [
                'nullable',
                'date',
            ],

            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Base Audit Log Query
        |--------------------------------------------------------------------------
        */

        $query = AuditLog::query()
            ->with([
                'user',
                'case',
            ])
            ->latest('created_at');


        /*
        |--------------------------------------------------------------------------
        | Search Filter
        |--------------------------------------------------------------------------
        |
        | Searches:
        | - audit action
        | - module
        | - description
        | - case number
        | - user name
        | - employee number
        |
        */

        if (
            !empty(
                $validated['search']
            )
        ) {
            $search =
                trim(
                    $validated['search']
                );

            $query->where(
                function ($subQuery) use ($search) {
                    $subQuery
                        ->where(
                            'action',
                            'ilike',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'module',
                            'ilike',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'description',
                            'ilike',
                            "%{$search}%"
                        )
                        ->orWhereHas(
                            'case',
                            function ($caseQuery) use ($search) {
                                $caseQuery->where(
                                    'case_number',
                                    'ilike',
                                    "%{$search}%"
                                );
                            }
                        )
                        ->orWhereHas(
                            'user',
                            function ($userQuery) use ($search) {
                                $userQuery
                                    ->where(
                                        'name',
                                        'ilike',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'employee_number',
                                        'ilike',
                                        "%{$search}%"
                                    );
                            }
                        );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Action Filter
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $validated['action']
            )
        ) {
            $query->where(
                'action',
                $validated['action']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Module Filter
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $validated['module']
            )
        ) {
            $query->where(
                'module',
                $validated['module']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | User Filter
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $validated['user_id']
            )
        ) {
            $query->where(
                'user_id',
                $validated['user_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Date Range Filters
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $validated['date_from']
            )
        ) {
            $query->whereDate(
                'created_at',
                '>=',
                $validated['date_from']
            );
        }


        if (
            !empty(
                $validated['date_to']
            )
        ) {
            $query->whereDate(
                'created_at',
                '<=',
                $validated['date_to']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Paginated Results
        |--------------------------------------------------------------------------
        */

        $logs = $query
            ->paginate(25)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Filter Option Lists
        |--------------------------------------------------------------------------
        */

        $actions = AuditLog::query()
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');


        $modules = AuditLog::query()
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');


        /*
        |--------------------------------------------------------------------------
        | Users Appearing in Audit Logs
        |--------------------------------------------------------------------------
        */

        $users = User::query()
            ->whereIn(
                'id',
                AuditLog::query()
                    ->whereNotNull('user_id')
                    ->select('user_id')
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'employee_number',
                'role',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Return Audit Log Viewer
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.audit-logs.index',
            compact(
                'logs',
                'actions',
                'modules',
                'users'
            )
        );
    }
}