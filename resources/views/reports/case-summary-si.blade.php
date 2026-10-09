<!DOCTYPE html>



<html lang="si">



<head>



    <meta charset="UTF-8">







    <title>



        නඩු සාරාංශය - {{ $case->case_number }}



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



            font-family: iskoolapota, sans-serif;



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
    | නව වාර්තා සඳහා dg_protection_* ක්ෂේත්‍ර ප්‍රධාන තීරණ දත්ත වේ.
    | පැරණි වාර්තා සඳහා final_protection_* ක්ෂේත්‍ර භාවිතා කරයි.
    |
    */

    $dgProtectionType =
        $case->dg_protection_type
        ?? (
            count($approvedProtectionTypes) === 1
                ? $approvedProtectionTypes[0]
                : null
        );

    $dgProtectionTypeLabel = match($dgProtectionType) {
        'Interim Protection' => 'අතුරු ආරක්ෂාව',
        'Body-to-Body Protection' => 'පුද්ගල-පුද්ගල ආරක්ෂාව',
        'Close Protection' => 'සමීප ආරක්ෂාව',
        'On-site Protection' => 'ස්ථානීය ආරක්ෂාව',
        null => null,
        default => $dgProtectionType,
    };

    $dgProtectionDecisionMaker =
        $case->dgProtectionDecisionMaker?->name
        ?? $case->finalProtectionApprover?->name
        ?? 'සටහන් කර නැත';

    $dgProtectionDecisionAt =
        $case->dg_protection_decided_at
        ?? $case->final_protection_approved_at;








    $assistanceAssignment =



        $case->assignments



            ?->firstWhere('division', 'Assistance Services');







    $assistanceResponsibleOfficer =



        $assistanceAssignment?->assignedUser?->name



        ?? 'Not assigned';





    $statusLabel = match($case->current_status) {

        'Registered' => 'ලියාපදිංචි කර ඇත',

        'Routed' => 'යොමු කර ඇත',

        'Under Processing' => 'ක්‍රියාත්මක වෙමින්',

        'Awaiting Decision' => 'තීරණය බලාපොරොත්තුවෙන්',

        'Closed' => 'වසා ඇත',

        default => $case->current_status,

    };



    $urgencyLabel = match($case->urgency) {

        'Critical' => 'අතිශය හදිසි',

        'Urgent' => 'හදිසි',

        'Normal' => 'සාමාන්‍ය',

        default => $case->urgency,

    };



    $complaintSourceLabel = match($case->complaint_source) {

        'Direct Complaint' => 'සෘජු පැමිණිල්ල',

        'Police Station' => 'පොලිස් ස්ථානය',

        'NAPVCW Police Protection Division' => 'NAPVCW පොලිස් ආරක්ෂණ අංශය',

        'Court Order' => 'අධිකරණ නියෝගය',

        'Commission Order' => 'කොමිෂන් නියෝගය',

        'Other Institution' => 'වෙනත් ආයතනය',

        default => $case->complaint_source,

    };



    $complaintModeLabel = match($case->complaint_mode) {

        'By Hand' => 'අතින් භාරදීම',

        'Post' => 'තැපෑල',

        'Courier' => 'කුරියර්',

        'Email' => 'විද්‍යුත් තැපෑල',

        'Fax' => 'ෆැක්ස්',

        'Telephone / Hotline' => 'දුරකථන / හොට්ලයින්',

        'Official Referral' => 'නිල යොමු කිරීම',

        default => $case->complaint_mode,

    };



    $complaintCategoryLabel = match($case->complaint_category) {

        'Rights / Entitlement Violation' => 'අයිතිවාසිකම් / හිමිකම් උල්ලංඝනය',

        'Protection Request' => 'ආරක්ෂාව සඳහා ඉල්ලීම',

        'Assistance Request' => 'සහාය සඳහා ඉල්ලීම',

        'Offence Information' => 'වරදක් පිළිබඳ තොරතුරු',

        'Court / Commission Order' => 'අධිකරණ / කොමිෂන් නියෝගය',

        'Police Request' => 'පොලිස් ඉල්ලීම',

        'Multi-Division Case' => 'බහු අංශ නඩුව',

        'Other' => 'වෙනත්',

        default => $case->complaint_category,

    };



    $personTypeLabel = match($case->victim_witness_type) {

        'Victim' => 'වින්දිතයා',

        'Witness' => 'සාක්ෂිකරු',

        'Representative' => 'නියෝජිතයා',

        'Other' => 'වෙනත්',

        null => 'සටහන් කර නැත',

        default => $case->victim_witness_type,

    };



    $primaryDivisionLabel = match($case->primary_division) {

        'Board Secretariat' => 'මණ්ඩල ලේකම් කාර්යාලය',

        'Law and Law Enforcement' => 'නීති හා නීතිය ක්‍රියාත්මක කිරීම',

        'Protection Services' => 'ආරක්ෂණ සේවා',

        'Police Protection' => 'පොලිස් ආරක්ෂණය',

        'Assistance Services' => 'සහාය සේවා',

        null => 'සඳහන් කර නැත',

        default => $case->primary_division,

    };



    $recommendedThreatStatusLabel = match($case->threat_assessment_status) {

        'Very High' => 'ඉතා ඉහළ',

        'High' => 'ඉහළ',

        'Low' => 'අඩු',

        'Very Low' => 'ඉතා අඩු',

        null => 'ඇගයීම කර නැත',

        default => $case->threat_assessment_status,

    };



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



                    අපරාධයක වින්දිතයන් සහ සාක්ෂිකරුවන් ආරක්ෂා කිරීම සඳහා වූ ජාතික අධිකාරිය



                </p>







                <div class="system-title">



                    ඩිජිටල් නඩු ප්‍රවාහ කළමනාකරණ පද්ධතිය



                </div>







                <div class="report-title">



                    නඩු සාරාංශ වාර්තාව



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



        වත්මන් තත්ත්වය:



        {{ $statusLabel }}







        |



        හදිසිභාවය:



        {{ $urgencyLabel }}



    </span>







