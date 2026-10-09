<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Case Activity Monitoring
    |--------------------------------------------------------------------------
    |
    | DCFMS does not impose fixed completion targets on victim, witness,
    | protection, assistance, legal, or police protection cases.
    |
    | Case duration can vary depending on the circumstances of the person,
    | threat assessments, police responses, court proceedings, counselling,
    | protection arrangements, external agencies, and other case-specific
    | factors.
    |
    | These settings are therefore used only for operational monitoring and
    | follow-up awareness. They must not be interpreted as statutory
    | deadlines, service-level agreements, or guaranteed completion periods.
    |
    */

    'case_monitoring' => [

        /*
        |--------------------------------------------------------------------------
        | Follow-Up Review Threshold
        |--------------------------------------------------------------------------
        |
        | A non-closed case may be highlighted for follow-up review when no
        | recorded case activity has occurred for this number of days.
        |
        | This value is only a monitoring indicator. It does not mean that the
        | case is overdue or that the case should have been completed within
        | this period.
        |
        */

        'activity_review_after_days' => 7,

    ],

];