<!DOCTYPE html>



<html lang="en">



<head>



    <meta charset="UTF-8">







    <title>



        Case Summary - {{ $case->case_number }}



    </title>







    <style>



        @page {



            margin: 28px 32px 36px 32px;



        }







        * {



            box-sizing: border-box;



        }







        body {



            /*



             * Helvetica is a DomPDF core font.



             * Using it avoids dependency on external/custom font files.



             */



            font-family: 'DejaVu Sans', sans-serif;



            font-size: 10px;



            line-height: 1.45;



            color: #1f2937;



            margin: 0;



            padding: 0;



        }







        .header {



            border-bottom: 2px solid #1e3a8a;



            padding-bottom: 12px;



            margin-bottom: 18px;



        }







        .header-table,



        .data,



        .timeline {



            width: 100%;



            border-collapse: collapse;



        }







        .header-table td {



            border: none;



        }







        .logo-cell {



            width: 72px;



            vertical-align: middle;



        }







        .logo {



            width: 58px;



            height: auto;



            display: block;



        }







        .title-cell {



            vertical-align: middle;



            padding-left: 4px;



        }







        .institution {



            font-size: 11px;



            color: #475569;



            margin: 0;



        }







        .system-title {



            font-size: 18px;



            font-weight: bold;



            color: #0f172a;



            margin: 3px 0;



        }







        .report-title {



            font-size: 13px;



            font-weight: bold;



            color: #1e40af;



            margin-top: 4px;



        }







        .case-number {



            background: #eff6ff;



            border: 1px solid #bfdbfe;



            padding: 10px;



            margin-bottom: 16px;



        }







        .case-number strong {



            color: #1e40af;



            font-size: 14px;



        }







        .section {



            margin-top: 16px;



            page-break-inside: avoid;



        }







        .section.allow-break {



            page-break-inside: auto;



        }







        .section-title {



            background: #0f172a;



            color: #ffffff;



            padding: 7px 9px;



            font-size: 11px;



            font-weight: bold;



            margin-bottom: 0;



        }







        table.data {



            margin-top: 0;



        }







        table.data th,



        table.data td {



            border: 1px solid #cbd5e1;



            padding: 6px;



            vertical-align: top;



        }







        table.data th {



            width: 29%;



            background: #f8fafc;



            text-align: left;



            font-weight: bold;



            color: #334155;



        }







        table.timeline th,



        table.timeline td {



            border: 1px solid #cbd5e1;



            padding: 5px;



            vertical-align: top;



        }







        table.timeline th {



            background: #e2e8f0;



            font-size: 9px;



            text-align: left;



        }







        .summary-box {



            border: 1px solid #cbd5e1;



            background: #f8fafc;



            padding: 8px;



            white-space: pre-line;



        }







        .muted {



            color: #64748b;



        }







        .decision-approved {



            color: #166534;



            font-weight: bold;



        }







        .decision-pending {



            color: #92400e;



            font-weight: bold;



        }







        .decision-not-approved {



            color: #64748b;



        }







        .protection-list {



            margin: 0;



            padding-left: 16px;



        }







        .protection-list li {



            margin-bottom: 3px;



        }







        .page-break {



            page-break-before: always;



        }







        .footer-note {



            margin-top: 20px;



            padding-top: 10px;



            border-top: 1px solid #cbd5e1;



            color: #64748b;



            font-size: 8px;



        }



    </style>



</head>







<body>







