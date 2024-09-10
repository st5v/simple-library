<?php

declare(strict_types=1);

namespace SimpleLibrary\InventoryLoading;

use SimpleLibrary\Inventory\Inventory;
use SimpleLibrary\Titles\Title;
use SimpleLibrary\Titles\TitleType;
use InvalidArgumentException;

/*
 * Responsible for initializing an inventory from CSV.
 */
class CsvInventoryLoader
{
    public function __construct(
        protected bool $hasHeader,
        protected int $colNumOfId,
        protected int $colNumOfType,
        protected int $colNumOfName
    ) {
    }

    public function loadTo(callable $csvParser, string $csvPath, Inventory $inventory): void
    {
        $csv = $csvParser($csvPath);
        $csvRecords = $this->hasHeader
            ? array_slice($csv, 1)
            : $csv;

        $this->validateCsv($csvRecords);

        foreach ($csvRecords as $csvRecord) {
            $title = new Title(
                (int)$csvRecord[$this->colNumOfId],
                TitleType::from($csvRecord[$this->colNumOfType]),
                $csvRecord[$this->colNumOfName],
            );
            $inventory->registerNewTitle($title);
        }
    }

    /**
     * @param array<int, array<int, string>> $csvRecords
     */
    protected function validateCsv(array $csvRecords): void
    {
        foreach ($csvRecords as $rowNum => $csvRecord) {
            if (!$this->isCsvRecordValid($csvRecord)) {
                throw new InvalidArgumentException('An invalid csv row has been found (' . $rowNum . ')!');
            }
        }

        if ($this->areDuplicateTitlesFoundWithSameId($csvRecords)) {
            throw new InvalidArgumentException('There are multiple titles with the same id!');
        }
    }

    /**
     * @param array<int, string> $csvRecord
     */
    protected function isCsvRecordValid(array $csvRecord): bool
    {
        return count($csvRecord) === 3
            && $this->isIdValid($csvRecord[$this->colNumOfId])
            && $this->isTypeValid($csvRecord[$this->colNumOfType])
            && $this->isNameValid($csvRecord[$this->colNumOfName]);
    }

    protected function isIdValid(string $titleId): bool
    {
        return ctype_digit($titleId);
    }

    protected function isTypeValid(string $type): bool
    {
        return in_array($type, TitleType::values());
    }

    protected function isNameValid(string $name): bool
    {
        return $name !== '';
    }

    /**
     * @param array<int, array<int, string>> $csvRecords
     */
    protected function areDuplicateTitlesFoundWithSameId(array $csvRecords): bool
    {
        $uniqueTitles = [];

        if ($this->hasHeader) {
            $csvRecords = array_slice($csvRecords, 1);
        }
        foreach ($csvRecords as $csvRecord) {
            $title = new Title(
                (int)$csvRecord[$this->colNumOfId],
                TitleType::from($csvRecord[$this->colNumOfType]),
                $csvRecord[$this->colNumOfName],
            );

            if (array_key_exists($title->titleId(), $uniqueTitles)) {
                if (!$uniqueTitles[$title->titleId()]->equals($title)) {
                    return true;
                }
                continue;
            }

            $uniqueTitles[$title->titleId()] = $title;
        }

        return false;
    }
}
