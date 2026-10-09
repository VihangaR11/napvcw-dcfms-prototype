<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\Admin\UserAccountController;

use App\Http\Controllers\CaseController;
use App\Http\Controllers\LegalCaseController;
use App\Http\Controllers\ProtectionCaseController;
use App\Http\Controllers\PoliceProtectionController;
use App\Http\Controllers\AssistanceCaseController;
use App\Http\Controllers\CaseReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AuditLogController;


/*
|--------------------------------------------------------------------------
| Public Landing Page
|--------------------------------------------------------------------------
|
| DCFMS splash / landing page.
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get(
    '/language/{locale}',
    function (string $locale) {

        abort_unless(
            in_array(
                $locale,
                [
                    'en',
                    'si',
                ],
                true
            ),
            404
        );

        session([
            'locale' => $locale,
        ]);

        return redirect()
            ->back();
    }
)->name('language.switch');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
|
| These routes are available only to users who are not authenticated.
|
*/

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/login',
        [LoginController::class, 'showLoginForm']
    )->name('login');

    Route::post(
        '/login',
        [LoginController::class, 'login']
    )->name('login.attempt');


    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/signup',
        [RegisterController::class, 'showRegistrationForm']
    )->name('register');

    Route::post(
        '/signup',
        [RegisterController::class, 'register']
    )->name('register.store');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
|
| All routes below require a valid authenticated DCFMS user.
|
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/logout',
        [LoginController::class, 'logout']
    )->name('logout');


    /*
    |--------------------------------------------------------------------------
    | Master Case Registry
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/cases',
        [CaseController::class, 'index']
    )->name('cases.index');


    Route::get(
        '/cases/create',
        [CaseController::class, 'create']
    )->name('cases.create');


    Route::post(
        '/cases',
        [CaseController::class, 'store']
    )->name('cases.store');


    Route::get(
        '/cases/{case}',
        [CaseController::class, 'show']
    )->name('cases.show');


    /*
    |--------------------------------------------------------------------------
    | Case Routing
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/cases/{case}/route',
        [CaseController::class, 'routeForm']
    )->name('cases.route.form');


    Route::post(
        '/cases/{case}/route',
        [CaseController::class, 'routeCase']
    )->name('cases.route');


    /*
    |--------------------------------------------------------------------------
    | Officer Assignment
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/cases/{case}/assignments/{assignment}/assign',
        [CaseController::class, 'assignmentForm']
    )->name('cases.assignments.form');


    Route::post(
        '/cases/{case}/assignments/{assignment}/assign',
        [CaseController::class, 'assignOfficer']
    )->name('cases.assignments.assign');


    /*
    |--------------------------------------------------------------------------
    | Law & Law Enforcement Workflow
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/cases/{case}/legal',
        [LegalCaseController::class, 'show']
    )->name('legal.show');


    Route::post(
        '/cases/{case}/legal',
        [LegalCaseController::class, 'update']
    )->name('legal.update');


    /*
    |--------------------------------------------------------------------------
    | Protection Services Workflow
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/cases/{case}/protection',
        [ProtectionCaseController::class, 'show']
    )->name('protection.show');


    Route::post(
        '/cases/{case}/protection',
        [ProtectionCaseController::class, 'update']
    )->name('protection.update');


    /*
    |--------------------------------------------------------------------------
    | Police Protection Workflow
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/cases/{case}/police-protection',
        [PoliceProtectionController::class, 'show']
    )->name('police-protection.show');


    Route::post(
        '/cases/{case}/police-protection',
        [PoliceProtectionController::class, 'update']
    )->name('police-protection.update');


    /*
    |--------------------------------------------------------------------------
    | Assistance Services Workflow
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/cases/{case}/assistance',
        [AssistanceCaseController::class, 'show']
    )->name('assistance.show');


    Route::post(
        '/cases/{case}/assistance',
        [AssistanceCaseController::class, 'update']
    )->name('assistance.update');


    /*
    |--------------------------------------------------------------------------
    | Case Summary Reports
    |--------------------------------------------------------------------------
    |
    | Preview opens the PDF in-browser.
    | Download sends the PDF as a file attachment.
    |
    */

    Route::get(
        '/cases/{case}/summary-pdf/preview',
        [CaseReportController::class, 'preview']
    )->name('cases.summary.preview');


    Route::get(
        '/cases/{case}/summary-pdf',
        [CaseReportController::class, 'download']
    )->name('cases.summary.pdf');


    /*
    |--------------------------------------------------------------------------
    | System Administrator - User Accounts
    |--------------------------------------------------------------------------
    |
    | The controller itself restricts access to system_admin users.
    |
    */

    Route::get(
        '/admin/users',
        [UserAccountController::class, 'index']
    )->name('admin.users.index');


    Route::post(
        '/admin/users/{user}/approve',
        [UserAccountController::class, 'approve']
    )->name('admin.users.approve');


    Route::post(
        '/admin/users/{user}/reject',
        [UserAccountController::class, 'reject']
    )->name('admin.users.reject');


    Route::post(
        '/admin/users/{user}/suspend',
        [UserAccountController::class, 'suspend']
    )->name('admin.users.suspend');


    Route::post(
        '/admin/users/{user}/reactivate',
        [UserAccountController::class, 'reactivate']
    )->name('admin.users.reactivate');

    Route::post(
        '/cases/{case}/police-protection/threat-status',
        [PoliceProtectionController::class, 'updateThreatStatus']
    )->name('police-protection.threat-status.update');

    Route::post(
    '/cases/{case}/protection/interim-approval',
    [ProtectionCaseController::class, 'updateInterimProtection']
)->name('protection.interim.update');


Route::post(
    '/cases/{case}/protection/final-approval',
    [ProtectionCaseController::class, 'updateFinalProtection']
)->name('protection.final.update');

Route::post(
    '/cases/{case}/close',
    [CaseController::class, 'close']
)->name('cases.close');


Route::post(
    '/cases/{case}/reopen',
    [CaseController::class, 'reopen']
)->name('cases.reopen');

Route::get(
    '/admin/audit-logs',
    [AuditLogController::class, 'index']
)->name('admin.audit-logs.index');

Route::post(
    '/cases/{case}/dg-protection-decision',
    [ProtectionCaseController::class, 'updateDgProtectionDecision']
)->name('cases.dg-protection-decision');


});