const bindCheckboxWithSpace = (parentElement) => {
    parentElement.find('input[type="checkbox"]').on('keyup', (e) => {
        const { delegateTarget } = e
        if (e.keyCode === 32) {
            $(delegateTarget).trigger('click')
        }
    })
}

const bindRadioWithSpace = (parentElement, delegateTarget) => {
    const radioList = parentElement.find('.radio-inlineo')
    parentElement.find('input[type="radio"]').on('keyup', (e) => {
        const { delegateTarget } = e
        if (e.keyCode === 32) {
            $(delegateTarget).prop('checked', true).trigger('click')
        }
    })
}

$(() => {
    $('body').bind('keyup', function (e) {
        if (e.shiftKey && e.which == 39) {
            if ($(`#steps-uid-0-t-${_formPendaftaran.indexActive + 1}`).length > 0) {
                $(`#steps-uid-0-t-${_formPendaftaran.indexActive + 1}`).trigger('click')
            } else if ($('.confirm-message-text').length == 0) {
                $('a[href="#finish"]').trigger('click');
            }
        } else if (e.shiftKey && e.which == 37 && _formPendaftaran.indexActive > 0) {
            $(`#steps-uid-0-t-${_formPendaftaran.indexActive - 1}`).trigger('click')
        }
    });
    document.onkeydown = function (evt) {
        evt = evt || window.event;
        if (evt.keyCode == 27) {
            $('input,textarea,select').blur();
        }
    };

    /** Tab Order */
    $(document).on('select2:close', '.select2', function (e) {
        $(this).focus();
    });
})