</div>











{{-- ============================================================



     1. MASTER CASE INFORMATION



\\============================================================ --}}



<div class="section">







    <div class="section-title">



        1. ප්‍රධාන නඩු තොරතුරු



    </div>







    <table class="data">







        <tr>



            <th>නඩු අංකය</th>



            <td>{{ $case->case_number }}</td>



        </tr>







        <tr>



            <th>ලැබුණු දිනය</th>



            <td>



                {{ $case->received_date?->format('d M Y') ?? 'සටහන් කර නැත' }}



            </td>



        </tr>







        <tr>



            <th>පැමිණිල්ලේ මූලාශ්‍රය</th>



            <td>{{ $complaintSourceLabel ?: 'සටහන් කර නැත' }}</td>



        </tr>







        <tr>



            <th>පැමිණිල්ල ලැබුණු ක්‍රමය</th>



            <td>{{ $complaintModeLabel ?: 'සටහන් කර නැත' }}</td>



        </tr>







        <tr>



            <th>පැමිණිලි ප්‍රවර්ගය</th>



            <td>{{ $complaintCategoryLabel ?: 'සටහන් කර නැත' }}</td>



        </tr>







        <tr>



            <th>හදිසිභාවය</th>



            <td>{{ $urgencyLabel ?: 'සටහන් කර නැත' }}</td>



        </tr>







        <tr>



            <th>වත්මන් තත්ත්වය</th>



            <td>{{ $statusLabel ?: 'සටහන් කර නැත' }}</td>



        </tr>







        <tr>



            <th>ප්‍රධාන අංශය</th>



            <td>{{ $primaryDivisionLabel }}</td>



        </tr>







        <tr>



            <th>ලියාපදිංචි කළේ</th>



            <td>{{ $case->creator?->name ?? 'පද්ධතිය' }}</td>



        </tr>







        <tr>



            <th>ලියාපදිංචි කළ දිනය / වේලාව</th>



            <td>



                {{ $case->created_at?->format('d M Y, h:i A') ?? 'සටහන් කර නැත' }}



            </td>



        </tr>







    </table>







</div>











{{-- ============================================================



     2. COMPLAINANT / PARTY INFORMATION



\\============================================================ --}}



