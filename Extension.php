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

        // Override the default twig files with the ones under templates
        $this->app['twig.loader.filesystem']->prependPath(__DIR__ . '/twig');

        $this->app->before(array($this, 'before'));

        $this->addTwigFunction("audioPlayer", "audioPlayer");
    }

    /**
     * Add the Translations directory from the extension
     */
    public function before()
    {
        if ($this->app['config']->getWhichEnd()=='backend') {
            $this->attachAppropriateAssets();
            
            $this->translationDir = __DIR__.'/locales/' . substr($this->app['locale'], 0, 2);

            if (is_dir($this->translationDir)) {
                $iterator = new \DirectoryIterator($this->translationDir);
                foreach ($iterator as $fileInfo) {
                    if ($fileInfo->isFile()) {
                        $this->app['translator']->addLoader('yml', new TranslationLoader\YamlFileLoader());
                        $this->app['translator']->addResource('yml', $fileInfo->getRealPath(), $this->app['locale']);
                    }
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

    /**
     * audioPlayer
     *
     * a twig function that takes a passed file and returns the appropriate audio player.
     *
     * @param string $file
     * @param string $fieldname (optional) define the name of the field in order to properly target the correct field
     * @return object $player
     */
    public function audioPlayer($file, $fieldname = 'audio')
    {
        $this->attachAppropriateAssets();

        if ($this->config['waveform']['enabled'] == true) {
            $this->addJavascript('assets/js/waveplayer.init.js', true);
            return $this->app['twig']->render('_waveplayer.twig', compact('file','fieldname'));
        }

        $filename = $this->app['resources']->getUrl('files') . $file;
        return $this->app['twig']->render('_audioplayer.twig', compact('filename'));
    }

    /**
     * attachAppropriateAssets
     *
     * checks the config for the extension to determine if waveform enabled or not
     * and attaches the appropriate assets to the request accordingly
     *
     * @access private
     */
    private function attachAppropriateAssets()
    {
        // Enable HTML Snippets
        $this->app['htmlsnippets'] = true;

        if ($this->config['waveform']['enabled'] == true) {
            $this->addCss('assets/css/html5-audio.css');
            $this->addJavascript('assets/js/audioHelper.js', true);
            $this->addJavascript('assets/js/wavesurfer.min.js', true);
            $this->addJavascript('assets/js/waveform.js', true);
        } else {
            $this->addJavascript('assets/js/audio.js', true);
        }
    }
}
