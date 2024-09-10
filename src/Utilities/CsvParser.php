<?php

declare(strict_types=1);

namespace SimpleLibrary\Utilities;

use InvalidArgumentException;

/*
 * Responsible for parsing a csv string to a php array.
 */
class CsvParser
{
    /**
     * @return array<int, array<int, string>>
     */
    public function parseFile(string $csvPath): array
    {
        if (!file_exists($csvPath) || !is_readable($csvPath)) {
            throw new InvalidArgumentException('CSV file is not found or is not readable!');
        }

        $csv = [];

        $csvFile = fopen($csvPath, 'r');
        if ($csvFile === false) {
            throw new InvalidArgumentException('Could not open the provided CSV file!');
        }

        while (($csvLine = fgetcsv($csvFile)) !== false) {
            $csv [] = $csvLine;
        }

        fclose($csvFile);

        return $csv;
    }
}