@php



    /*



    |--------------------------------------------------------------------------



    | PDF-Safe Local Logos



    |--------------------------------------------------------------------------



    |



    | Convert local image files to data URIs so DomPDF does not need HTTP,



    | remote access, or a filesystem URL while rendering.



    |



    */







    $makeImageDataUri = function (?string $path): ?string {



        if (!$path || !is_file($path) || !is_readable($path)) {



            return null;



        }







        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));







        $mime = match ($extension) {



            'png' => 'image/png',



            'jpg', 'jpeg' => 'image/jpeg',



            'webp' => 'image/webp',



            default => null,



        };







        if (!$mime) {



            return null;



        }







        $contents = file_get_contents($path);







        if ($contents === false) {



            return null;



        }







        return 'data:' . $mime . ';base64,' . base64_encode($contents);



    };







    $napvcwLogoSrc = $makeImageDataUri(



        public_path('images/napvcw-logo.png')



    );







    if (!$napvcwLogoSrc) {



        $napvcwLogoSrc = $makeImageDataUri(



            public_path('images/napvcw-logo.webp')



        );



    }







    /*



    |--------------------------------------------------------------------------



    | Case-Level Protection Data



    |--------------------------------------------------------------------------



    */







    $approvedProtectionTypes =



        is_array($case->approved_protection_types)



            ? $case->approved_protection_types



            : [];

    /*
    |--------------------------------------------------------------------------
    | Director General Protection Decision
    |--------------------------------------------------------------------------
    |
    | New records use dg_protection_* as the authoritative decision.
    | Older records fall back to the existing final_protection_* fields.
    |
    */

    $dgProtectionType =
        $case->dg_protection_type
        ?? (
            count($approvedProtectionTypes) === 1
                ? $approvedProtectionTypes[0]
                : null
        );

    $dgProtectionDecisionMaker =
        $case->dgProtectionDecisionMaker?->name
        ?? $case->finalProtectionApprover?->name
        ?? 'Not recorded';

    $dgProtectionDecisionAt =
        $case->dg_protection_decided_at
        ?? $case->final_protection_approved_at;








    $assistanceAssignment =



        $case->assignments



            ?->firstWhere('division', 'Assistance Services');







    $assistanceResponsibleOfficer =



        $assistanceAssignment?->assignedUser?->name



        ?? 'Not assigned';



@endphp











{{-- ============================================================



     HEADER



\\============================================================ --}}



<div class="header">







    <table class="header-table">



        <tr>







            <td class="logo-cell">



                @if($napvcwLogoSrc)



                    <img



                        src="{{ $napvcwLogoSrc }}"



                        class="logo"



                        alt="NAPVCW Logo"



                    >



                @endif



            </td>







            <td class="title-cell">







                <p class="institution">



                    National Authority for the Protection of Victims of Crime and Witnesses



                </p>







                <div class="system-title">



                    Digital Case Flow Management System



                </div>







                <div class="report-title">



                    CASE SUMMARY REPORT



                </div>







            </td>







        </tr>



    </table>







</div>











{{-- ============================================================



     CASE IDENTIFICATION



\\============================================================ --}}



<div class="case-number">







    <strong>



        {{ $case->case_number }}



    </strong>







    <br>







    <span class="muted">



        Current Status:



        {{ $case->current_status }}







        |



        Urgency:



        {{ $case->urgency }}



    </span>







</div>











{{-- ============================================================



     1. MASTER CASE INFORMATION



\\============================================================ --}}



<div class="section">







    <div class="section-title">



        1. Master Case Information



    </div>







    <table class="data">







        <tr>



            <th>Case Number</th>



            <td>{{ $case->case_number }}</td>



        </tr>







        <tr>



            <th>Received Date</th>



            <td>



                {{ $case->received_date?->format('d M Y') ?? 'Not recorded' }}



            </td>



        </tr>







        <tr>



            <th>Complaint Source</th>



            <td>{{ $case->complaint_source ?? 'Not recorded' }}</td>



        </tr>







        <tr>



            <th>Complaint Mode</th>



            <td>{{ $case->complaint_mode ?? 'Not recorded' }}</td>



        </tr>







        <tr>



            <th>Complaint Category</th>



            <td>{{ $case->complaint_category ?? 'Not recorded' }}</td>



        </tr>







        <tr>



            <th>Urgency</th>



            <td>{{ $case->urgency ?? 'Not recorded' }}</td>



        </tr>







        <tr>



            <th>Current Status</th>



            <td>{{ $case->current_status ?? 'Not recorded' }}</td>



        </tr>







        <tr>



            <th>Primary Division</th>



            <td>{{ $case->primary_division ?? 'Not specified' }}</td>



        </tr>







        <tr>



            <th>Registered By</th>



            <td>{{ $case->creator?->name ?? 'System' }}</td>



        </tr>







        <tr>



            <th>Registered Date / Time</th>



            <td>



                {{ $case->created_at?->format('d M Y, h:i A') ?? 'Not recorded' }}



            </td>



        </tr>







    </table>







