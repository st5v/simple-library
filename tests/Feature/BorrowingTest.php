<?php

use SimpleLibrary\States\LoanableState;
use SimpleLibrary\States\BorrowedState;
use SimpleLibrary\Titles\Title;
use SimpleLibrary\Titles\TitleType;
use Carbon\Carbon;

test('borrowing one item decreases the loanable item count by one', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');

    expect(count($inventory->items()))->toEqual(13);
    expect(count($inventory->itemsWithState(LoanableState::class)))->toEqual(13);

    $inventory->borrowTitle(new Title(7, TitleType::DVD, 'Pi'), Carbon::now());

    expect(count($inventory->items()))->toEqual(13);
    expect(count($inventory->itemsWithState(LoanableState::class)))->toEqual(12);
});

test('borrowing 3 items decreases the loanable item count by 3', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');

    expect(count($inventory->items()))->toEqual(13);
    expect(count($inventory->itemsWithState(LoanableState::class)))->toEqual(13);

    $inventory->borrowTitle(new Title(7, TitleType::DVD, 'Pi'), Carbon::now());
    $inventory->borrowTitle(new Title(7, TitleType::DVD, 'Pi'), Carbon::now());
    $inventory->borrowTitle(new Title(7, TitleType::DVD, 'Pi'), Carbon::now());

    expect(count($inventory->items()))->toEqual(13);
    expect(count($inventory->itemsWithState(LoanableState::class)))->toEqual(10);
});

test('borrowing a title that has more items doesn\'t decrease the loanable title count', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');

    expect(count($inventory->titles()))->toEqual(8);
    expect(count($inventory->titlesWithState(LoanableState::class)))->toEqual(8);

    $inventory->borrowTitle(new Title(7, TitleType::DVD, 'Pi'), Carbon::now());

    expect(count($inventory->titles()))->toEqual(8);
    expect(count($inventory->titlesWithState(LoanableState::class)))->toEqual(8);
});

test('borrowing a title that has only one item does decrease the loanable title count', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');

    expect(count($inventory->titles()))->toEqual(8);
    expect(count($inventory->titlesWithState(LoanableState::class)))->toEqual(8);

    $inventory->borrowTitle(new Title(8, TitleType::VHS, 'WarGames'), Carbon::now());

    expect(count($inventory->titles()))->toEqual(8);
    expect(count($inventory->titlesWithState(LoanableState::class)))->toEqual(7);
});

test('borrowing an item changes the state to borrowed', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');

    $borrowedItem = $inventory->borrowTitle(new Title(8, TitleType::VHS, 'WarGames'), Carbon::now());

    expect($borrowedItem->currentState())->toBeInstanceOf(BorrowedState::class);
});

test('borrowing an item doesn\'t make it overdue', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');

    expect(count($inventory->overdueItems(borrowPeriodInDays: 7)))->toEqual(0);

    $borrowedItem = $inventory->borrowTitle(
        new Title(8, TitleType::VHS, 'WarGames'),
        Carbon::now()
    );

    expect(count($inventory->overdueItems(borrowPeriodInDays: 7)))->toEqual(0);
});

test('borrowing an item a week ago makes it overdue', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');

    expect(count($inventory->overdueItems(borrowPeriodInDays: 7)))->toEqual(0);

    $borrowedItem = $inventory->borrowTitle(
        new Title(8, TitleType::VHS, 'WarGames'),
        Carbon::now()->subDays(7)
    );

    expect(count($inventory->overdueItems(borrowPeriodInDays: 7)))->toEqual(1);
});

test('borrowing an item 1 week - 1 second ago does not make it overdue', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');

    expect(count($inventory->overdueItems(borrowPeriodInDays: 7)))->toEqual(0);

    $borrowedItem = $inventory->borrowTitle(
        new Title(8, TitleType::VHS, 'WarGames'),
        Carbon::now()->subDays(7)->addSeconds(1)
    );

    expect(count($inventory->overdueItems(borrowPeriodInDays: 7)))->toEqual(0);
});
