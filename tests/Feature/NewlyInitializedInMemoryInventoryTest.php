<?php

use SimpleLibrary\States\LoanableState;

test('in memory inventory handles titles properly', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');
    
    expect(count($inventory->titles()))->toEqual(8);
});

test('new items in inventory initialize idle current state', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');
    
    expect($inventory->items())->each(
        fn ($item) => $item->currentState()->toBeInstanceOf(LoanableState::class)
    );
});

test('after inventory initalization all items are loanable', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');

    expect($inventory->items())->toEqual($inventory->itemsWithState(LoanableState::class));
});

test('new items in inventory initialize empty borrow history', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');
    
    expect($inventory->items())->each(
        fn ($item) => $item->borrowHistory()->toBeEmpty()
    );
});

test('all titles are loanable after inventory initalization', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');
    
    expect($inventory->titles())->toEqual($inventory->titlesWithState(LoanableState::class));
});