</div>











{{-- ============================================================



     2. COMPLAINANT / PARTY INFORMATION



\\============================================================ --}}



<div class="section">







    <div class="section-title">



        2. Complainant / Party Information



    </div>







    <table class="data">







        <tr>



            <th>Complainant Name</th>



            <td>{{ $case->complainant_name ?? 'Not recorded' }}</td>



        </tr>







        <tr>



            <th>Person Type</th>



            <td>{{ $case->victim_witness_type ?? 'Not recorded' }}</td>



        </tr>







        <tr>



            <th>Contact Number</th>



            <td>{{ $case->contact_number ?? 'Not recorded' }}</td>



        </tr>







        <tr>



            <th>Email</th>



            <td>{{ $case->email ?? 'Not recorded' }}</td>



        </tr>







    </table>







</div>











{{-- ============================================================



     3. COMPLAINT SUMMARY



\\============================================================ --}}



<div class="section">







    <div class="section-title">



        3. Complaint Summary



    </div>







    <div class="summary-box">



        {{ $case->complaint_summary ?? 'No complaint summary recorded.' }}



    </div>







</div>











{{-- ============================================================



     4. DIVISION ASSIGNMENTS



\\============================================================ --}}



<div class="section allow-break">







    <div class="section-title">



        4. Division Assignments



    </div>







    <table class="timeline">







        <thead>



            <tr>



                <th>Division</th>



                <th>Responsible Officer</th>



                <th>Status</th>



                <th>Assigned Date</th>



            </tr>



        </thead>







        <tbody>







            @forelse($case->assignments as $assignment)







                <tr>







                    <td>



                        {{ $assignment->division }}



                    </td>







                    <td>



                        {{ $assignment->assignedUser?->name ?? 'Not assigned' }}



                    </td>







                    <td>



                        {{ $assignment->status ?? 'Not recorded' }}



                    </td>







                    <td>



                        {{ $assignment->assigned_at?->format('d M Y') ?? 'N/A' }}



                    </td>







                </tr>







            @empty







                <tr>



                    <td colspan="4">



                        No active division assignments.



                    </td>



                </tr>







            @endforelse







        </tbody>







    </table>







</div>











{{-- ============================================================



     5. LAW & LAW ENFORCEMENT WORKFLOW



\\============================================================ --}}



@if($case->legalDetail)







    <div class="section">







        <div class="section-title">



            5. Law & Law Enforcement Workflow



        </div>







        <table class="data">







            <tr>



                <th>RE Number</th>



                <td>{{ $case->legalDetail->re_number ?? 'Not assigned' }}</td>



            </tr>







            <tr>



                <th>Legal Status</th>



                <td>{{ $case->legalDetail->legal_status ?? 'Not recorded' }}</td>



            </tr>







            <tr>



                <th>Legal Officer</th>



                <td>



                    {{ $case->legalDetail->legalOfficer?->name ?? 'Not assigned' }}



                </td>



            </tr>







            <tr>



                <th>Investigation Officer</th>



                <td>



                    {{ $case->legalDetail->investigationOfficer?->name ?? 'Not assigned' }}



                </td>



            </tr>







            <tr>



                <th>Inquiry Started</th>



                <td>



                    {{ $case->legalDetail->inquiry_started_date?->format('d M Y') ?? 'N/A' }}



                </td>



            </tr>







            <tr>



                <th>Observation Requested</th>



                <td>



                    {{ $case->legalDetail->observation_requested_date?->format('d M Y') ?? 'N/A' }}



                </td>



            </tr>







            <tr>



                <th>Observation Due</th>



                <td>



                    {{ $case->legalDetail->observation_due_date?->format('d M Y') ?? 'N/A' }}



                </td>



            </tr>







            <tr>



                <th>Investigation Findings</th>



                <td>{{ $case->legalDetail->io_findings ?? 'Not recorded' }}</td>



            </tr>







            <tr>



                <th>Legal Recommendation</th>



                <td>



                    {{ $case->legalDetail->legal_recommendation ?? 'Not recorded' }}



                </td>



            </tr>







            <tr>



                <th>Case Conference</th>



                <td>



                    {{ $case->legalDetail->case_conference_required ? 'Required' : 'Not required' }}



                </td>



            </tr>







            <tr>



                <th>Board Submission</th>



                <td>



                    {{ $case->legalDetail->board_submission_required ? 'Required' : 'Not required' }}



                </td>



            </tr>







            <tr>



                <th>Board Decision</th>



                <td>



                    {{ $case->legalDetail->board_decision ?? 'Not recorded' }}



                </td>



            </tr>







        </table>







    </div>







