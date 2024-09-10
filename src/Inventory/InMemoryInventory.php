<?php

declare(strict_types=1);

namespace SimpleLibrary\Inventory;

use SimpleLibrary\Titles\Title;
use SimpleLibrary\States\LoanableState;
use Carbon\Carbon;
use RuntimeException;

/*
 * An inventory implementation that stores all the data in memory.
 */
class InMemoryInventory implements Inventory
{
    /**
     * @var array<int, Item>
     */
    protected array $items = [];

    public function registerNewTitle(Title $title): void
    {
        $this->items [] = new Item(
            itemId: count($this->items),
            title: $title,
            currentState: new LoanableState(),
            borrowHistory: []
        );
    }

    /**
     * @return array<int, Item>
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @return array<int, Item>
     */
    public function itemsWithState(string $stateClass): array
    {
        return array_values(
            array_filter(
                $this->items,
                fn ($item) => $item->hasState($stateClass)
            )
        );
    }

    /**
     * @return array<int, Item>
     */
    public function overdueItems(int $borrowPeriodInDays): array
    {
        return array_values(array_filter(
            $this->items,
            fn ($item) => $item->isOverdue($borrowPeriodInDays)
        ));
    }

    public function hasTitle(Title $title): bool
    {
        foreach ($this->items as $item) {
            if ($item->title()->equals($title)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array<int, Title>
     */
    public function titles(): array
    {
        $uniqueTitles = [];

        foreach ($this->items as $item) {
            $title = $item->title();
            $uniqueTitles[$title->titleId()] = $title;
        }

        return array_values($uniqueTitles);
    }

    /**
     * @return array<int, Title>
     */
    public function titlesWithState(string $stateClass): array
    {
        $titlesWithState = [];

        foreach ($this->items as $item) {
            if (!$item->hasState($stateClass)) {
                continue;
            }

            $title = $item->title();
            $titlesWithState[$title->titleId()] = $title;
        }

        return array_values($titlesWithState);
    }

    public function findItemById(int $itemId): Item
    {
        foreach ($this->items as $item) {
            if ($item->itemId() === $itemId) {
                return $item;
            }
        }

        throw new RuntimeException('No item found with the provided id!');
    }

    public function borrowTitle(Title $title, Carbon $borrowTime): Item
    {
        foreach ($this->items as $item) {
            if (
                !$item->hasState(LoanableState::class)
                || !$item->isTitle($title)
            ) {
                continue;
            }

            $item->borrow($borrowTime);
            return $item;
        }

        throw new RuntimeException('There is no loanable item for the provided title!');
    }

    public function returnItem(int $itemId, Carbon $returnTime): Item
    {
        $itemToBeReturned = $this->findItemById($itemId);
        $itemToBeReturned->return($returnTime);
        return $itemToBeReturned;
    }
}
