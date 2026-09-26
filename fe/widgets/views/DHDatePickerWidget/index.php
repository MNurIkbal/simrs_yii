<?php
    use yii\web\View;
?>

<div class="input-group">
    <input placeholder="Pilih Tanggal" value="<?= $defaultValue ?>" type="text" class="form-control picker__input <?= $nameId ?>" data-mask="99-99-9999" id="<?= $nameId ?>" />
    <label class="input-group-addon btn" style="padding:5px;" id="<?= $btnNameId ?>">
        <span class="fa fa-calendar"></span>
    </label>
    <input type="text" style="display:none" class="<?= $targetClass ?>" col-index=1 readonly="true">
</div>

<?php
$phpVars = [];
$this->registerJs("
var configPickADate = {
    editable: true,
    format: 'dd-mm-yyyy',
    formatSubmit: 'dd-mm-yyyy',
    selectMonths: true,
    selectYears: `$defaultYears`,
    onClose: function () {
        $('.datepicker').focus();
    }
};
var minDate = `$minDate`
var maxDate = `$maxDate`
if(minDate != '') {
    minDate = Date.parse(minDate)
    configPickADate = {
        ...configPickADate, 
        min: minDate
    }
} else {
    minDate = null
}
if(maxDate != '') {
    maxDate = Date.parse(maxDate)
    configPickADate = {
        ...configPickADate, 
        max: maxDate
    }
} else {
    maxDate = null
}
var xdateHelperWidget = function (xdates, targetClass, limit = true) {
    var xdates = $(xdates);
    var target = $(targetClass);
    var dateValue;
    xdates.change(function (e) {
        dateValue = xdates.val();
        target.val(dateValue);
    });
    xdates.on('keydown', function (e) {
        if (e.which == 13) {
            dateValue = xdates.val();
            target.val(dateValue);
        }
    });
}
$(document).on('click', '#$btnNameId', function (event) {
    var xdateTmp = $('#$nameId').pickadate(configPickADate);
    var picker_xDate = xdateTmp.pickadate('picker');
    if (picker_xDate.get('open')) {
        picker_xDate.close();
    } else {
        picker_xDate.open();
    }
    event.stopPropagation();
});
", View::POS_READY);
$this->registerJs("xdateHelperWidget('#$nameId', '.$targetClass');", View::POS_READY);
?>