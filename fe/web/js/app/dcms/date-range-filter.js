//add by Rizqi Fitrianto override Rizal
//date range picker helper
//27-02-2018 override 03052021
var dateRangeHelper = function (startClass, endClass, targetClass, limit = true) {
    //declare variable
    var start = $(startClass);
    var end = $(endClass);
    var target = $(targetClass);

    start.attr('readonly', true);
    end.attr('readonly', true);


    // Options
    var oneDay = 24 * 60 * 60 * 1000;
    // var rangeDemoFormat = '%e-%b-%Y';
    var rangeDemoFormat = '%d-%m-%Y'; /* THIS LINE IS OVERRIDE */
    var rangeDemoConv = new AnyTime.Converter({ format: rangeDemoFormat, moment: moment() });

    $('#rangeDemoToday').click(function (e) {
        start.val(rangeDemoConv.format(new Date())).change();
    });

    // Clear dates
    $('#rangeDemoClear').click(function (e) {
        start.val('').change();
    });
    // Start date
    start.AnyTime_noPicker().AnyTime_picker({
        format: rangeDemoFormat
    });

    try {

        let fromDay = rangeDemoConv.parse(start.val()).getTime()
        var dateValue;
        let dayLater = new Date(fromDay + oneDay);

        let ninetyDaysLater = new Date(fromDay + (90 * oneDay));
        if (limit) {
            ninetyDaysLater = new Date(fromDay + (50 * 12 * 30 * oneDay));//5 tahun kedepan
        }
        dayLater.setHours(0, 0, 0, 0);

        let valEnd = rangeDemoConv.format(dayLater);
        if (end.val()) {
            valEnd = rangeDemoConv.format(end.val());
        }
        end.AnyTime_noPicker()
            .removeAttr('disabled')
            .val(valEnd)
            .AnyTime_picker({
                earliest: dayLater - oneDay,
                format: rangeDemoFormat,
                latest: ninetyDaysLater
            })
    } catch (error) {
    }

    // On value change
    start.change(function (e) {
        try {
            fromDay = rangeDemoConv.parse(start.val()).getTime();

            dayLater = new Date(fromDay + oneDay);
            dayLater.setHours(0, 0, 0, 0);
            if (limit) {
                ninetyDaysLater = new Date(fromDay + (50 * 12 * 30 * oneDay));//5 tahun kedepan
            } else {
                ninetyDaysLater = new Date(fromDay + (90 * oneDay));
            }

            ninetyDaysLater.setHours(23, 59, 59, 999);

            // End date
            end.AnyTime_noPicker()
                .removeAttr('disabled')
                .AnyTime_picker({
                    earliest: dayLater - oneDay,
                    format: rangeDemoFormat,
                    latest: ninetyDaysLater
                })
            if (new Date(start.val()) > new Date(end.val())) {
                end.AnyTime_noPicker().val(rangeDemoConv.format(dayLater))
            }
            dateValue = start.val() + ' - ' + end.val();
            target.val(dateValue);
        }

        catch (e) {
            // Disable End date field
            end.val('').attr('disabled', 'disabled');
        }
    });

    end.change(function (e) {
        dateValue = start.val() + ' - ' + end.val();
        target.val(dateValue);
    })
}