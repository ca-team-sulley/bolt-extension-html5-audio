HTML5 Audio
===========

This extension adds an audio field to Bolt CMS which produces either a Waveform player, or falls back to an HTML5 Audio Element.
Please edit config options to suit your needs before using this extension.

## Configuration (app/config/extensions/html5-audio.cainc.yml)
The allowed-filetypes configuration key lets you add additional file types to the allowed-filetypes array.
The enabled key allows you to enable the javascript and canvas based waveform player. By setting false, it will use the html5 audio element.

### Accepted Types are the audio file types that you wish to allow. Keep in mind that this will not override any browser compatabilities
allowed-filetypes:
  - mp3
  - wav
  - ogg
  - m4a

#### Setting the Enabled option to false for Waveform will cause the extension to use standard HTML5 audio element instead.
waveform:
  enabled: true

## Examples

### Contenttypes.yml (Add Field to Pages labeled as Audio...)

```YML
pages:
    name: Pages
    singular_name: Page
    fields:
        title:
            type: text
            class: large
            group: content
        slug:
            type: slug
            uses: title
        image:
            type: image
        audio:
            type: audio
        teaser:
            type: html
            height: 150px
        body:
            type: html
            height: 300px
        template:
            type: templateselect
            filter: '*.twig'
    taxonomy: [ chapters ]
    recordsperpage: 100
```

### Twig Function (Theme Template)

In this example, the name of the field on the backend is audio. The second parameter passed in the twig function is the fieldname.
By passing this, it ensures that the proper field will be referenced from the player:

```Twig
    {{ audioPlayer(record.audio, 'audio')|raw }}
```

## Translations
    Chinese
    Czech
    German - Translation Verified
    English - Translation Verified
    Spanish - Translation Verified
    French
    Hindi - Translation Verified
    Hungarian
    Italian
    Japanese
    Dutch - Translation Verified
    Polish
    Portuguese
    Russian
    Swedish - Translation Verified

## Support
If you run into issues or need a new feature, please open a ticket over at [https://github.com/ca-team-sulley/bolt-extension-html5-audio/issues](https://github.com/ca-team-sulley/bolt-extension-html5-audio/issues)
... or fix it yourself, pull-requests are welcome =)