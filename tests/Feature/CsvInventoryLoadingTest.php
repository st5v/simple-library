<?php

use SimpleLibrary\Titles\Title;
use SimpleLibrary\Titles\TitleType;
use SimpleLibrary\InventoryLoading\CsvInventoryLoader;

test('inventory is loaded correctly from CSV', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');
    
    expect(count($inventory->items()))->toEqual(13);
});

test('inventory contains the titles in CSV', function () {
    $inventory = initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv');
    
    expect($inventory->hasTitle(
        new Title(8, TitleType::VHS, 'WarGames')
    ))->toBeTrue();

    expect($inventory->hasTitle(
        new Title(123, TitleType::VHS, 'Not existing title')
    ))->toBeFalse();
});
