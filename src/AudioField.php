<?php
namespace Bolt\Extension\Cainc\Html5Audio;

use Bolt\Field\FieldInterface;

class AudioField implements FieldInterface
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
        return array('default' => '');
    }
}