<div class="section">







    <div class="section-title">



        2. පැමිණිලිකරු / පාර්ශව තොරතුරු



    </div>







    <table class="data">







        <tr>



            <th>පැමිණිලිකරුගේ නම</th>



            <td>{{ $case->complainant_name ?? 'සටහන් කර නැත' }}</td>



        </tr>







        <tr>



            <th>පුද්ගල වර්ගය</th>



            <td>{{ $personTypeLabel }}</td>



        </tr>







        <tr>



            <th>දුරකථන අංකය</th>



            <td>{{ $case->contact_number ?? 'සටහන් කර නැත' }}</td>



        </tr>







        <tr>



            <th>විද්‍යුත් තැපෑල</th>



            <td>{{ $case->email ?? 'සටහන් කර නැත' }}</td>



        </tr>







    </table>







</div>











{{-- ============================================================



     3. COMPLAINT SUMMARY



\\============================================================ --}}



<div class="section">







    <div class="section-title">



        3. පැමිණිල්ලේ සාරාංශය



    </div>







    <div class="summary-box">



        {{ $case->complaint_summary ?? 'පැමිණිලි සාරාංශයක් සටහන් කර නැත.' }}



    </div>







</div>











{{-- ============================================================



     4. DIVISION ASSIGNMENTS



\\============================================================ --}}



