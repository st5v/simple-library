<?php

declare(strict_types=1);

namespace SimpleLibrary\Titles;

/*
 * Representation of a product (as an abstract idea), descriptions of the items
 * that the library could own.
 * These are not physical objects.
 */
class Title
{
    protected int $titleId;
    protected TitleType $type;
    protected string $name;

    public function __construct(int $titleId, TitleType $type, string $name)
    {
        $this->titleId = $titleId;
        $this->type = $type;
        $this->name = $name;
    }

    public function titleId(): int
    {
        return $this->titleId;
    }

    public function equals(Title $title): bool
    {
        return $this->titleId === $title->titleId
            && $this->type === $title->type
            && $this->name === $title->name;
    }
}
