/**
 * Audio - (constructor) sets basic element variables for use throughout the class.
 * @param {HTMLElement} element - dialog container
 * @constructor
 */
function Audio(element, types) {
    this.container = $('#editcontent').find('fieldset#'+element);
    this.file = this.container.find('input.audioinput');
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
 * init - Audio init method
 */
Audio.prototype.init = function () {

    var self = this;

    /**
     * On Change - An event handler for when the audio filename field changes.
     */
    self.file.on('change', function(){
        var type = this.file.val().split('.').pop();
        var valid_type = $.inArray(type, this.types);

        if(valid_type > -1){
            this.source.attr('src', this.filepath + this.file.val());
            this.source.attr('type', 'audio/' + type);
            this.audio.trigger('load');
        }

    }.bind(this));

    /**
     * Val Change Trigger - An event handler to trigger a change event for fields when the value is changed programmatically.
     */
    (function($){
        var originalVal = $.fn.val;
        $.fn.val = function(){
            var result =originalVal.apply(this,arguments);
            if(arguments.length>0)
                $(this).change();
            return result;
        };
    })(jQuery);
};