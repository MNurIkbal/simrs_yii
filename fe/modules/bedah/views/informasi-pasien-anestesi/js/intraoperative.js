window.timeoutAnestesi = null;

function calculateDifference(endObject, startObject, max) {
    var end = (endObject.hour * 60) + endObject.minute;
    var start = (startObject.hour * 60) + startObject.minute;

    if (end < start) {
        if (((end + 1440) - start) <= max) {
            end = end + 1440;
        }
    }

    return end - start;
}

function formatDifference(length) {
    if (length < 60) {
        return length + (length > 1 ? " Minutes" : " Minute");
    }

    var hour = Math.floor(length / 60);
    var minute = length - (hour * 60);

    var result = hour + (hour > 1 ? " Hours" : " Hour");
    if (minute > 0) {
        result += " " + minute + (minute == 0 ? " Minute" : "Minutes");
    }
    return result;
}

function displayGeneratedTime(startObject, add) {
    var total = (startObject.hour * 60) + startObject.minute + add;
    var hour = Math.floor(total / 60);
    var minute = total - (hour * 60);
    if (hour > 23) {
        hour = hour - 23;
    }
    return hour + ":" + (minute.toString().padStart(2, 0));
}

function validateIntraOperativeTime(minutes, element, label, min, max) {
    if (minutes <= min || minutes > max) {
        var message = minutes > max ? 'Maximum length is ' + (max / 60) + ' Hours' : 'Length of ' + label + ' must be greather then 0';
        $(element).text(message);
        $(element).closest('.form-group').addClass('has-error');
        return false;
    } else {
        $(element).text('');
        $(element).closest('.form-group').removeClass('has-error');
        return true;
    }
}

function drawVitalSignPoint() {
    $('#table-vital-sign-anestesi .graphable').remove();
    var container = $('#table-vital-sign-anestesi').parent().offset();
    var icon1 = $('#table-vital-sign-anestesi .generated-empty-container i.intra-operative-anestesi-color.fa-chevron-up').toArray();
    var icon2 = $('#table-vital-sign-anestesi .generated-empty-container i.intra-operative-anestesi-color.fa-chevron-down').toArray();
    icon1.sort(function(a, b) {
        var al = $(a).offset().left;
        var bl = $(b).offset().left;
        if (al === bl) {
            return 0;
        }
        if (al > bl) {
            return 1;
        } else {
            return -1;
        }
    });
    icon2.sort(function(a, b) {
        var al = $(a).offset().left;
        var bl = $(b).offset().left;
        if (al === bl) {
            return 0;
        }
        if (al > bl) {
            return 1;
        } else {
            return -1;
        }
    });
    for (var i = 1; i < icon1.length; i++) {
        var newElement = $('<div class="graphable"></div>');
        var prevPosition = $(icon1[i - 1]).offset();
        var curPosition = $(icon1[i]).offset();
        newElement.css('left', (prevPosition.left - container.left + ($(icon1[i]).width() / 2)).toString() + 'px');
        newElement.css('top', ( Math.min(prevPosition.top, curPosition.top) - container.top + ($(icon1[i]).height() / 2)).toString() + 'px');
        newElement.css('width', (curPosition.left - prevPosition.left).toString() + 'px');
        if (prevPosition.top === curPosition.top) {
            newElement.addClass('line-horizontal-dash');
        } else {
            newElement.css('height', ( Math.max(prevPosition.top, curPosition.top) - Math.min(prevPosition.top, curPosition.top)).toString() + 'px');
            if (prevPosition.top < curPosition.top) {
                newElement.addClass('line-topdown-dash');
            } else {
                newElement.addClass('line-bottomup-dash');
            }
        }
        $(icon1[i]).after(newElement);
    }
    for (var i = 1; i < icon2.length; i++) {
        var newElement = $('<div class="graphable"></div>');
        var prevPosition = $(icon2[i - 1]).offset();
        var curPosition = $(icon2[i]).offset();
        newElement.css('left', (prevPosition.left - container.left + ($(icon2[i]).width() / 2)).toString() + 'px');
        newElement.css('top', ( Math.min(prevPosition.top, curPosition.top) - container.top + ($(icon2[i]).height() / 2)).toString() + 'px');
        newElement.css('width', (curPosition.left - prevPosition.left).toString() + 'px');
        if (prevPosition.top === curPosition.top) {
            newElement.addClass('line-horizontal-dash');
        } else {
            newElement.css('height', ( Math.max(prevPosition.top, curPosition.top) - Math.min(prevPosition.top, curPosition.top)).toString() + 'px');
            if (prevPosition.top < curPosition.top) {
                newElement.addClass('line-topdown-dash');
            } else {
                newElement.addClass('line-bottomup-dash');
            }
        }
        $(icon2[i]).after(newElement);
    }
}

