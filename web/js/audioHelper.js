/*-----------------------------------
 Audio Widget Helper Functions
 ----------------------------------*/
/**
 * Call this method to convert seconds into mm:ss:xxx (x is milliseconds) format
 * @param {number} time - time in seconds.
 * @returns {String}
 */
function formatTimeMilliseconds(time) {
    var minutes = Math.floor(time % 36000 / 60);
    var seconds = Math.floor(time % 36000 % 60);
    var milliseconds = (time % 1).toFixed(3).toString().replace('0.', '');

    if (minutes < 10) {
        minutes = '0' + minutes;
    }

    if (seconds < 10) {
        seconds = '0' + seconds;
    }

    return (minutes + ':' + seconds + ':' + milliseconds);
}

/**
 * Converts a formatted string time ('00:00:000') to milliseconds.
 *
 * @param {String} timeStr - The string time to convert.
 *
 * @returns {number} - The time in milliseconds.
 */
function convertStringTimeToMilliseconds(timeStr) {
    var strings = timeStr.split(':'),
        minutes = strings[0] * 60000,
        seconds = strings[1] * 1000,
        milliseconds = strings[2] * 1;

    return minutes + seconds + milliseconds;
}

/**
 * showDialog - Display a modal dialog using a given configuration.
 * @param {!Object} config - dialog configuration object.
 */
function showDialog(config) {
    if (!config) {
        throw new Error ("Developer Error: config object for 'ShowDialog' is empty or null");
    }

    var cb = config.cb || function () {};

    $(config.selector || '#dialog').dialog({
        title: config.title || '',
        dialogClass: config.class || '',
        autoOpen: true,
        width: config.width || 500,
        modal: true,
        closeOnEscape: true,
        position: config.position || { my: "center", at: "center", of: window },
        buttons: config.buttons || {
            'Yes': function () {
                if (typeof cb == 'function') {
                    cb();
                }
                $(this).dialog('close');
            },
            'No': function () {
                $(this).dialog('close');
            }
        },
        close: config.close || ''
    });
}
