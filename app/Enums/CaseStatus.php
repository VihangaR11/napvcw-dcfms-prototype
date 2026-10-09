<?php

namespace App\Enums;

enum CaseStatus: string
{
    case REGISTERED =
        'Registered';

    case ROUTED =
        'Routed';

    case UNDER_PROCESSING =
        'Under Processing';

    case AWAITING_DECISION =
        'Awaiting Decision';

    case CLOSED =
        'Closed';


    public function label(): string
    {
        return $this->value;
    }


    public static function values(): array
    {
        return array_map(
            fn (self $status) =>
                $status->value,
            self::cases()
        );
    }
}