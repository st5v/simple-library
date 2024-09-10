# Simple Library

Design solution for the task detailed in `docs/task.txt`.

## Design Notes

- There is **no UI**, only the core of the solution.
  - This means that there is no config, there is no main, there is no central application/manager
  - Code can be run and tested through automated tests
  - In terms of MVC pattern, this code contains an Model layer, and does not contain code for View or Controller layers
- **Important terms**:
  - Title: abstract representation of a product
  - Item: physical objects of titles that the library owns (synonym: copy)
  - Inventory: collection of all the items that the library owns
  - Loanable items: items in the inventory that are ready to be borrowed (items with loanable state)
  - Borrowed items: items in the inventory that are owned by the library but borrowed by someone (items with borrowed state)
- **Assumptions**:
  - The solution assumes that it's used only in the time zone where it was run
  - The provided CSV file contained invalid records. One option would be to ignore those lines, another to try to fix those lines. These solutions hide the problem that the provided CSV file wasn't created or exported with the expected format. This is why, I validate the CSV and throw an error if it contains invalid data.
  - The solution assumes, that two titles are only the same if all their attributes are the same (id, type, name). As a result, a movie differs from the very same movie if the type is different. It's logical, because for example a DVD might contain a menu with extras, while VHS videos don't.
- **Further design notes**:
  - In the Inventory classes, borrowTitle and returnItem might look inconsistent, but it was intentional. When a customer goes to the library, they want a title and not an item. They just want a DVD of a movie, they don't care which copy they receive. The exact copy to be borrowed will be determined by some artitrary internal logic and should not matter to the customer. On the other hand, when they return an item, they do return a specific item they borrowed.
  - Both items and titles have ids. The title id identifies the abstract product while the item id identifies the exact copy. Further info why item id is necessary can be seen in the item class' file.
  - This solution calls the title's title name. Title would be a better word, also that's how the CSV files call that attribute, but to avoid confusion, a title in this solution refers to the abstract unit of entertainment (such as book or movie) while its title is referred to as its name.
  - Since PHP doesn't support generic array types, I used phpdoc with static analysis to indicate the intention

## How to run

### Prerequisites

1. **PHP** 8.3.x CLI: <https://www.php.net/manual/en/install.php>
2. **Composer** 2.x.x: <https://getcomposer.org/download/>

### Initialize

1. `git clone git@github.com:st5v/simple-library.git`
2. `cd simple-library`
3. `composer install`

### Run tests

Run the following command inside the project folder: `composer run-script pest`

### Ensuring code quality

Run the following commands inside the project folder to ensure code quality:

- `composer run-script psptan`
- `composer run-script phpmd`
- `composer run-script phpcs`