$("#table-vital-sign-anestesi").ready(function() {
    setTimeout(drawVitalSignPoint, 500);
});
    
$(window).on('resize', drawVitalSignPoint);

function regenerateColumns(startObject, length) {
    var totalcol = Math.floor(length / 5) + 1;
    $(".fullcol", "#table-vital-sign-anestesi").attr("colspan", totalcol);
    $(".regenerateable", "#table-vital-sign-anestesi").remove();
    for (var i = 0; i <= length; i+=5) {
        var time = displayGeneratedTime(startObject, i);
        $("#generated-time-container", "#table-vital-sign-anestesi").append(
            "<td class='regenerateable'><div class='static-width'>" + time + "</div></td>"
        );
        $("#generated-button-container", "#table-vital-sign-anestesi").append(
            "<td class='regenerateable'>" +
            '<div class="static-width" rel="data-' + time + '">' +
            "<button  data-target='#modal_backdrop' type='button' data-toggle='modal' data-options='modal' class='btn btn-success' action='/bedah/informasi-pasien-anestesi/modal-intra-operative?time=" + window.encodeURIComponent(time) + "'><i class='fa fa-plus'></i></button>" +
            '<input type="hidden" class="intra-operative-anestesi-sign-time" value="' + time + '">' +
            '<input type="hidden" class="intra-operative-anestesi-sign-rr">' +
            '<input type="hidden" class="intra-operative-anestesi-sign-hr">' +
            '<input type="hidden" class="intra-operative-anestesi-sign-systolic">' +
            '<input type="hidden" class="intra-operative-anestesi-sign-diastolic">' +
            "</div>" +
            "</td>"
        );
        $(".generated-empty-container", "#table-vital-sign-anestesi").each(function() {
            $(this).append(
                "<td class='regenerateable'><div class='static-width' rel='draw-" + time + "'></div></td>"
            );
        });
        $(".generated-input-container", "#table-vital-sign-anestesi").each(function() {
            $(this).append(
                "<td class='regenerateable'>" +
                "<div class='static-width'>" +
                "<input type='text' class='form-control doco-number intra-operative-anestesi-monitoring-vinput' autocomplete='off'>" +
                '<input type="hidden" class="intra-operative-anestesi-monitoring-time" value="' + time + '">' +
                '<input type="hidden" class="intra-operative-anestesi-monitoring-monitoring_id" value="' + $(this).closest('tr').find('td:eq(0)').attr('rel') + '">' +
                "</div>" +
                "</td>"
            );
        });
    }
}

