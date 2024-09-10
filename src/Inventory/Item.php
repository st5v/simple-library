<?php

declare(strict_types=1);

namespace SimpleLibrary\Inventory;

use SimpleLibrary\Titles\Title;
use SimpleLibrary\States\State;
use SimpleLibrary\States\BorrowedState;
use SimpleLibrary\States\ReturnedState;
use SimpleLibrary\States\LoanableState;
use Carbon\Carbon;
use RuntimeException;

/**
 * An physical instance of a title that was registered in the library.
 */
class Item
{
    /**
     * Identifies an item.
     *
     * There are situations when an exact instance must be
     * identified. For example, a member calls that they accidentally returned
     * their graduation DVD instead of the borrowed movie. If we have an id for
     * the item, it's easy to track back which DVD box they borrowed.
     * Without it, we'd need to check all the copies of the same movie.
     */
    protected int $itemId;
    protected Title $title;
    protected State $currentState;
    /**
     * @var array<int, ReturnedState>
     */
    protected array $borrowHistory;

    /**
     * @param array<int, ReturnedState> $borrowHistory
     */
    public function __construct(
        int $itemId,
        Title $title,
        State $currentState,
        array $borrowHistory,
    ) {
        $this->itemId = $itemId;
        $this->title = $title;
        $this->currentState = $currentState;
        $this->borrowHistory = $borrowHistory;
    }

    public function itemId(): int
    {
        return $this->itemId;
    }

    public function title(): Title
    {
        return $this->title;
    }

    public function isTitle(Title $title): bool
    {
        return $this->title->equals($title);
    }

    public function hasState(string $stateClass): bool
    {
        return is_a($this->currentState, $stateClass);
    }

    public function currentState(): State
    {
        return $this->currentState;
    }

    /**
     * @return array<int, ReturnedState>
     */
    public function borrowHistory(): array
    {
        return $this->borrowHistory;
    }

    public function borrow(Carbon $borrowTime): void
    {
        $this->currentState = new BorrowedState($borrowTime);
    }

    public function return(Carbon $returnTime): void
    {
        if (!$this->hasState(BorrowedState::class)) {
            throw new RuntimeException('Can only return an item that is being borrowed!');
        }

        $this->borrowHistory [] = new ReturnedState(
            // @phpstan-ignore-next-line method.notFound (At this point it can only be BorrowedState)
            $this->currentState->borrowTime(),
            $returnTime
        );
        $this->currentState = new LoanableState();
    }

    public function isOverdue(int $borrowPeriodInDays): bool
    {
        return $this->hasState(BorrowedState::class)
            && Carbon::now()
                ->subDays($borrowPeriodInDays)
                // @phpstan-ignore method.notFound (At this point it can only be BorrowedState)
                ->gt($this->currentState->borrowTime());
    }
}
