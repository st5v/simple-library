<?php

declare(strict_types=1);

namespace SimpleLibrary\Titles;

enum TitleType: string
{
    case DVD = "DVD";
    case CD = "CD";
    case VHS = "VHS";
    case Book = "Book";

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