<div class="section allow-break">







    <div class="section-title">



        4. අංශ පැවරුම්



    </div>







    <table class="timeline">







        <thead>



            <tr>



                <th>අංශය</th>



                <th>වගකිවයුතු නිලධාරියා</th>



                <th>තත්ත්වය</th>



                <th>පවරා ඇති දිනය</th>



            </tr>



        </thead>







        <tbody>







            @forelse($case->assignments as $assignment)







                <tr>







                    <td>



                        {{ match($assignment->division) {

                            'Board Secretariat' => 'මණ්ඩල ලේකම් කාර්යාලය',

                            'Law and Law Enforcement' => 'නීති හා නීතිය ක්‍රියාත්මක කිරීම',

                            'Protection Services' => 'ආරක්ෂණ සේවා',

                            'Police Protection' => 'පොලිස් ආරක්ෂණය',

                            'Assistance Services' => 'සහාය සේවා',

                            default => $assignment->division,

                        } }}



                    </td>







                    <td>



                        {{ $assignment->assignedUser?->name ?? 'පවරා නැත' }}



                    </td>







                    <td>



                        {{ $assignment->status ?? 'සටහන් කර නැත' }}



                    </td>







                    <td>



                        {{ $assignment->assigned_at?->format('d M Y') ?? 'අදාළ නොවේ' }}



                    </td>







                </tr>







            @empty







                <tr>



                    <td colspan="4">



                        සක්‍රීය අංශ පැවරුම් නොමැත.



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



            5. නීති හා නීතිය ක්‍රියාත්මක කිරීමේ කාර්ය ප්‍රවාහය



        </div>







        <table class="data">







            <tr>



                <th>RE අංකය</th>



                <td>{{ $case->legalDetail->re_number ?? 'පවරා නැත' }}</td>



            </tr>







            <tr>



                <th>නීති තත්ත්වය</th>



                <td>{{ match($case->legalDetail->legal_status) {

                        'Received by Legal Division' => 'නීති අංශයට ලැබී ඇත',

                        'RE Number Assigned' => 'RE අංකය පවරා ඇත',

                        'LO / IO Assigned' => 'LO / IO පවරා ඇත',

                        'Inquiry Started' => 'විමර්ශනය ආරම්භ කර ඇත',

                        'Observation Requested' => 'නිරීක්ෂණ ඉල්ලා ඇත',

                        'Awaiting Observation' => 'නිරීක්ෂණ බලාපොරොත්තුවෙන්',

                        'First Reminder Sent' => 'පළමු මතක් කිරීම යවා ඇත',

                        'Second Reminder Sent' => 'දෙවන මතක් කිරීම යවා ඇත',

                        'Field Visit Required' => 'ක්ෂේත්‍ර සංචාරයක් අවශ්‍යයි',

                        'Investigation In Progress' => 'විමර්ශනය ක්‍රියාත්මක වෙමින්',

                        'IO Report Prepared' => 'IO වාර්තාව සකස් කර ඇත',

                        'Legal Review' => 'නීති සමාලෝචනය',

                        'Case Conference Recommended' => 'නඩු සම්මන්ත්‍රණය නිර්දේශ කර ඇත',

                        'Case Conference Scheduled' => 'නඩු සම්මන්ත්‍රණය සැලසුම් කර ඇත',

                        'Case Conference Conducted' => 'නඩු සම්මන්ත්‍රණය පවත්වා ඇත',

                        'Board Submission Required' => 'මණ්ඩලයට ඉදිරිපත් කිරීම අවශ්‍යයි',

                        'Submitted to Board' => 'මණ්ඩලයට ඉදිරිපත් කර ඇත',

                        'Awaiting Board Decision' => 'මණ්ඩල තීරණය බලාපොරොත්තුවෙන්',

                        'Board Decision Issued' => 'මණ්ඩල තීරණය නිකුත් කර ඇත',

                        'Closed' => 'වසා ඇත',

                        null => 'සටහන් කර නැත',

                        default => $case->legalDetail->legal_status,

                    } }}</td>



            </tr>







            <tr>



                <th>නීති නිලධාරී</th>



                <td>



                    {{ $case->legalDetail->legalOfficer?->name ?? 'පවරා නැත' }}



                </td>



            </tr>







            <tr>



                <th>විමර්ශන නිලධාරී</th>



                <td>



                    {{ $case->legalDetail->investigationOfficer?->name ?? 'පවරා නැත' }}



                </td>



            </tr>







            <tr>



                <th>විමර්ශනය ආරම්භ කළ දිනය</th>



                <td>



                    {{ $case->legalDetail->inquiry_started_date?->format('d M Y') ?? 'අදාළ නොවේ' }}



                </td>



            </tr>







            <tr>



                <th>නිරීක්ෂණ ඉල්ලූ දිනය</th>



                <td>



                    {{ $case->legalDetail->observation_requested_date?->format('d M Y') ?? 'අදාළ නොවේ' }}



                </td>



            </tr>







            <tr>



                <th>නිරීක්ෂණ අවසන් දිනය</th>



                <td>



                    {{ $case->legalDetail->observation_due_date?->format('d M Y') ?? 'අදාළ නොවේ' }}



                </td>



            </tr>







            <tr>



                <th>විමර්ශන සොයාගැනීම්</th>



                <td>{{ $case->legalDetail->io_findings ?? 'සටහන් කර නැත' }}</td>



            </tr>







            <tr>



                <th>නීති නිර්දේශය</th>



                <td>



                    {{ $case->legalDetail->legal_recommendation ?? 'සටහන් කර නැත' }}



                </td>



            </tr>







            <tr>



                <th>නඩු සම්මන්ත්‍රණය</th>



                <td>



                    {{ $case->legalDetail->case_conference_required ? 'අවශ්‍යයි' : 'අවශ්‍ය නොවේ' }}



                </td>



            </tr>







            <tr>



                <th>මණ්ඩලයට ඉදිරිපත් කිරීම</th>



                <td>



                    {{ $case->legalDetail->board_submission_required ? 'අවශ්‍යයි' : 'අවශ්‍ය නොවේ' }}



                </td>



            </tr>







            <tr>



                <th>මණ්ඩල තීරණය</th>



                <td>



                    {{ $case->legalDetail->board_decision ?? 'සටහන් කර නැත' }}



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



            6. ආරක්ෂණ සේවා කාර්ය ප්‍රවාහය



        </div>







        <table class="data">







            <tr>



                <th>ආරක්ෂණ තත්ත්වය</th>



                <td>



                    {{ match($case->protectionDetail->protection_status) {

                        'Protection Request Received' => 'ආරක්ෂණ ඉල්ලීම ලැබී ඇත',

                        'Protection Officer Assigned' => 'ආරක්ෂණ නිලධාරියෙකු පවරා ඇත',

                        'Threat Assessment Requested' => 'තර්ජන ඇගයීම ඉල්ලා ඇත',

                        'Awaiting Threat Assessment' => 'තර්ජන ඇගයීම බලාපොරොත්තුවෙන්',

                        'Threat Assessment Received' => 'තර්ජන ඇගයීම ලැබී ඇත',

                        'Protection Decision Pending' => 'ආරක්ෂණ තීරණය බලාපොරොත්තුවෙන්',

                        'Protection Active' => 'ආරක්ෂාව සක්‍රීයයි',

                        'Protection Under Review' => 'ආරක්ෂාව සමාලෝචනය වෙමින්',

                        'Protection Continued' => 'ආරක්ෂාව දිගටම ක්‍රියාත්මකයි',

                        'Protection Withdrawn' => 'ආරක්ෂාව ඉවත් කර ඇත',

                        'Protection Terminated' => 'ආරක්ෂාව අවසන් කර ඇත',

                        'Closed' => 'වසා ඇත',

                        null => 'සටහන් කර නැත',

                        default => $case->protectionDetail->protection_status,

                    } }}



                </td>



            </tr>







            <tr>



                <th>ආරක්ෂණ නිලධාරී</th>



                <td>



                    {{ $case->protectionDetail->protectionOfficer?->name ?? 'පවරා නැත' }}



                </td>



            </tr>







            <tr>



                <th>තර්ජන වර්ගය</th>



                <td>



                    {{ $case->protectionDetail->threat_type ?? 'සටහන් කර නැත' }}



                </td>



            </tr>







            <tr>



                <th>ක්‍රියාකාරී තර්ජන ඇගයීම් තත්ත්වය</th>



                <td>



                    {{ match($case->protectionDetail->threat_assessment_status) {

                        'Not Requested' => 'ඉල්ලා නැත',

                        'Request Sent' => 'ඉල්ලීම යවා ඇත',

                        'Request Received' => 'ඉල්ලීම ලැබී ඇත',

                        'Assessment In Progress' => 'ඇගයීම ක්‍රියාත්මක වෙමින්',

                        'Assessment Completed' => 'ඇගයීම අවසන් කර ඇත',

                        'Assessment Received' => 'ඇගයීම ලැබී ඇත',

                        null => 'සටහන් කර නැත',

                        default => $case->protectionDetail->threat_assessment_status,

                    } }}



                </td>



            </tr>







            <tr>



                <th>ක්‍රියාකාරී තර්ජන මට්ටම</th>



                <td>



                    {{ match($case->protectionDetail->threat_level) {

                        'Low' => 'අඩු',

                        'Moderate' => 'මධ්‍යම',

                        'High' => 'ඉහළ',

                        'Critical' => 'අතිශය ඉහළ',

                        null => 'තීරණය කර නැත',

                        default => $case->protectionDetail->threat_level,

                    } }}



                </td>



            </tr>







            <tr>



                <th>අතුරු ආරක්ෂණ ඉල්ලීම</th>



                <td>



                    {{ $case->protectionDetail->interim_protection_required ? 'අවශ්‍යයි' : 'අවශ්‍ය නොවේ' }}



                </td>



            </tr>







            <tr>



                <th>ආරක්ෂණ තීරණය / සටහන්</th>



                <td>



                    {{ $case->protectionDetail->protection_decision ?? 'සටහන් කර නැත' }}



                </td>



            </tr>







            <tr>



                <th>ආරක්ෂණ ප්‍රතිඵලය</th>



                <td>



                    {{ match($case->protectionDetail->protection_outcome) {

                        'Protection Provided' => 'ආරක්ෂාව ලබා දී ඇත',

                        'Protection Continued' => 'ආරක්ෂාව දිගටම පවත්වා ඇත',

                        'Protection Withdrawn' => 'ආරක්ෂාව ඉවත් කර ඇත',

                        'Protection Terminated' => 'ආරක්ෂාව අවසන් කර ඇත',

                        'Transferred / Referred' => 'මාරු / යොමු කර ඇත',

                        'Closed' => 'වසා ඇත',

                        null => 'තීරණය කර නැත',

                        default => $case->protectionDetail->protection_outcome,

                    } }}



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



            7. පොලිස් ආරක්ෂණ තර්ජන ඇගයීම



        </div>







        <table class="data">







            <tr>



                <th>ඇගයීම් තත්ත්වය</th>



                <td>



                    {{ match($case->policeProtectionDetail->assessment_status) {

                        'Awaiting Request' => 'ඉල්ලීම බලාපොරොත්තුවෙන්',

                        'Request Received' => 'ඉල්ලීම ලැබී ඇත',

                        'Assessment Started' => 'ඇගයීම ආරම්භ කර ඇත',

                        'Assessment In Progress' => 'ඇගයීම ක්‍රියාත්මක වෙමින්',

                        'Assessment Completed' => 'ඇගයීම අවසන් කර ඇත',

                        null => 'සටහන් කර නැත',

                        default => $case->policeProtectionDetail->assessment_status,

                    } }}



                </td>



            </tr>







            <tr>



                <th>ඇගයීම් නිලධාරියා</th>



                <td>



                    {{ $case->policeProtectionDetail->officer?->name ?? 'පවරා නැත' }}



                </td>



            </tr>







            <tr>



                <th>ඉල්ලීම ලැබුණු දිනය</th>



                <td>



                    {{ $case->policeProtectionDetail->request_received_date?->format('d M Y') ?? 'අදාළ නොවේ' }}



                </td>



            </tr>







            <tr>



                <th>ඇගයීම අවසන් කළ දිනය</th>



                <td>



                    {{ $case->policeProtectionDetail->assessment_completed_date?->format('d M Y') ?? 'අදාළ නොවේ' }}



                </td>



            </tr>







            <tr>



                <th>ඇගයීම් තර්ජන මට්ටම</th>



                <td>



                    {{ match($case->policeProtectionDetail->threat_level) {

                        'Low' => 'අඩු',

                        'Moderate' => 'මධ්‍යම',

                        'High' => 'ඉහළ',

                        'Critical' => 'අතිශය ඉහළ',

                        null => 'තීරණය කර නැත',

                        default => $case->policeProtectionDetail->threat_level,

                    } }}



                </td>



            </tr>







            <tr>



                <th>ඇගයීම් සාරාංශය</th>



                <td>



                    {{ $case->policeProtectionDetail->assessment_summary ?? 'සටහන් කර නැත' }}



                </td>



            </tr>







            <tr>



                <th>ඇගයීම් නිර්දේශය</th>



                <td>



                    {{ $case->policeProtectionDetail->recommendation ?? 'සටහන් කර නැත' }}



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



            8. සහාය සේවා කාර්ය ප්‍රවාහය



        </div>







        <table class="data">







            <tr>



                <th>සහාය තත්ත්වය</th>



                <td>



                    {{ match($case->assistanceDetail->assistance_status) {

                        'Assistance Request Received' => 'සහාය ඉල්ලීම ලැබී ඇත',

                        'Assistance Type Identified' => 'සහාය වර්ගය හඳුනාගෙන ඇත',

                        'Officer Assigned' => 'නිලධාරියෙකු පවරා ඇත',

                        'Referral Required' => 'යොමු කිරීම අවශ්‍යයි',

                        'Referral Initiated' => 'යොමු කිරීම ආරම්භ කර ඇත',

                        'Under Follow-up' => 'පසු විපරම් යටතේ',

                        'Assistance Completed' => 'සහාය කටයුතු අවසන් කර ඇත',

                        'Closed' => 'වසා ඇත',

                        null => 'සටහන් කර නැත',

                        default => $case->assistanceDetail->assistance_status,

                    } }}



                </td>



            </tr>







            <tr>



                <th>වගකිවයුතු නිලධාරියා</th>



                <td>



                    {{ $assistanceResponsibleOfficer }}



                </td>



            </tr>







            <tr>



                <th>සහාය වර්ගය</th>



                <td>



                    {{ match($case->assistanceDetail->assistance_type) {

                        'Medical Assistance' => 'වෛද්‍ය සහාය',

                        'Counselling' => 'උපදේශන සේවාව',

                        'Rehabilitation' => 'පුනරුත්ථාපන සහාය',

                        'Victim Impact Statement Support' => 'වින්දිත බලපෑම් ප්‍රකාශය සඳහා සහාය',

                        'Court Participation Support' => 'අධිකරණ සහභාගීත්ව සහාය',

                        'Compensation Related Support' => 'වන්දි සම්බන්ධ සහාය',

                        'Dependent / Next-of-Kin Support' => 'යැපෙන්නා / ආසන්න ඥාතියා සඳහා සහාය',

                        'Remote Testimony Support' => 'දුරස්ථ සාක්ෂි දීම සඳහා සහාය',

                        'Other Assistance' => 'වෙනත් සහාය',

                        null => 'සටහන් කර නැත',

                        default => $case->assistanceDetail->assistance_type,

                    } }}



                </td>



            </tr>







            <tr>



                <th>යොමු කිරීම අවශ්‍යද</th>



                <td>



                    {{ $case->assistanceDetail->referral_required ? 'ඔව්' : 'නැත' }}



                </td>



            </tr>







            <tr>



                <th>යොමු කළ ස්ථානය / ආයතනය</th>



                <td>



                    {{ $case->assistanceDetail->referred_to ?? 'අදාළ නොවේ' }}



                </td>



            </tr>







            <tr>



                <th>වත්මන් ක්‍රියාව</th>



                <td>



                    {{ $case->assistanceDetail->current_action ?? 'සටහන් කර නැත' }}



                </td>



            </tr>







            <tr>



                <th>ප්‍රතිඵලය</th>



                <td>



                    {{ $case->assistanceDetail->assistance_outcome ?? 'සටහන් කර නැත' }}



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



        9. පොලිස් ආරක්ෂණ අධ්‍යක්ෂගේ තර්ජන නිර්දේශය



    </div>







    <table class="data">







        <tr>



            <th>නිර්දේශිත තර්ජන තත්ත්වය</th>



            <td>



                <strong>



                    {{ $recommendedThreatStatusLabel }}



                </strong>



            </td>



        </tr>







        <tr>



            <th>නිර්දේශ කළේ</th>



            <td>



                {{ $case->threatAssessmentDirector?->name ?? 'සටහන් කර නැත' }}



            </td>



        </tr>







        <tr>



            <th>තනතුර</th>



            <td>



                අධ්‍යක්ෂ - පොලිස් ආරක්ෂණය



            </td>



        </tr>







        <tr>



            <th>නිර්දේශ කළ දිනය / වේලාව</th>



            <td>



                {{ $case->threat_assessment_at



                    ? $case->threat_assessment_at->format('d M Y, h:i A')



                    : 'සටහන් කර නැත'



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
        10. අධ්‍යක්ෂ ජනරාල්ගේ ආරක්ෂණ තීරණය
    </div>

    <table class="data">

        <tr>
            <th>තර්ජන ඇගයීම් නිර්දේශය</th>
            <td>
                <strong>{{ $recommendedThreatStatusLabel }}</strong>
            </td>
        </tr>

        <tr>
            <th>අධ්‍යක්ෂ ජනරාල් තෝරාගත් ආරක්ෂණ වර්ගය</th>
            <td>
                @if($dgProtectionTypeLabel)
                    <span class="decision-approved">
                        {{ $dgProtectionTypeLabel }}
                    </span>
                @elseif($case->threat_assessment_status)
                    <span class="decision-pending">
                        අධ්‍යක්ෂ ජනරාල්ගේ ආරක්ෂණ තීරණය බලාපොරොත්තුවෙන්.
                    </span>
                @else
                    <span class="decision-not-approved">
                        තර්ජන ඇගයීම් නිර්දේශය බලාපොරොත්තුවෙන්.
                    </span>
                @endif
            </td>
        </tr>

        <tr>
            <th>තීරණය ලබා දුන්නේ</th>
            <td>{{ $dgProtectionDecisionMaker }}</td>
        </tr>

        <tr>
            <th>තීරණය ලබා දුන් දිනය / වේලාව</th>
            <td>
                {{
                    $dgProtectionDecisionAt
                        ? $dgProtectionDecisionAt->format('d M Y, h:i A')
                        : 'සටහන් කර නැත'
                }}
            </td>
        </tr>

        <tr>
            <th>අතුරු ආරක්ෂණ අනුමැතිය</th>
            <td>
                @if($case->interim_protection_approved)
                    <span class="decision-approved">අනුමත කර ඇත</span>
                @else
                    <span class="decision-not-approved">අනුමත කර නැත</span>
                @endif
            </td>
        </tr>

        <tr>
            <th>අතුරු ආරක්ෂාව අනුමත කළේ</th>
            <td>{{ $case->interimProtectionApprover?->name ?? 'සටහන් කර නැත' }}</td>
        </tr>

        <tr>
            <th>අතුරු අනුමැතියේ දිනය / වේලාව</th>
            <td>
                {{
                    $case->interim_protection_approved_at
                        ? $case->interim_protection_approved_at->format('d M Y, h:i A')
                        : 'සටහන් කර නැත'
                }}
            </td>
        </tr>

        @if(count($approvedProtectionTypes) > 1)
            <tr>
                <th>පැරණි අනුමත ආරක්ෂණ ක්‍රියාමාර්ග</th>
                <td>
                    <ul class="protection-list">
                        @foreach($approvedProtectionTypes as $protectionType)
                            <li>
                                {{
                                    match($protectionType) {
                                        'Interim Protection' => 'අතුරු ආරක්ෂාව',
                                        'Close Protection' => 'සමීප ආරක්ෂාව',
                                        'Body-to-Body Protection' => 'පුද්ගල-පුද්ගල ආරක්ෂාව',
                                        'On-site Protection' => 'ස්ථානීය ආරක්ෂාව',
                                        default => $protectionType,
                                    }
                                }}
                            </li>
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



        11. හවුල් නඩු ක්‍රියාකාරකම් කාලරේඛාව



    </div>







    <table class="timeline">







        <thead>



            <tr>



                <th style="width: 14%;">දිනය</th>



                <th style="width: 18%;">අංශය</th>



                <th style="width: 17%;">තත්ත්වය</th>



                <th style="width: 25%;">ක්‍රියාව</th>



                <th style="width: 26%;">සටහන් / පරිශීලකයා</th>



            </tr>



        </thead>







        <tbody>







            @forelse($case->statusHistory as $history)







                <tr>







                    <td>



                        {{ $history->created_at?->format('d M Y') ?? 'අදාළ නොවේ' }}







                        <br>







                        <span class="muted">



                            {{ $history->created_at?->format('h:i A') ?? '' }}



                        </span>



                    </td>







                    <td>



                        {{ match($history->division) {

                            'Board Secretariat' => 'මණ්ඩල ලේකම් කාර්යාලය',

                            'Law and Law Enforcement' => 'නීති හා නීතිය ක්‍රියාත්මක කිරීම',

                            'Protection Services' => 'ආරක්ෂණ සේවා',

                            'Police Protection' => 'පොලිස් ආරක්ෂණය',

                            'Assistance Services' => 'සහාය සේවා',

                            null => 'පද්ධතිය',

                            default => $history->division,

                        } }}



                    </td>







                    <td>



                        {{ $history->status ?? 'සටහන් කර නැත' }}



                    </td>







                    <td>



                        {{ $history->action_taken ?? 'සටහන් කර නැත' }}



                    </td>







                    <td>



                        {{ $history->remarks ?? '-' }}







                        <br>







                        <span class="muted">



                            යාවත්කාලීන කළේ:



                            {{ $history->updatedBy?->name ?? 'පද්ධතිය' }}



                        </span>



                    </td>







                </tr>







            @empty







                <tr>



                    <td colspan="5">



                        නඩු ක්‍රියාකාරකම් සටහන් කර නැත.



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



        12. වාර්තා තොරතුරු



    </div>







    <table class="data">







        <tr>



            <th>වාර්තාව ජනනය කළේ</th>



            <td>



                {{ $generatedBy?->name ?? 'පද්ධතිය' }}



            </td>



        </tr>







        <tr>



            <th>සේවක අංකය</th>



            <td>



                {{ $generatedBy?->employee_number ?? 'අදාළ නොවේ' }}



            </td>



        </tr>







        <tr>



            <th>තනතුර / භූමිකාව</th>



            <td>



                {{ $generatedBy?->designation



                    ?? (($generatedBy?->role) ? ucwords(str_replace('_', ' ', $generatedBy->role)) : 'පද්ධතිය')



                }}



            </td>



        </tr>







        <tr>



            <th>ජනනය කළ දිනය / වේලාව</th>



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



    ඩිජිටල් නඩු ප්‍රවාහ කළමනාකරණ පද්ධතිය.







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
