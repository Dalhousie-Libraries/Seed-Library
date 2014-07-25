/**
 *  Finds an object in a JSON array and delete it.
 *  As copied from:
 *  http://stackoverflow.com/questions/6310623/remove-item-from-json-array-using-its-name-value
 */
function findAndRemove(array, property, value) {
    for (var i = 0; i < array.length; i++) {
        if (array[i][property] == value) {
            //Remove from array
            array.splice(i, 1);
            break;
        }
    }
}

/**
 *  Checks if an array contains a specific value.
 */
function contains(array, property, value) {
    for (var i = 0; i < array.length; i++)
        if (array[i][property] == value)
            return true;
}

/**
 * Checks if page is inside an iframe.
 * @returns {Boolean}
 */
function inIframe() {
    try {
        return window.self !== window.top;
    } catch (e) {
        return true;
    }
}

/**
 * Redirects to another page.
 */
function redirect(link) {
    window.location.replace(link);
}

/**
 * Convert a MYSQL date to AngularJS ISO string
 */
function dateToISOString(date, timezone) {
    return date.replace(' ', 'T') + '.000' + (timezone ? timezone : 'Z');
}

/**
 * Go back to the last page.
 */
function goBack(step)
{
    if (step) history.go(step);
    else history.go(-1)
    
    return false;
}