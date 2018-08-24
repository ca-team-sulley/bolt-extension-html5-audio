/**
 * AudioWaveform - (constructor) sets basic element variables for use throughout the class.
 * @param {HTMLElement} element - dialog container
 * @constructor
 */
function AudioWaveform(element, types, form) {
    var file = 'div';

    if (!form) {
        form = 'fieldset';
        file = 'input';
    } else {
        form = 'div';
    }

    this.container = $(document).find(form+'#'+element);
    this.timer = 0;
    this.wavesurfer = null;
    this.file = this.container.find(file+'.audioinput');

    if (file == 'div') {
        this.fileName = this.file.html();
    } else {
        this.fileName = this.file.val();
    }

    this.source = this.container.find('source').attr('src');
    this.types = types;
    this.filepath = document.location.origin + '/files/';
}

/**
 * Class Prototype Create
 * @type {Object}
 */
AudioWaveform.prototype = Object.create({});

/**
 * Set the constructor method for this object
 * @type {AudioWaveform}
 */
AudioWaveform.prototype.constructor = AudioWaveform;

/**
 * generateWaveform - a class instance method that generates the waveform
 */
AudioWaveform.prototype.generateWaveform = function () {
    var surferElement = this.container.find('.wave');

    this.wavesurfer = AudioWaveform.createWaveSurferObject(surferElement);
    this.loadFile();
};

/**
 * setDomElements - a secondary constructor that further defines element variables to be used
 * throughout the class which were unavailable until the generator runs
 * @constructor
 */
AudioWaveform.prototype.setDomElements = function () {
    this.slider = this.container.find('.zoom-slider');
    this.zoomOut = this.container.find('.zoom-out');
    this.zoomIn = this.container.find('.zoom-in');
    this.playPause = this.container.find('.playpause-button');
    this.stop = this.container.find('.stop-button');
    this.current = this.container.find('.current');
    this.total = this.container.find('.total');
};

/**
 * cleanup - a class instance method to be called when the edit audio modal is closed to clean
 * up the superfluous elements
 */
AudioWaveform.prototype.cleanup = function () {
    this.container.find('.wave').remove();
    this.container.find('.waveform_view').remove();
    this.container.find('.alert').remove();

    this.slider.off();
    this.zoomOut.off();
    this.zoomIn.off();
    this.playPause.off();
    this.stop.off();

    clearInterval(self.timer);
};

/**
 * setZoom - when passed an offset, this method is used to zoom in and out of a waveform and its associated range slider.
 * @param {int} offset - the amount the zoom should increment by
 */
AudioWaveform.prototype.setZoom = function (offset) {
    var zoom = Number(this.slider.val()) + offset;

    this.slider.val(zoom);
    this.wavesurfer.zoom(Number(this.slider.val()));
};

/**
 * displays a given error message.
 * @param {!Object} msgConfig - contains:
 *                 {!String} error - The error message string.
 *                 {?String} reason - The reason for the error (optional).
 */
AudioWaveform.prototype.showError = function (msgConfig) {
    this.audio.after("<div class='alert alert-dismissible alert-danger'>" +
        "<button type='button' class='close' data-dismiss='alert'>x</button>" +
        "<h4>" + msgConfig.error + "</h4>" +
        "Reason: " + msgConfig.reason + "</div>");
};

/**
 * loadFile - loads an audio file into the waveform if it is of an accepted type
 *
 */
AudioWaveform.prototype.loadFile = function () {
    if (this.types != undefined) {
        var type = this.fileName.split('.').pop();
        var valid_type = $.inArray(type, this.types);

        if (valid_type > -1) {
            this.wavesurfer.load(this.filepath + this.fileName);
        }
    } else {
        this.wavesurfer.load(this.filepath + this.source);
    }
};

/**
 * Listens for file changes, as JS made updates to the hidden field do not
 * trigger any events
 */