$("#table-vital-sign-anestesi").on("input keyup keypress keydown", ".intra-operative-anestesi-monitoring-vinput", function () {
    if (window.timeoutAnestesi != null) {
        clearTimeout(window.timeoutAnestesi);
    }
    var length1 = parseInt($('#intraoperativeanestesiform-length_anesthesia').val());
    var length2 = parseInt($('#intraoperativeanestesiform-length_surgery').val());
    if (
        validateIntraOperativeTime(length1, '#error_IntraOperativeAnestesiFormlength_anesthesia', 'Anesthesia', 0, 300) &&
        validateIntraOperativeTime(length2, '#error_IntraOperativeAnestesiFormlength_surgery', 'Surgery', 0, 1440) &&
        $(this).val().length > 0
    ) {
        var urlAction = $('#component-intraoperative').data('action');
        var container = $(this).closest('div.static-width');
        window.timeoutAnestesi = setTimeout(function() {
            var data = $("#root-model-intra-operative-anestesi *").serializeArray();
            var vinput = $('input.intra-operative-anestesi-monitoring-vinput', container).val();
            var time = $('input.intra-operative-anestesi-monitoring-time', container).val();
            var monitoring_id = $('input.intra-operative-anestesi-monitoring-monitoring_id', container).val();
            data.push({
                name: 'IntraOperativeAnestesiForm[other_monitoring_individual][time]',
                value: time
            },{
                name: 'IntraOperativeAnestesiForm[other_monitoring_individual][vinput]',
                value: vinput
            },{
                name: 'IntraOperativeAnestesiForm[other_monitoring_individual][monitoring_id]',
                value: monitoring_id
            });
            $().docoForm('click', {
                skipConfirm: true,
                data: data,
                url: urlAction,
                success: function (response) {
                    $('#intraoperativeanestesiform-anestesiintraopr_id').val(response.item.anestesiintraopr_id);
                    $('.timeinduction').each(function() {
                        if ($(this).is(':not([readonly])')) {
                            $(this).off().attr('readonly', true).data('timepicker').remove();
                        }
                    });
                }
            });
        }, 500);
    }
});

$(document).on("click", "#save-button-intra-operative-anestesi", function () {
    var length1 = parseInt($('#intraoperativeanestesiform-length_anesthesia').val());
    var length2 = parseInt($('#intraoperativeanestesiform-length_surgery').val());
    if (
        validateIntraOperativeTime(length1, '#error_IntraOperativeAnestesiFormlength_anesthesia', 'Anesthesia', 0, 300) &&
        validateIntraOperativeTime(length2, '#error_IntraOperativeAnestesiFormlength_surgery', 'Surgery', 0, 1440)
    ) {
        var urlAction = $('#component-intraoperative').data('action');
        var data = $("#root-model-intra-operative-anestesi *").serializeArray();
        var time = $('#modal-intra-operative-anestesi-time').val();
        var rr = $('#modal-intra-operative-anestesi-rr').val();
        var hr = $('#modal-intra-operative-anestesi-hr').val();
        var systolic = $('#modal-intra-operative-anestesi-systolic').val();
        var diastolic = $('#modal-intra-operative-anestesi-diastolic').val();
        data.push({
            name: 'IntraOperativeAnestesiForm[vital_sign_individual][time]',
            value: time
        },{
            name: 'IntraOperativeAnestesiForm[vital_sign_individual][rr]',
            value: rr
        },{
            name: 'IntraOperativeAnestesiForm[vital_sign_individual][hr]',
            value: hr
        },{
            name: 'IntraOperativeAnestesiForm[vital_sign_individual][systolic]',
            value: systolic
        },{
            name: 'IntraOperativeAnestesiForm[vital_sign_individual][diastolic]',
            value: diastolic
        });
        $().docoForm('click', {
            skipConfirm: false,
            data: data,
            url: urlAction,
            success: function (response) {
                $('div.static-width[rel="draw-' + time + '"]', "#table-vital-sign-anestesi").html('');
                $('div.static-width[rel="draw-' + time + '"]', "#table-vital-sign-anestesi").each(function() {
                    if ($(this).closest('tr').find('td:eq(0)').html() == rr) {
                        $(this).append('<i class="intra-operative-anestesi-color fa fa-circle-o"></i>');
                    }
                    if ($(this).closest('tr').find('td:eq(1)').html() == hr) {
                        $(this).append('<i class="intra-operative-anestesi-color fa fa-circle"></i>');
                    }
                    if ($(this).closest('tr').find('td:eq(2)').html() == systolic) {
                        $(this).append('<i class="intra-operative-anestesi-color fa fa-chevron-down"></i>');
                    }
                    if ($(this).closest('tr').find('td:eq(2)').html() == diastolic) {
                        $(this).append('<i class="intra-operative-anestesi-color fa fa-chevron-up"></i>');
                    }
                });

                $('div.static-width[rel="data-' + time + '"] input.intra-operative-anestesi-sign-rr', "#table-vital-sign-anestesi").val(rr);
                $('div.static-width[rel="data-' + time + '"] input.intra-operative-anestesi-sign-hr', "#table-vital-sign-anestesi").val(hr);
                $('div.static-width[rel="data-' + time + '"] input.intra-operative-anestesi-sign-systolic', "#table-vital-sign-anestesi").val(systolic);
                $('div.static-width[rel="data-' + time + '"] input.intra-operative-anestesi-sign-diastolic', "#table-vital-sign-anestesi").val(diastolic);

                $('div.static-width[rel="data-' + time + '"] button.btn', "#table-vital-sign-anestesi").attr('action', '/bedah/informasi-pasien-anestesi/modal-intra-operative?time=' + window.encodeURIComponent(time) + '&hr=' + hr + '&rr=' + rr + '&systolic=' + systolic + '&diastolic=' + diastolic);

                $('.timeinduction').each(function() {
                    if ($(this).is(':not([readonly])')) {
                        $(this).off().attr('readonly', true).data('timepicker').remove();
                    }
                });
                $('#intraoperativeanestesiform-anestesiintraopr_id').val(response.item.anestesiintraopr_id);

                drawVitalSignPoint();

                $("#modal_backdrop").modal("hide");
            }
        });
    }
});

