<?php declare(strict_types=1);

namespace BulkEdit\Form\Element;

use Laminas\Form\Element\Select;

class OptionalSelect extends Select
{
    use TraitOptionalElement;
}