@endif











{{-- ============================================================



     6. PROTECTION SERVICES WORKFLOW



\\============================================================ --}}



@if($case->protectionDetail)







    <div class="section">







        <div class="section-title">



            6. Protection Services Workflow



        </div>







        <table class="data">







            <tr>



                <th>Protection Status</th>



                <td>



                    {{ $case->protectionDetail->protection_status ?? 'Not recorded' }}



                </td>



            </tr>







            <tr>



                <th>Protection Officer</th>



                <td>



                    {{ $case->protectionDetail->protectionOfficer?->name ?? 'Not assigned' }}



                </td>



            </tr>







            <tr>



                <th>Threat Type</th>



                <td>



                    {{ $case->protectionDetail->threat_type ?? 'Not recorded' }}



                </td>



            </tr>







            <tr>



                <th>Operational Threat Assessment Status</th>



                <td>



                    {{ $case->protectionDetail->threat_assessment_status ?? 'Not recorded' }}



                </td>



            </tr>







            <tr>



                <th>Operational Threat Level</th>



                <td>



                    {{ $case->protectionDetail->threat_level ?? 'Not determined' }}



                </td>



            </tr>







            <tr>



                <th>Interim Protection Request</th>



                <td>



                    {{ $case->protectionDetail->interim_protection_required ? 'Required' : 'Not required' }}



                </td>



            </tr>







            <tr>



                <th>Protection Decision / Notes</th>



                <td>



                    {{ $case->protectionDetail->protection_decision ?? 'Not recorded' }}



                </td>



            </tr>







            <tr>



                <th>Protection Outcome</th>



                <td>



                    {{ $case->protectionDetail->protection_outcome ?? 'Not determined' }}



                </td>



            </tr>







        </table>







    </div>







@endif











{{-- ============================================================



     7. POLICE PROTECTION THREAT ASSESSMENT



\\============================================================ --}}



@if($case->policeProtectionDetail)







    <div class="section">







        <div class="section-title">



            7. Police Protection Threat Assessment



        </div>







        <table class="data">







            <tr>



                <th>Assessment Status</th>



                <td>



                    {{ $case->policeProtectionDetail->assessment_status ?? 'Not recorded' }}



                </td>



            </tr>







            <tr>



                <th>Assessment Officer</th>



                <td>



                    {{ $case->policeProtectionDetail->officer?->name ?? 'Not assigned' }}



                </td>



            </tr>







            <tr>



                <th>Request Received</th>



                <td>



                    {{ $case->policeProtectionDetail->request_received_date?->format('d M Y') ?? 'N/A' }}



                </td>



            </tr>







            <tr>



                <th>Assessment Completed</th>



                <td>



                    {{ $case->policeProtectionDetail->assessment_completed_date?->format('d M Y') ?? 'N/A' }}



                </td>



            </tr>







            <tr>



                <th>Assessment Threat Level</th>



                <td>



                    {{ $case->policeProtectionDetail->threat_level ?? 'Not determined' }}



                </td>



            </tr>







            <tr>



                <th>Assessment Summary</th>



                <td>



                    {{ $case->policeProtectionDetail->assessment_summary ?? 'Not recorded' }}



                </td>



            </tr>







            <tr>



                <th>Assessment Recommendation</th>



                <td>



                    {{ $case->policeProtectionDetail->recommendation ?? 'Not recorded' }}



                </td>



            </tr>







        </table>







    </div>







