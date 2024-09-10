<?php

use SimpleLibrary\States\LoanableState;
use SimpleLibrary\States\BorrowedState;
use SimpleLibrary\States\ReturnedState;
use SimpleLibrary\Titles\Title;
use SimpleLibrary\Titles\TitleType;
use Carbon\Carbon;

test('returned items have one more borrow history then before', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');
   
    $borrowedItem = $inventory->borrowTitle(new Title(8, TitleType::VHS, 'WarGames'), Carbon::now());
    expect($borrowedItem->borrowHistory())->toBeEmpty();

    $inventory->returnItem($borrowedItem->itemId(), Carbon::now());
    expect($borrowedItem->borrowHistory())->toHaveLength(1);
});

test('returned items change their state from borrowed to loanable', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');
    
    $chosenItem = $inventory->findItemById(9);
    expect($chosenItem->currentState())->toBeInstanceOf(LoanableState::class);

    $borrowedItem = $inventory->borrowTitle(new Title(8, TitleType::VHS, 'WarGames'), Carbon::now());
    expect($borrowedItem->currentState())->toBeInstanceOf(BorrowedState::class);

    $returnedItem = $inventory->returnItem($borrowedItem->itemId(), Carbon::now());
    expect($returnedItem->currentState())->toBeInstanceOf(LoanableState::class);
});

test('returning an overdue item decreases the count of overdue items by one', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');
   
    $borrowedItem = $inventory->borrowTitle(
        new Title(8, TitleType::VHS, 'WarGames'),
        Carbon::now()->subDays(10)
    );
    $overdueItemsCount = count($inventory->overdueItems(7));

    $returnedItem = $inventory->returnItem($borrowedItem->itemId(), Carbon::now());
    expect(count($inventory->overdueItems(7)))->toEqual($overdueItemsCount - 1);
});

test('returning an item increases the loanable item count by one', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');
    
    $borrowedItem = $inventory->borrowTitle(new Title(8, TitleType::VHS, 'WarGames'), Carbon::now());
    $loanableItemCount = count($inventory->itemsWithState(LoanableState::class));

    $returnedItem = $inventory->returnItem($borrowedItem->itemId(), Carbon::now());
    expect(count($inventory->itemsWithState(LoanableState::class)))->toEqual($loanableItemCount + 1);
});

test('returning an item increases the loanable title count by one if the title was not loanable before', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');
    
    $borrowedItem = $inventory->borrowTitle(new Title(8, TitleType::VHS, 'WarGames'), Carbon::now());
    $loanableTitleCount = count($inventory->titlesWithState(LoanableState::class));

    $returnedItem = $inventory->returnItem($borrowedItem->itemId(), Carbon::now());
    expect(count($inventory->titlesWithState(LoanableState::class)))->toEqual($loanableTitleCount + 1);
});

test('returning an item does not increase the loanable title count by one if the title was still loanable before', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');
    
    $borrowedItem = $inventory->borrowTitle(new Title(6, TitleType::VHS, 'Hackers'), Carbon::now());
    $loanableTitleCount = count($inventory->titlesWithState(LoanableState::class));

    $returnedItem = $inventory->returnItem($borrowedItem->itemId(), Carbon::now());
    expect(count($inventory->titlesWithState(LoanableState::class)))->toEqual($loanableTitleCount);
});
