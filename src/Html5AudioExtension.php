<?php
namespace Bolt\Extension\Cainc\Html5Audio;

use Bolt\Extension\SimpleExtension;
use Bolt\Asset\File\JavaScript;
use Bolt\Asset\File\Stylesheet;
use Bolt\Controller\Zone;
use Symfony\Component\HttpFoundation\ParameterBag;
use Silex\Application;

class Html5AudioExtension extends SimpleExtension
{
    protected function registerFields()
    {
        return [
            new Field\AudioField()
        ];
    }

    protected function registerTwigPaths()
    {
        return [
            'templates'
        ];
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
        // get the config file name if using one. otherwise its 'default'
    		// check for config settings
        $filename = $this->app['resources']->getUrl('files') . $file;
        if ($this->config['waveform']['enabled'] == true) {
            return $this->app['twig']->render('_waveplayer.twig', compact('file', 'fieldname'));
        }
        return $this->app['twig']->render('_audioplayer.twig', compact('filename'));
    }

    /**
  	 * {@inheritdoc}
  	 */
  	protected function registerTwigFunctions()
  	{
  		return [
  			'audioPlayer' => [ 'audioPlayer' ],
  		];
  	}
    
    protected function registerAssets()
    {
        $styleBack = (new Stylesheet('css/html5-audio.css'))
            ->setZone(Zone::BACKEND);
        if ($this->config['waveform']['enabled'] == true) {
          $style = (new Stylesheet('css/html5-audio_front.css'));
          $helperjs = (new JavaScript('js/audioHelper_front.js'));
          $helperBackjs = (new JavaScript('js/audioHelper.js'))->setZone(Zone::BACKEND);
          $wavesurferjs = (new JavaScript('js/wavesurfer_front.min.js'));
          $wavesurferBackjs = (new JavaScript('js/wavesurfer.min.js'))->setZone(Zone::BACKEND);
          $waveformjs = (new JavaScript('js/waveform_front.js'));
          $waveformBackjs = (new JavaScript('js/waveform.js'))->setZone(Zone::BACKEND);
          $waveplayerjs = (new JavaScript('js/waveplayer.init.js'))
            ->setLate(true)
            ->setPriority(5)
            ->setZone(Zone::FRONTEND);
          return [
              $style, $styleBack, 
              $helperjs, $helperBackjs,
              $wavesurferjs, $wavesurferBackjs,
              $waveformjs, $waveformBackjs,
              $waveplayerjs
          ];
        } else {
          $audiojs = (new JavaScript('js/audio.js'))->setZone(Zone::BACKEND);
          return [
              $styleBack,
              $audiojs
          ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function registerServices(Application $app)
    {
        $this->config = $this->getConfig();
        $this->app = $app;
        $app['html5audio.config'] = $app->share(
            function () {
                return new ParameterBag($this->getConfig());
            }
        );
    }

    /**
  	 * @return array
  	 */
  	protected function getDefaultConfig()
  	{
  		return [
				'allowed-filetypes' => ['mp3', 'ogg', 'wav', 'm4a'],
				'waveform'          => [ 'enabled' => true ]
  		];
  	}
}
