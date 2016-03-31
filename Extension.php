<?php

namespace Bolt\Extension\Cainc\Html5Audio;

use Bolt\BaseExtension;
use Bolt\Application;
use Bolt\Translation\Translator as Trans, Symfony\Component\Translation\Loader as TranslationLoader;

class Extension extends BaseExtension
{
    /**
     * Initializes the extension
     */
    public function initialize()
    {
        $this->app['config']->fields->addField(new AudioField());

        if ($this->app['config']->getWhichEnd()=='backend') {
            $this->app->before(array($this, 'before'));
            // Enable HTML Snippets
            if (version_compare($this->app['bolt_version'], '2.0.0') >= 0) {
                $this->app['htmlsnippets'] = true;
            }
            // Override the default twig files with the ones under templates
            $this->app['twig.loader.filesystem']->prependPath(__DIR__ . '/twig');

            if ($this->config['waveform']['enabled'] == true){
                $this->addCss('assets/css/html5-audio.css');
                $this->addJavascript('assets/audioHelper.js', true);
                $this->addJavascript('assets/js/wavesurfer.min.js', true);
                $this->addJavascript('assets/waveform.js', true);
            }else{
                $this->addJavascript('assets/audio.js', true);
            }
        }
    }

    /**
     * Add the Translations directory from the extension
     */
    public function before()
    {
        $this->translationDir = __DIR__.'/locales/' . substr($this->app['locale'], 0, 2);
        if (is_dir($this->translationDir))
        {
            $iterator = new \DirectoryIterator($this->translationDir);
            foreach ($iterator as $fileInfo)
            {
                if ($fileInfo->isFile())
                {
                    $this->app['translator']->addLoader('yml', new TranslationLoader\YamlFileLoader());
                    $this->app['translator']->addResource('yml', $fileInfo->getRealPath(), $this->app['locale']);
                }
            }
        }
    }

    /**
     * Get the extension name
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "Html5Audio";
    }
}
