    
var dateRangeOnDemand = function (startClass, endClass, targetClass) {
    //declare variable
    var start = $(startClass);
    var end = $(endClass);
    var target = $(targetClass);

    start.attr('readonly', true);
    end.attr('readonly', true);

    var oneDay = 24 * 60 * 60 * 1000;
    var rangeDemoFormat = '%e-%b-%Y';
    var rangeDemoConv = new AnyTime.Converter({ format: rangeDemoFormat, moment: moment() });
try{
    let fromDay = rangeDemoConv.parse(start.val()).getTime()
    var dateValue;
    let dayLater = new Date(fromDay + oneDay);

    dayLater.setHours(0, 0, 0, 0);

    let valEnd = rangeDemoConv.format(dayLater);
    if(end.val()){
        valEnd = rangeDemoConv.format(end.val());
    }
    end.AnyTime_noPicker()
        .removeAttr('disabled')
        .val(valEnd)
        .AnyTime_picker({
            earliest: dayLater - oneDay,
            format: rangeDemoFormat
        })
}catch(error){}

    start.click(function(e){
        start.AnyTime_noPicker().AnyTime_picker({format:rangeDemoFormat}).focus();
        e.preventDefault();
    });
    end.click(function(e){
        end.AnyTime_noPicker().removeAttr("disabled").AnyTime_picker({format:rangeDemoFormat}).focus();
        e.preventDefault();
    });
    start.change(function (e) {

         try {
            fromDay = rangeDemoConv.parse(start.val()).getTime();

            dayLater = new Date(fromDay + oneDay);
            dayLater.setHours(0, 0, 0, 0);

            let valEnd = rangeDemoConv.format(dayLater);
            // if(end.val()){
            //     valEnd = rangeDemoConv.format(end.val());
            // }
            // End date
            end.AnyTime_noPicker()
                .removeAttr('disabled')
                .val(valEnd)
                .AnyTime_picker({
                    earliest: dayLater - oneDay,
                    format: rangeDemoFormat
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
        dateValue = start.val() + " - " + end.val();
        target.val(dateValue);
    });
}