@endif











{{-- ============================================================



     8. ASSISTANCE SERVICES WORKFLOW



\\============================================================ --}}



@if($case->assistanceDetail)







    <div class="section">







        <div class="section-title">



            8. Assistance Services Workflow



        </div>







        <table class="data">







            <tr>



                <th>Assistance Status</th>



                <td>



                    {{ $case->assistanceDetail->assistance_status ?? 'Not recorded' }}



                </td>



            </tr>







            <tr>



                <th>Responsible Officer</th>



                <td>



                    {{ $assistanceResponsibleOfficer }}



                </td>



            </tr>







            <tr>



                <th>Assistance Type</th>



                <td>



                    {{ $case->assistanceDetail->assistance_type ?? 'Not recorded' }}



                </td>



            </tr>







            <tr>



                <th>Referral Required</th>



                <td>



                    {{ $case->assistanceDetail->referral_required ? 'Yes' : 'No' }}



                </td>



            </tr>







            <tr>



                <th>Referred To</th>



                <td>



                    {{ $case->assistanceDetail->referred_to ?? 'N/A' }}



                </td>



            </tr>







            <tr>



                <th>Current Action</th>



                <td>



                    {{ $case->assistanceDetail->current_action ?? 'Not recorded' }}



                </td>



            </tr>







            <tr>



                <th>Outcome</th>



                <td>



                    {{ $case->assistanceDetail->assistance_outcome ?? 'Not recorded' }}



                </td>



            </tr>







        </table>







    </div>







@endif











{{-- ============================================================



     9. DIRECTOR - POLICE PROTECTION RECOMMENDATION



\\============================================================ --}}



<div class="section">







    <div class="section-title">



        9. Director - Police Protection Threat Recommendation



    </div>







    <table class="data">







        <tr>



            <th>Recommended Threat Status</th>



            <td>



                <strong>



                    {{ $case->threat_assessment_status ?? 'Not Assessed' }}



                </strong>



            </td>



        </tr>







        <tr>



            <th>Recommended By</th>



            <td>



                {{ $case->threatAssessmentDirector?->name ?? 'Not recorded' }}



            </td>



        </tr>







        <tr>



            <th>Designation</th>



            <td>



                Director - Police Protection



            </td>



        </tr>







        <tr>



            <th>Recommendation Date / Time</th>



            <td>



                {{ $case->threat_assessment_at



                    ? $case->threat_assessment_at->format('d M Y, h:i A')



                    : 'Not recorded'



                }}



            </td>



        </tr>







    </table>







</div>











{{-- ============================================================

     10. DIRECTOR GENERAL PROTECTION DECISION

\============================================================ --}}

