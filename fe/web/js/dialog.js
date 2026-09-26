/**
 * Old way of using the question dialog -> use $.showQuestionDialog
 */
function showQuestionDialog(header, message, options, callback) {
    $('body').append('<div id="confirm-dialog-overlay"></div>');
    $('body').append('<div id="confirm-dialog"><div class="dialog-content"><div class="row"><h2 class="confirm-header-text"></h2></div><p class="confirm-message-text"></p></div></div>')
    $('.confirm-message-text').html(message);
    $('.confirm-header-text').text(header);

    console.log('showQuestionDialog is outdated use $.showQuestionDialog() instead');

    var overlayResponse = options.overlay || false;

    $.each(options.buttons, function(key, value) {
        var btnText = key;
        var btnClass = value;

        $('#confirm-dialog').append('<button data-btn="questionDialog" class="' + btnClass + '">' + btnText + '</button>');
    });

    $('[data-btn="questionDialog"]').bind('click', function(e) {
        $('#confirm-dialog-overlay').unbind();
        $('[data-btn="questionDialog"]').undelegate( "click" );

        if (typeof options.hidden == 'undefined') {
            hideQuestionDialog();
        }
        callback(e.currentTarget.innerText);
    });

    $('#confirm-dialog-overlay').bind("click", function() {
        if (overlayResponse !== false) {
            $('[data-btn="questionDialog"]').undelegate( "click" );
            $('#confirm-dialog-overlay').unbind();

            if (typeof options.hidden == 'undefined') {
                hideQuestionDialog();
            }
            callback(overlayResponse);
        }
    });

}

function hideQuestionDialog() {
    $('#confirm-dialog').remove();
    $('#confirm-dialog-overlay').fadeOut(350, function() {
        this.remove();
    });
}

/**
 * @author Stein Janssen <birdmage@gmail.com>
 *
 * To show question dialog call -> $.showQuestionDialog('header text', 'message text', options, function(response) {});
 *
 * The options is a object with data for the buttons in the dialog
 *
 *  var options = {
 *      overlay: 'Response when overlay was clicked here',
 *      buttons: {
 *          firstButtonText:  firstButtonClass,
 *          secondButtonText: secondButtonClass,
 *          thirdButtonText:  thirdButtonClass
 *      }
 *  };
 *
 * The next classes are avalible
 * alert                color red
 * save                 color green
 *
 * After the user clicked a button the callback function will be executed.
 * The response of the callback function will be the text of the clicked button.
 */
(function( $ ) {

    /**
     * Default html for overlay and dialog
     */
    var overlayTemplate = '<div id="confirm-dialog-overlay"></div>';
    var dialogTemplate = `
                <div id="confirm-dialog">
                    <p class="confirm-message-text"></p>
                    <div class="button-place"></div>
                </div>
                `

    /**
     * Hide the question dialog
     */
    var hideQuestionDialog = function() {
        $('#confirm-dialog').remove();
        $('#confirm-dialog-overlay').fadeOut(350, function() {
            this.remove();
        });
    }

    /**
     * showQuestionDialog function
     *
     * @param  string   header
     * @param  string   message
     * @param  object   options
     * @param  function callback
     */
    $.showQuestionDialog = function(header, message, options, callback) {
        var overlayResponse = options.overlay || false;
        let isConfirmDisabled = false;

        $('body').append(overlayTemplate);
        $('body').append(dialogTemplate);
        $('.confirm-message-text').html(message);
        $('.confirm-header-text').text(header);

        if(typeof options.confirmation != 'undefined' && options.confirmation){
            $('<div class="warning-confirm-message"><input type="checkbox" name="disable-confirmation" id="disable-confirmation">Jangan perlihatkan pesan ini lagi</div>').insertBefore('#confirm-dialog .button-place');
            $('#disable-confirmation').uniform();
        }

        if(typeof options.warningConfirmation != 'undefined' && options.warningConfirmation == false) {
            $('.warning-confirm-message').remove();
        }

        $('#confirm-dialog #disable-confirmation').bind('change', function(e) {
            isConfirmDisabled = $(this).is(":checked");
        });

        $.each(options.buttons, function(key, value) {
            var btnText = key;
            var btnClass = value;

            $('#confirm-dialog .button-place').append('<button data-btn="questionDialog" class="' + btnClass + '">' + btnText + '</button>');
        });

        $('[data-btn="questionDialog"]').bind('click', function(e) {
            $('#confirm-dialog-overlay').unbind();
            $('[data-btn="questionDialog"]').undelegate( "click" );

            if (typeof options.hidden == 'undefined') {
                hideQuestionDialog();
            }

            if (typeof options.confirmation && options.confirmation) {
                callback({
                  confirmation: e.currentTarget.innerText,
                  isDisabled: isConfirmDisabled
                });
                return;
            }

            callback(e.currentTarget.innerText);
        });

        $('#confirm-dialog-overlay').bind("click", function() {
            if (overlayResponse !== false) {
                $('[data-btn="questionDialog"]').undelegate( "click" );
                $('#confirm-dialog-overlay').unbind();

                if (typeof options.hidden == 'undefined') {
                    hideQuestionDialog();
                }
                callback(overlayResponse);
            }
        });
    }

}( jQuery ));
