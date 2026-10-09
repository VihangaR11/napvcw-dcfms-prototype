<?php















namespace App\Http\Controllers;















use App\Models\DcfmsCase;







use Barryvdh\DomPDF\Facade\Pdf;

use Mpdf\Mpdf;

use Mpdf\Output\Destination;

use Mpdf\Config\ConfigVariables;

use Mpdf\Config\FontVariables;















class CaseReportController extends Controller







{







    /*







    |--------------------------------------------------------------------------







    | Authorize Case Report Access







    |--------------------------------------------------------------------------







    |







    | Institution-wide:







    | - Chairman







    | - Director General







    | - Board Secretary







    | - Policy Director







    |







    | Division Directors:







    | - See reports for cases routed to their division







    |







    | Officers:







    | - See reports only for assigned cases







    |







    | System Admin:







    | - Technical read access for the prototype







    |







    */















    private function authorizeCaseAccess(







        DcfmsCase $case







    ): void {







        $user = auth()->user();















        abort_unless(







            $user,







            401







        );























        /*







        |--------------------------------------------------------------------------







        | Institution-Wide Read Access







        |--------------------------------------------------------------------------







        */















        if (in_array(







            $user->role,







            [







                'chairman',







                'director_general',







                'board_secretary',







                'policy_director',







                'system_admin',







            ],







            true







        )) {







            return;







        }























        /*







        |--------------------------------------------------------------------------







        | Division Director Access







        |--------------------------------------------------------------------------







        */















        $directorDivision =







            match ($user->role) {















                'legal_director' =>







                    'Law and Law Enforcement',















                'protection_director' =>







                    'Protection Services',















                'police_protection_director' =>







                    'Police Protection',















                default =>







                    null,







            };























        if ($directorDivision) {















            $hasDivisionAccess =







                $case->assignments()







                    ->where(







                        'division',







                        $directorDivision







                    )







                    ->where(







                        'is_active',







                        true







                    )







                    ->exists();























            abort_unless(







                $hasDivisionAccess,







                403







            );























            return;







        }























        /*







        |--------------------------------------------------------------------------







        | Assigned Officer Access







        |--------------------------------------------------------------------------







        |







        | Legal Officers and Protection Officers may also be assigned







        | to Assistance Services.







        |







        */















        if (in_array(







            $user->role,







            [







                'legal_officer',







                'investigation_officer',







                'protection_officer',







                'police_protection_officer',







            ],







            true







        )) {















            $hasAssignment =







                $case->assignments()







                    ->where(







                        'assigned_user_id',







                        $user->id







                    )







                    ->where(







                        'is_active',







                        true







                    )







                    ->exists();























            abort_unless(







                $hasAssignment,







                403







            );























            return;







        }























        abort(403);







    }























    /*







    |--------------------------------------------------------------------------







    | Load Case Report Data







    |--------------------------------------------------------------------------







    */















    private function loadCaseData(







        DcfmsCase $case







    ): DcfmsCase {















        return $case->load([















            /*







            |--------------------------------------------------------------------------







            | Core Case Data







            |--------------------------------------------------------------------------







            */















            'creator',















            'threatAssessmentDirector',

            'dgProtectionDecisionMaker',

            'interimProtectionApprover',

            'finalProtectionApprover',























            /*







            |--------------------------------------------------------------------------







            | Division Assignments







            |--------------------------------------------------------------------------







            */















            'assignments' => function ($query) {















                $query







                    ->where(







                        'is_active',







                        true







                    )







                    ->orderBy(







                        'division'







                    );







            },















            'assignments.assignedUser',























            /*







            |--------------------------------------------------------------------------







            | Shared Timeline







            |--------------------------------------------------------------------------







            */















            'statusHistory' => function ($query) {















                $query->orderBy(







                    'created_at'







                );







            },















            'statusHistory.updatedBy',























            /*







            |--------------------------------------------------------------------------







            | Law & Law Enforcement







            |--------------------------------------------------------------------------







            */















            'legalDetail.legalOfficer',







            'legalDetail.investigationOfficer',























            /*







            |--------------------------------------------------------------------------







            | Protection Services







            |--------------------------------------------------------------------------







            */















            'protectionDetail.protectionOfficer',























            /*







            |--------------------------------------------------------------------------







            | Police Protection







            |--------------------------------------------------------------------------







            */















            'policeProtectionDetail.officer',























            /*







            |--------------------------------------------------------------------------







            | Assistance Services







            |--------------------------------------------------------------------------







            |







            | There is no Assistance Officer role.







            |







            | Assistance activities are handled collaboratively by







            | Legal Officers and Protection Officers.







            |







            */















            'assistanceDetail',







        ]);







    }























