<?php

declare(strict_types=1);

namespace SimpleLibrary\Inventory;

use SimpleLibrary\Titles\Title;
use Carbon\Carbon;

/*
 * Describes how an inventory must be implemented.
 * Once we put dynamism to this library, it's likely that we change CSVs to
 * storing data in database.
 * Using this interface, the extension with MysqlInventory is easy.
 */
interface Inventory
{
    public function registerNewTitle(Title $title): void;

    /**
     * @return array<int, Item>
     */
    public function items(): array;

    /**
     * @return array<int, Item>
     */
    public function itemsWithState(string $stateClass): array;

    /**
     * @return array<int, Item>
     */
    public function overdueItems(int $borrowPeriodInDays): array;

    public function hasTitle(Title $title): bool;

    /**
     * @return array<int, Title>
     */
    public function titles(): array;

    /**
     * @return array<int, Title>
     */
    public function titlesWithState(string $stateClass): array;

    public function findItemById(int $itemId): Item;

    public function borrowTitle(Title $title, Carbon $borrowTime): Item;

    public function returnItem(int $itemId, Carbon $returnTime): Item;
}
