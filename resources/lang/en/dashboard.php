<?php

return [
    'not_recorded' => 'Not recorded',
    'not_assessed' => 'Not assessed',

    'months' => [
        'january' => 'January',
        'february' => 'February',
        'march' => 'March',
        'april' => 'April',
        'may' => 'May',
        'june' => 'June',
        'july' => 'July',
        'august' => 'August',
        'september' => 'September',
        'october' => 'October',
        'november' => 'November',
        'december' => 'December',
    ],

    'dg' => [
        'role' => 'Director General',
        'title' => 'Executive Case Decision Dashboard',
        'description' => 'Monitor institution-wide case progress, make protection decisions and analyse the current case registry for management decisions.',
        'open_case_registry' => 'Open Case Registry',

        'stats' => [
            'total' => 'Total Cases',
            'registered' => 'Registered',
            'routed' => 'Routed',
            'processing' => 'Under Processing',
            'awaiting_decision' => 'Awaiting Decision',
            'closed' => 'Closed',
        ],

        'decision_queue' => [
            'eyebrow' => 'Director General Decision Queue',
            'title' => 'Protection Decisions Requiring Action',
            'description' => 'Select the protection type for cases that have received a Director - Police Protection threat recommendation.',
            'pending' => 'pending',
            'case' => 'Case',
            'threat_status' => 'Threat Status',
            'recommended_by' => 'Recommended By',
            'protection_type' => 'Protection Type',
            'action' => 'Action',
            'select_type' => 'Select protection type',
            'confirm' => 'Confirm Decision',
            'empty_title' => 'No protection decisions are pending',
            'empty_description' => 'Cases will appear here after the Director - Police Protection records a threat recommendation.',
        ],

        'protection_types' => [
            'interim' => 'Interim Protection',
            'body_to_body' => 'Body-to-Body Protection',
            'close' => 'Close Protection',
        ],

        'analytics' => [
            'eyebrow' => 'Decision Analytics',
            'title' => 'Case Registry Statistics',
            'description' => 'Change the period and analytical variable to review the case registry from different management perspectives.',
            'period' => 'Period',
            'all_time' => 'All Time',
            'year' => 'Year',
            'month' => 'Month',
            'variable' => 'Analysis Variable',
            'apply' => 'Apply Statistics',
            'status_pie' => 'Cases by Status',
            'status_pie_description' => 'Distribution of cases by their current lifecycle status.',
            'bar_title' => 'Cases by :variable',
            'bar_description' => 'Current registry distribution for the selected analytical variable.',
            'no_data' => 'No case data available for the selected filters.',
            'cases' => 'Cases',
        ],

        'dimensions' => [
            'current_status' => 'Current Status',
            'urgency' => 'Urgency',
            'complaint_category' => 'Complaint Category',
            'primary_division' => 'Primary Division',
            'threat_assessment_status' => 'Threat Assessment Status',
            'protection_type' => 'DG Protection Type',
        ],
    ],
];
