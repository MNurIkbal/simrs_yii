<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;

?>

<style type="text/css">
    #jenisobatalkes_id {
        margin-bottom: 2%;
    }
</style>

<?php $form = ActiveForm::begin([
    'id' => 'form-generate-ro',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<!-- Modal header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">
        <?= Yii::t('fe', 'Rekomendasi Order') ?>
    </h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <?= $form->field($model, 'is_consignment')->checkbox(); ?>

    <div class="form-group highlight-addon field-jenisobatalkes_id required">
        <label class="control-label has-star col-sm-4" for="jenisobatalkes_id">Jenis Obat Alkes</label>
        <div class="col-sm-8">
            <button type="button" 
                id="btn-check-jenisobat" 
                name="RecommendationOrderForm[jenisobatalkes_id]"
                class="btn btn-info btn-labeled btn-xs btn-custom-jenis-obat" 
                data-toggle="modal" 
                data-target="#modal_list_jenis_obat">
                    <b><i class="fa fa-list"></i></b>Jenis Obat Alkes
            </button>
        </div>
    </div>

    <?= $form->field($model, 'days_of_inventory', [
            'labelOptions' => [
                'class' => 'text-left'
            ]
        ])->textInput([
            'class' => 'form-control input-sm doco-number',
            'id' => 'days_of_inventory'
        ]); ?>
</div>

<!-- Modal footer -->
<div class="modal-footer">
    <?= Html::button("<b><i class='fa fa-gears'></i></b>&nbsp;Generate Rekomendasi Order", [
        'class' => 'btn btn-info btn-labeled btn-xs',
        'id' => 'btn-generate-ro'
    ]) ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
var checked_jenis = [];
var narkotika_id = "<?= $narkotika_id ?>";
var isConsignment = false;
var urlJenisObat = "/pengadaan/purchase-requisition/list-jenis-obat-alkes?is_consignment=";
$(document).ready(function() {
    function checkConsignment() {
        isConsignment = $("#recommendationorderform-is_consignment").is(":checked");
        checked_jenis = []
        $("#days_of_inventory").prop("disabled", isConsignment);
    }

    if($("#purchaserequisitionform-is_consignment").is(":checked")) {
        $("#recommendationorderform-is_consignment").prop("checked", true).change();
        checkConsignment();
    }

    $("#recommendationorderform-is_consignment").on("change", function (e) {
        checkConsignment();
    })

    $("#btn-check-jenisobat").on("click", function (e) {
        $(this).attr("action", urlJenisObat + isConsignment)
    })

    $("#jenisobatalkes_id").on("change", function(e) {
        if(this.value == narkotika_id) {
            $("#days_of_inventory").val(30)
        }
    });
});
</script>
