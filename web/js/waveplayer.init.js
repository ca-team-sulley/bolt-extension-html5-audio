jQuery(document).ready(function() {
    if (jQuery("#audio").length > 0) {
        var waveform = new AudioWaveform('audio', ['mp3', 'wav', 'ogg', 'm4a'], true);

        waveform.init();
    }
});
