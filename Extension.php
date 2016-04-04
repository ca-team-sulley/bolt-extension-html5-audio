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

            // Override the default twig files with the ones under templates
            $this->app['twig.loader.filesystem']->prependPath(__DIR__ . '/twig');

            $this->attachAppropriateAssets();
        }

        $this->addTwigFunction("audioPlayer", "audioPlayer");
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

    /**
     * audioPlayer
     *
     * a twig function that takes a passed file and returns an audio player.
     *
     * @param string $file
     * @param string $fieldname (optional) define the name of the field in order to properly target the correct field
     * @return object $player
     */
    public function audioPlayer($file, $fieldname = 'audio')
    {
        $this->attachAppropriateAssets();

        if ($this->config['waveform']['enabled'] == true){
            $player = $this->waveformScript($file, $fieldname);
        }else{
            $player = "<audio controls preload='none' src='" . $this->app['paths']['rooturl'] . "files/" . $file . "'></audio>";
        }

        return $player;
    }

    /**
     * waveformScript
     *
     * the waveform player to be passed to the frontend.
     *
     * @param string $file
     * @return object $html
     */
    public function waveformScript($file, $fieldname)
    {
        $html = <<< EOM
        <fieldset class="audio" id="$fieldname">
        <div class="waveform_view">
        <div class="wave"></div>
        <div class="wave_zoom">
        <i class="fa fa-search-minus zoom-out"></i>
        <input class="zoom-slider" type="range" min="1" max="100" value="0">
        <i class="fa fa-search-plus zoom-in"></i>
        </div>
        <div class="wave_controls pull-left">
        <span class="player-control">
        <button class="button playpause-button"><i class="fa fa-play"></i> Play / <i class="fa fa-pause"></i> Pause</button>
        <button class="button stop-button"><i class="fa fa-stop"></i> Stop</button> 
        </span>
        </div>
        <div class="pull-right track-time">
        <span class="current">-</span> / <span class="total">-</span>
        </div>
        </div>
        <source src="$file">        
EOM;
        $this->addJavascript('assets/js/waveplayer.init.js', true);
        return $html;
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
            $this->addJavascript('assets/audioHelper.js', true);
            $this->addJavascript('assets/js/wavesurfer.min.js', true);
            $this->addJavascript('assets/waveform.js', true);
        } else {
            $this->addJavascript('assets/audio.js', true);
        }
    }
}