<div class="section">

    <div class="section-title">
        10. Director General Protection Decision
    </div>

    <table class="data">

        <tr>
            <th>Threat Assessment Recommendation</th>
            <td>
                <strong>
                    {{ $case->threat_assessment_status ?? 'Not assessed' }}
                </strong>
            </td>
        </tr>

        <tr>
            <th>Protection Type Selected by DG</th>
            <td>
                @if($dgProtectionType)
                    <span class="decision-approved">
                        {{ $dgProtectionType }}
                    </span>
                @elseif($case->threat_assessment_status)
                    <span class="decision-pending">
                        Awaiting Director General protection decision.
                    </span>
                @else
                    <span class="decision-not-approved">
                        Awaiting threat assessment recommendation.
                    </span>
                @endif
            </td>
        </tr>

        <tr>
            <th>Decision By</th>
            <td>{{ $dgProtectionDecisionMaker }}</td>
        </tr>

        <tr>
            <th>Decision Date / Time</th>
            <td>
                {{
                    $dgProtectionDecisionAt
                        ? $dgProtectionDecisionAt->format('d M Y, h:i A')
                        : 'Not recorded'
                }}
            </td>
        </tr>

        <tr>
            <th>Interim Protection Approval</th>
            <td>
                @if($case->interim_protection_approved)
                    <span class="decision-approved">Approved</span>
                @else
                    <span class="decision-not-approved">Not approved</span>
                @endif
            </td>
        </tr>

        <tr>
            <th>Interim Approval By</th>
            <td>{{ $case->interimProtectionApprover?->name ?? 'Not recorded' }}</td>
        </tr>

        <tr>
            <th>Interim Approval Date / Time</th>
            <td>
                {{
                    $case->interim_protection_approved_at
                        ? $case->interim_protection_approved_at->format('d M Y, h:i A')
                        : 'Not recorded'
                }}
            </td>
        </tr>

        @if(count($approvedProtectionTypes) > 1)
            <tr>
                <th>Legacy Approved Protection Measures</th>
                <td>
                    <ul class="protection-list">
                        @foreach($approvedProtectionTypes as $protectionType)
                            <li>{{ $protectionType }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        @endif

    </table>

</div>



11. SHARED CASE ACTIVITY TIMELINE



\\============================================================ --}}



<div class="section allow-break page-break">







    <div class="section-title">



        11. Shared Case Activity Timeline



    </div>







    <table class="timeline">







        <thead>



            <tr>



                <th style="width: 14%;">Date</th>



                <th style="width: 18%;">Division</th>



                <th style="width: 17%;">Status</th>



                <th style="width: 25%;">Action</th>



                <th style="width: 26%;">Remarks / User</th>



            </tr>



        </thead>







        <tbody>







            @forelse($case->statusHistory as $history)







                <tr>







                    <td>



                        {{ $history->created_at?->format('d M Y') ?? 'N/A' }}







                        <br>







                        <span class="muted">



                            {{ $history->created_at?->format('h:i A') ?? '' }}



                        </span>



                    </td>







                    <td>



                        {{ $history->division ?? 'System' }}



                    </td>







                    <td>



                        {{ $history->status ?? 'Not recorded' }}



                    </td>







                    <td>



                        {{ $history->action_taken ?? 'Not recorded' }}



                    </td>







                    <td>



                        {{ $history->remarks ?? '-' }}







                        <br>







                        <span class="muted">



                            By:



                            {{ $history->updatedBy?->name ?? 'System' }}



                        </span>



                    </td>







                </tr>







            @empty







                <tr>



                    <td colspan="5">



                        No case activity recorded.



                    </td>



                </tr>







            @endforelse







        </tbody>







    </table>







</div>











{{-- ============================================================



     12. REPORT INFORMATION



\\============================================================ --}}



<div class="section">







    <div class="section-title">



        12. Report Information



    </div>







    <table class="data">







        <tr>



            <th>Generated By</th>



            <td>



                {{ $generatedBy?->name ?? 'System' }}



            </td>



        </tr>







        <tr>



            <th>Employee Number</th>



            <td>



                {{ $generatedBy?->employee_number ?? 'N/A' }}



            </td>



        </tr>







        <tr>



            <th>Designation / Role</th>



            <td>



                {{ $generatedBy?->designation



                    ?? ucwords(str_replace('_', ' ', $generatedBy?->role ?? 'System'))



                }}



            </td>



        </tr>







        <tr>



            <th>Generated Date / Time</th>



            <td>



                {{ $generatedAt?->format('d M Y, h:i A') ?? now()->format('d M Y, h:i A') }}



            </td>



        </tr>







    </table>







</div>











{{-- ============================================================



     FOOTER



\\============================================================ --}}



<div class="footer-note">







    This report was generated electronically through the NAPVCW



    Digital Case Flow Management System.







    <br>







    It reflects information recorded in the system at the date and



    time of generation.







    <br>







    This report should be handled according to the Authority's



    applicable confidentiality, access-control and records-management



    requirements.







</div>







</body>



</html>