AudioWaveform.prototype.listenForFileChange = function () {
    var self = this;

    setTimeout(function () {
        if (self.file.val() !== self.fileName) {
            self.fileName = self.file.val();
            self.file.trigger('change');
        }

        self.listenForFileChange();
    }, 500);
};

/**
 * init - AudioWaveform init method
 */
AudioWaveform.prototype.init = function () {
    // generate the waveform and clean up the audio edit modal, as well as set up the basic element variables
    this.generateWaveform();
    this.setDomElements();
    this.listenForFileChange();

    var self = this;

    /**
     * On Change - An event handler for when the audio filename field changes.
     */
    self.file.on('change', function () {
        this.loadFile();
    }.bind(this));

    /**
     * On Ready - An event handler for when a track is loaded and ready to play.
     */
    this.wavesurfer.on('ready', function () {
        var duration = self.wavesurfer.getDuration();
        //the zoom factor controls how much the waveform zoom increments, rather than setting as a magic number elsewhere
        var zoomFactor = 5;

        // Show duration of track.
        self.current.text('0:00');
        self.total.text(formatTimeMilliseconds(duration));

        /*-----------------------------------
             Waveform Controls
         ----------------------------------*/

        /**
         * Connect the Zoom slider to the waveform editor
         */
        self.slider.on('input', function(){
            self.wavesurfer.zoom(Number(this.value)); //this.value refers to self.slider, which is in scope as this
        });

        /**
         * Slider Zoom Out Button Functionality
         * @param {MouseEvent} e
         */
        self.zoomOut.on('click', function (e) {
            e.preventDefault();

            self.setZoom(-zoomFactor);
        });

        /**
         * Slider Zoom In Button Functionality
         * @param {MouseEvent} e
         */
        self.zoomIn.on('click', function (e) {
            e.preventDefault();

            self.setZoom(zoomFactor);
        });

        /**
         * If the waveform is paused...
         * @param {MouseEvent} e
         */
        self.playPause.on('click', function (e) {
            e.preventDefault();
            self.wavesurfer.playPause();

            // Show the progress of the track in time, but only if the audio is playing.
            if (self.wavesurfer.isPlaying()) {
                self.timer = setInterval(function () {
                    // We're getting called continuously, so make sure we're still playing; the audio could have
                    // reached the end, the user hit Play/Pause again or Stop, etc.
                    if(self.wavesurfer.isPlaying()) {
                        self.current.text(formatTimeMilliseconds(self.wavesurfer.getCurrentTime()));
                    } else {
                        clearInterval(self.timer);
                    }
                }, 0);
            } else {
                clearInterval(self.timer);
            }
        });

        /**
         * If the waveform is stopped...
         * @param {MouseEvent} e
         */
        self.stop.on('click', function (e) {
            e.preventDefault();

            if (self.wavesurfer.isPlaying()) {
                self.wavesurfer.stop();
                clearInterval(self.timer);
            }
        });
    });

    /**
     * An event handler to update the current time in the waveform if the cursor is used to select a place in its audio
     */
    this.wavesurfer.on('seek', function () {
        self.current.text(formatTimeMilliseconds(self.wavesurfer.getCurrentTime()));
    });

    /**
     * On Error - An event handler for when a track cannot load into the waveform.
     */
    this.wavesurfer.on('error', function () {
        this.container.find('.wave').remove();
        this.container.find('.waveform_view').remove();

        var reason;

        if (this.source && this.source.length > 0) {
            reason = "invalid file reference '" + this.source + "'";
        } else {
            reason = "no file reference found";
        }

        this.showError({
            'error': "Failed to load audio file",
            'reason': reason
        });
    }.bind(this));
};

/**
 * Create the Wavesurfer object on the passed in selector.
 * @param {HTMLElement} selector - waveform container
 * @return {WaveSurfer}
 */
AudioWaveform.createWaveSurferObject = function (selector) {
    return WaveSurfer.create({
        container: selector[0],
        cursorColor: '#000000',
        cursorWidth: 1,
        height: 100,
        waveColor: '#588EFB',
        progressColor: '#F043A4'
    });
};