    /*







    |--------------------------------------------------------------------------







    | Create PDF Instance







    |--------------------------------------------------------------------------







    */



    private function createEnglishPdf(

        DcfmsCase $case

    ) {



        /*



        |--------------------------------------------------------------------------



        | Capture Actual Report Generation Time



        |--------------------------------------------------------------------------



        |



        | This timestamp is created when the PDF request is processed.



        | It is intentionally NOT taken from the case created/updated/received



        | timestamps.



        |



        | DCFMS is deployed for NAPVCW Sri Lanka, therefore the report timestamp



        | is rendered in Asia/Colombo local time.



        |



        */







        $generatedAt =



            now('Asia/Colombo');







        $generatedBy =



            auth()->user();











        /*



        |--------------------------------------------------------------------------



        | DomPDF Working Directories



        |--------------------------------------------------------------------------



        */







        $tempDirectory =



            storage_path(



                'app/dompdf/temp'



            );







        $fontDirectory =



            storage_path(



                'app/dompdf/fonts'



            );











        /*



        |--------------------------------------------------------------------------



        | Ensure Required DomPDF Directories Exist



        |--------------------------------------------------------------------------



        */







        foreach (



            [



                $tempDirectory,



                $fontDirectory,



            ]



            as $directory



        ) {



            if (!is_dir($directory)) {



                mkdir(



                    $directory,



                    0775,



                    true



                );



            }



        }











        /*



        |--------------------------------------------------------------------------



        | Generate PDF



        |--------------------------------------------------------------------------



        */







        $pdf = Pdf::loadView(



            'reports.case-summary-en',



            [



                'case' =>



                    $case,







                'generatedBy' =>



                    $generatedBy,







                'generatedAt' =>



                    $generatedAt,



            ]



        );











        /*



        |--------------------------------------------------------------------------



        | PDF Configuration



        |--------------------------------------------------------------------------



        */







        $pdf->setPaper(



            'a4',



            'portrait'



        );











        $pdf->setOptions([



            'defaultFont' =>



                'DejaVu Sans',







            'isRemoteEnabled' =>



                false,







            'isHtml5ParserEnabled' =>



                true,







            'tempDir' =>



                $tempDirectory,







            'fontDir' =>



                $fontDirectory,







            'fontCache' =>



                $fontDirectory,



        ]);











        return $pdf;



    }













    /*

    |--------------------------------------------------------------------------

    | Create Sinhala PDF Binary

    |--------------------------------------------------------------------------

    |

    | Sinhala uses a separate mPDF renderer because complex Sinhala shaping

    | is not reliable through the DomPDF path used for the English report.

    |

    | Required font:

    | resources/fonts/NotoSansSinhala-Regular.ttf

    |

    */



    private function createSinhalaPdf(

        DcfmsCase $case

    ): string {



        $generatedAt =

            now('Asia/Colombo');



        $generatedBy =

            auth()->user();



        $fontFile =

            resource_path(

                'fonts/NotoSansSinhala-Regular.ttf'

            );



        abort_unless(

            is_file($fontFile),

            500,

            'Sinhala PDF font is missing. Add NotoSansSinhala-Regular.ttf to resources/fonts.'

        );



        $defaultConfig =

            (new ConfigVariables())

                ->getDefaults();



        $defaultFontConfig =

            (new FontVariables())

                ->getDefaults();



        $tempDirectory =

            storage_path(

                'app/mpdf/temp'

            );



        if (!is_dir($tempDirectory)) {

            mkdir(

                $tempDirectory,

                0775,

                true

            );

        }



        $mpdf =

            new Mpdf([

                'mode' =>

                    'utf-8',



                'format' =>

                    'A4',



                'orientation' =>

                    'P',



                'tempDir' =>

                    $tempDirectory,



                'fontDir' =>

                    array_merge(

                        $defaultConfig['fontDir'],

                        [

                            resource_path(

                                'fonts'

                            ),

                        ]

                    ),



                'fontdata' =>

                    $defaultFontConfig['fontdata'] + [

                        'notosanssinhala' => [

                            'R' =>

                                'NotoSansSinhala-Regular.ttf',



                            'useOTL' =>

                                0xFF,



                            'useKashida' =>

                                75,

                        ],

                    ],



                'default_font' =>

                    'notosanssinhala',



                'autoScriptToLang' =>

                    true,



                'autoLangToFont' =>

                    true,



                'margin_left' =>

                    12,



                'margin_right' =>

                    12,



                'margin_top' =>

                    12,



                'margin_bottom' =>

                    15,

            ]);



        $html =

            view(

                'reports.case-summary-si',

                [

                    'case' =>

                        $case,



                    'generatedBy' =>

                        $generatedBy,



                    'generatedAt' =>

                        $generatedAt,

                ]

            )->render();



        $mpdf->WriteHTML(

            $html

        );



        return

            $mpdf->Output(

                '',

                Destination::STRING_RETURN

            );

    }





