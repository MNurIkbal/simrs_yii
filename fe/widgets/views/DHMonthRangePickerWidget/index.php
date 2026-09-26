<?php

use yii\web\View;
?>

<style>
    .datepicker>div {
        display: block;
    }
</style>

<div class='input-group widget-datepicker' style='margin-bottom:0px !important;'>
    <input type="text" id="<?= $startId ?>" class="form-control month-range-picker" data-location='start' readonly value='<?php echo $startTime ?>'>
    <div class="input-group-addon">to</div>
    <input type="text" id="<?= $endId ?>" class="form-control month-range-picker" data-location='end' readonly value='<?php echo $endTime ?>'>
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
var { withDefault, startId, endId } = $nameId;
const elementNameStart = `#${startId}`;
const elementNameEnd = `#${endId}`;
let startMonthPicker = $(elementNameStart).datepicker({
    format: `mm-yyyy`,
    startView: `months`,
    minViewMode: `months`,
});
let endMonthPicker = $(elementNameEnd).datepicker({
    format: `mm-yyyy`,
    startView: `months`,
    minViewMode: `months`,
});
$(elementNameStart).on(`change`, function () {
    if (!withDefault) {
        const endVal = $(elementNameEnd).val();
        if (!endVal) endMonthPicker.datepicker('setDate', $(this).val());
    }
    endMonthPicker.datepicker('setStartDate', $(this).val());
})
$(elementNameEnd).on(`change`, function () {
    if (!withDefault) {
        const startVal = $(elementNameStart).val();
        if (!startVal) startMonthPicker.datepicker('setDate', $(this).val());
    }
    startMonthPicker.datepicker('setEndDate', $(this).val());
})

// Set Default
if (withDefault) runDefaultValue();

function runDefaultValue() {
    setTimeout(function(){
        $(elementNameStart).datepicker(`setDate`,`$startTime`);
        $(elementNameEnd).datepicker(`setDate`,`$endTime`);
    }, 10)
}

", View::POS_READY);

// $this->registerJs($this->render('index.js'), View::POS_READY);
// $this->registerJs($this->render('functionDatepicker.js'), View::POS_END);
?>