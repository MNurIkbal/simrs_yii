function matchSigna (params, data) {
    if ($.trim(params.term) === '') {
        return data;
    }

    // Do not display the item if there is no 'text' property
    if (typeof data.text === 'undefined') {
        return null;
    }
    var dataText = data.text.replace(/[^a-zA-Z0-9]/gi, '').toUpperCase();
    var termText = params.term.replace(/[^a-zA-Z0-9]/gi, '').toUpperCase();
    // `params.term` should be the term that is used for searching
    // `data.text` is the text that is displayed for the data object

    if (dataText.indexOf(termText) > -1) {
        var modifiedData = $.extend({}, data, true);

        // You can return modified objects from here
        // This includes matching the `children` how you want in nested data sets
        return modifiedData;
    }

    // Return `null` if the term should not be displayed
    return null;
}