$(".usetimepicker:not([readonly])").timepicker({
    showMeridian: false,
    minuteStep: 5
});

$(".timeinduction").on("changeTime.timepicker", function() {
    var endObject = $("#intraoperativeanestesiform-end_induction").data("timepicker");
    var startObject = $("#intraoperativeanestesiform-start_induction").data("timepicker");
    var minutes = calculateDifference(endObject, startObject, 300);
    var newval = formatDifference(minutes);
    $("#length_anesthesia").val(newval);
    $("#length_anesthesia").attr("title", newval);
    $('#intraoperativeanestesiform-length_anesthesia').val(minutes);
    $('#intraoperativeanestesiform-length_anesthesia').trigger('change');
    if (!(minutes <= 0 || minutes > 300)) {
        regenerateColumns(startObject, minutes);
    }
});

$(".timesurgery").on("changeTime.timepicker", function() {
    var endObject = $("#intraoperativeanestesiform-end_surgery").data("timepicker");
    var startObject = $("#intraoperativeanestesiform-start_surgery").data("timepicker");
    var minutes = calculateDifference(endObject, startObject, 1440);
    if (minutes == 0) {
        minutes = 1440;
    }
    var newval = formatDifference(minutes);
    $("#length_surgery").val(newval);
    $("#length_surgery").attr("title", newval);
    $('#intraoperativeanestesiform-length_surgery').val(minutes);
    $('#intraoperativeanestesiform-length_surgery').trigger('change');
});

$("#intraoperativeanestesiform-patient_exit").on("changeTime.timepicker", function() {
    $('#intraoperativeanestesiform-length_surgery').trigger('change');
});

$(document).on('change', '#intraoperativeanestesiform-length_anesthesia, #intraoperativeanestesiform-length_surgery', function() {
    if (window.timeoutAnestesi != null) {
        clearTimeout(window.timeoutAnestesi);
    }
    var length1 = parseInt($('#intraoperativeanestesiform-length_anesthesia').val());
    var length2 = parseInt($('#intraoperativeanestesiform-length_surgery').val());
    if (
        validateIntraOperativeTime(length1, '#error_IntraOperativeAnestesiFormlength_anesthesia', 'Anesthesia', 0, 300) &&
        validateIntraOperativeTime(length2, '#error_IntraOperativeAnestesiFormlength_surgery', 'Surgery', 0, 1440)
    ) {
        if ($(this).attr('id') != 'intraoperativeanestesiform-length_anesthesia') {
            var urlAction = $('#component-intraoperative').data('action');
            window.timeoutAnestesi = setTimeout(function() {
                var data = $("#root-model-intra-operative-anestesi *").serializeArray();
                $().docoForm('click', {
                    skipConfirm: true,
                    skipConfirmMessage: true,
                    data: data,
                    url: urlAction,
                    success: function (response) {
                        $('#intraoperativeanestesiform-anestesiintraopr_id').val(response.item.anestesiintraopr_id);
                        $('.timeinduction').each(function() {
                            if ($(this).is(':not([readonly])')) {
                                $(this).off().attr('readonly', true).data('timepicker').remove();
                            }
                        });
                    }
                });
            }, 500);
        }
    }
});