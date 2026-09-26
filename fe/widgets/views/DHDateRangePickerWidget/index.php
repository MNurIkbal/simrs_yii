<?php

use yii\web\View;
?>

<div class='input-group' style='margin-bottom:0px !important;'>
    <input value='<?php echo $startTime ?>' type='text' class='dhwidget-daterangepicker dhwidget-daterangepicker-start form-control picker__input <?php echo $startId ?>' data-mask='99-99-9999' id='<?php echo $startId ?>' />
    <label class='input-group-addon btn' style='padding:5px;' id='btn_add_start_date_custom<?php echo $startId ?>'>
        <span class='fa fa-calendar'></span>
    </label>
    <span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span>
    <input type='text' value='<?php echo $endTime ?>' class='dhwidget-daterangepicker dhwidget-daterangepicker-end form-control picker__input <?php echo $endId ?>' data-mask='99-99-9999' id='<?php echo $endId ?>' />
    <label class='input-group-addon btn' style='padding:5px;' id='btn_add_end_date_custom<?php echo $endId ?>'>
        <span class='fa fa-calendar'></span>
    </label>
    <input type='text' style='display:none' class='<?php echo $nameId ?>' col-index=2 readonly='true'>
</div>

<?php
$phpVars = [
    'startId' => $startId,
    'endId' => $endId,
    'nameId' => $nameId,
    'withDefault' => $withDefault
];
$this->registerJsVar($nameId, $phpVars);
$this->registerJs("
var { withDefault } = $nameId;
dateRangeHelper2(`.$startId`, `.$endId`, `.$nameId`, true);
var btnStartString = '#btn_add_start_date_custom$startId';
var btnEndString = '#btn_add_end_date_custom$endId';
// Unbind Event
$(document).off('click', btnStartString);
$(document).off('click', btnEndString);
// Bind Again Event
$(document).on('click', btnStartString, function (event) {
    var localStartDate = $(`#$startId`).pickadate({
        editable: true,
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
        selectMonths: true,
        selectYears: true,
        onClose: function () {
            $('.datepicker').focus();
        }
    });
    var picker_startDate = localStartDate.pickadate('picker');
    if (picker_startDate.get('open')) {
        picker_startDate.close();
    } else {
        picker_startDate.open();
    }
    var _endDate = $(`#$endId`).pickadate('picker');
    try {
        var checked = _endDate.get('open');
    } catch (e) {
        checked = null;
    }
    if (checked) {
        _endDate.close();
    }
    event.stopPropagation();
});

$(document).on('click', btnEndString, function (event) {
    var localEndDate = $(`#$endId`).pickadate({
        editable: true,
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
        selectMonths: true,
        selectYears: true,
        onClose: function () {
        $('.datepicker').focus();
        }
    });
    var picker_endDate = localEndDate.pickadate('picker');
    if (picker_endDate.get('open')) {
        picker_endDate.close();
    } else {
        picker_endDate.open();
    }
    var _startDate = $(`#$startId`).pickadate('picker');
    try {
        var checked = _startDate.get('open');
    } catch (e) {
        checked = null;
    }
    if (checked) {
        _startDate.close();
    }
    event.stopPropagation();
});

if (withDefault) runDefaultValue();

function runDefaultValue() {
    // Bugfix value (ke replace sama global function bootstrapFilter)
    setTimeout(function(){
        $(`#$startId`).val(`$startTime`);
        $(`#$endId`).val(`$endTime`);
    }, 10)
}
", View::POS_READY);

// $this->registerJs($this->render('index.js'), View::POS_READY);
// $this->registerJs($this->render('functionDatepicker.js'), View::POS_END);
?>