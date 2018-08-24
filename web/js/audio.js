/**
 * Audio - (constructor) sets basic element variables for use throughout the class.
 * @param {HTMLElement} element - dialog container
 * @constructor
 */
function Audio(element, types) {
    this.container = $('#editcontent').find('fieldset#'+element);
    this.file = this.container.find('input.audioinput');
    this.fileName = this.file.val();
    this.audio = this.container.find('audio');
    this.source = this.container.find('source');
    this.types = types;
    this.filepath = document.location.origin + '/files/';
}

/**
 * Class Prototype Create
 * @type {Object}
 */
Audio.prototype = Object.create({});

/**
 * Set the constructor method for this object
 * @type {Audio}
 */
Audio.prototype.constructor = Audio;

/**
 * Listens for file changes, as JS made updates to the hidden field do not
 * trigger any events
 */
Audio.prototype.listenForFileChange = function () {
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
 * init - Audio init method
 */
Audio.prototype.init = function () {
    this.listenForFileChange();

    var self = this;

    /**
     * On Change - An event handler for when the audio filename field changes.
     */
    self.file.on('change', function () {
        var type = this.file.val().split('.').pop();
        var valid_type = $.inArray(type, this.types);

        if (valid_type > -1) {
            this.source.attr('src', this.filepath + this.file.val());
            this.source.attr('type', 'audio/' + type);
            this.audio.trigger('load');
        }
    }.bind(this));
};
