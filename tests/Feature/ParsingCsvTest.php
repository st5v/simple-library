<?php

test('should throw exception for not existing csv file', function () {
    expect(
        fn() => initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/not_existing.csv')
    )->toThrow(InvalidArgumentException::class);
});

test('should throw exception with the original initial inventory csv', function () {
    expect(
        fn() => initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/original_initial_inventory.csv')
    )->toThrow(InvalidArgumentException::class);
});

test('should not throw exception with the fixed initial inventory csv', function () {
    expect(
        fn() => initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/fixed_initial_inventory.csv')
    )->not->toThrow(InvalidArgumentException::class);
});

test('should throw exception if wrong id was found', function () {
    expect(
        fn() => initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/wrong_id_inventory.csv')
    )->toThrow(InvalidArgumentException::class);
});

test('should throw exception if wrong type was found', function () {
    expect(
        fn() => initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/wrong_type_inventory.csv')
    )->toThrow(InvalidArgumentException::class);
});

test('should throw exception if wrong name was found', function () {
    expect(
        fn() => initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/wrong_name_inventory.csv')
    )->toThrow(InvalidArgumentException::class);
});

test('should not throw exception for empty inventory csv', function () {
    expect(
        fn() => initInMemoryInventoryFromCsv(__DIR__ . '/../csv_files/empty_inventory.csv')
    )->not->toThrow(InvalidArgumentException::class);
});