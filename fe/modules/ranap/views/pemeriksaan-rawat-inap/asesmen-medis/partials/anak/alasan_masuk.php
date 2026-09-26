<?php
use yii\web\View;

$classForm = 'form-control input-sm'
?>

<div class="row" style="margin-top:15px;">
    <div class="col-md-12 form-group">
        <div class="col-sm-6">
            <?= $form->field($model, 'sumber_data')->radioList(
                ['1' => 'Pasien', '2' => 'Keluarga Terdekat', '3' => 'Lain-Lain'],
                [
                    'itemOptions' => [
                        'class' => 'sumber_data'
                    ],
                    'inline' => 'true',
                ]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'sumber_data_lainnya')->label(false)->textInput(['class' => $classForm]); ?>
        </div>
    </div>
</div>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">A. Alasan Masuk</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'keluhan_utama')->label()->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'riwayat_keluhan')->label()->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var sumber_data_lainnya = "'.$model->sumber_data_lainnya.'"

$(document).ready(function(){
    const otherSumberData = $("#anakform-sumber_data_lainnya");
    otherSumberData.prop("readonly", true);

    if(asesmenMedisId) {
        if(sumber_data_lainnya) {
            otherSumberData.prop("readonly", false);
        }
    }
    
    $(document).on("change", ".sumber_data", function(){
        if($(this).is(":checked")) {
            if($(this).val() == "3") {
                otherSumberData.prop("readonly", false);
            }
            else {
                otherSumberData.val("").prop("readonly", true);
            }
        }
    })
})
', View::POS_END);
?>
