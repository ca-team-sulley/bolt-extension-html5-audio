<?php

namespace Bolt\Extension\Cainc\Html5Audio\Field;

use Bolt\Storage\EntityManager;
use Bolt\Storage\Field\Type\FieldTypeBase;
use Bolt\Storage\QuerySet;

class AudioField extends FieldTypeBase
{
    public function getName()
    {
        return 'audio';
    }

    public function getTemplate()
    {
        return '_audio-widget.twig';
    }

    public function getStorageType()
    {
        return 'text';
    }

    public function getStorageOptions()
    {
        return [
            'default' => null,
            'notnull' => false,
        ];
    }
}