    /*

    |--------------------------------------------------------------------------

    | Sinhala PDF HTTP Response

    |--------------------------------------------------------------------------

    */



    private function sinhalaPdfResponse(

        DcfmsCase $case,

        bool $download

    ) {



        $binary =

            $this->createSinhalaPdf(

                $case

            );



        $fileName =

            $this->buildFileName(

                $case

            );



        $disposition =

            $download

                ? 'attachment'

                : 'inline';



        return response(

            $binary,

            200,

            [

                'Content-Type' =>

                    'application/pdf',



                'Content-Disposition' =>

                    $disposition .

                    '; filename="' .

                    $fileName .

                    '"',



                'Content-Length' =>

                    strlen($binary),



                'Cache-Control' =>

                    'private, no-store, no-cache, must-revalidate',

            ]

        );

    }





/*



    |--------------------------------------------------------------------------



    | Safe PDF File Name











    |--------------------------------------------------------------------------







    */















    private function buildFileName(







        DcfmsCase $case







    ): string {















        $safeCaseNumber =







            preg_replace(







                '/[^A-Za-z0-9\\\\\\\\-_]/',







                '-',







                (string)







                $case->case_number







            );























        return







            'NAPVCW_Case_Summary_' .







            $safeCaseNumber .







            '.pdf';







    }























    /*







    |--------------------------------------------------------------------------







    | Preview Case Summary PDF







    |--------------------------------------------------------------------------







    */















    public function preview(







        DcfmsCase $case







    ) {







        /*







        |--------------------------------------------------------------------------







        | Access Control







        |--------------------------------------------------------------------------







        */















        $this->authorizeCaseAccess(







            $case







        );























        /*







        |--------------------------------------------------------------------------







        | Load Report Data







        |--------------------------------------------------------------------------







        */















        $case =







            $this->loadCaseData(







                $case







            );























        /*







        |--------------------------------------------------------------------------







        | Generate PDF







        |--------------------------------------------------------------------------







        */















        if (app()->getLocale() === 'si') {



            return

                $this->sinhalaPdfResponse(

                    $case,

                    false

                );

        }



        $pdf =

            $this->createEnglishPdf(

                $case

            );























        /*







        |--------------------------------------------------------------------------







        | Stream to Browser







        |--------------------------------------------------------------------------







        */















        return $pdf->stream(







            $this->buildFileName(







                $case







            )







        );







    }























    /*







    |--------------------------------------------------------------------------







    | Download Case Summary PDF







    |--------------------------------------------------------------------------







    */















    public function download(







        DcfmsCase $case







    ) {







        /*







        |--------------------------------------------------------------------------







        | Access Control







        |--------------------------------------------------------------------------







        */















        $this->authorizeCaseAccess(







            $case







        );























        /*







        |--------------------------------------------------------------------------







        | Load Report Data







        |--------------------------------------------------------------------------







        */















        $case =







            $this->loadCaseData(







                $case







            );























        /*







        |--------------------------------------------------------------------------







        | Generate PDF







        |--------------------------------------------------------------------------







        */















        if (app()->getLocale() === 'si') {



            return

                $this->sinhalaPdfResponse(

                    $case,

                    true

                );

        }



        $pdf =

            $this->createEnglishPdf(

                $case

            );























        /*







        |--------------------------------------------------------------------------







        | Download File







        |--------------------------------------------------------------------------







        */















        return $pdf->download(







            $this->buildFileName(







                $case







            )







        );







    }







}