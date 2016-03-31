<?php

namespace Bolt\Extension\Cainc\Html5Audio;

if (isset($app)) {
    $app['extensions']->register(new Extension($app));
}
