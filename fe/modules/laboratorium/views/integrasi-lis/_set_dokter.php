<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$form = ActiveForm::begin([
    'id' => 'form-set-dokter',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="form-group">
        <div class="col-md-4">
            <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Waktu Periksa'); ?></label>
        </div>
        <div class="col-md-8">
            <b><span id="tglmasukpenunjang"></span></b>
            <?= Html::hiddenInput('InfoPasienLabView[tglmasukpenunjang]', $modelPasien->tglmasukpenunjang);?>
        </div>
    </div>
    <?=$form
        ->field($modelPasien, 'pegawai_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($data_pegawai, 'pegawai_id', 'nama_pegawai'), [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', '-- Pilih --'),
            // 'disabled' => true,
        ]);
    ?>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', 'Masuk'), ['class' => 'btn btn-success btn-sm']); ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        //jQuery(".btn, .btn-success, .btn-sm").removeClass("btn-toolbar");
    });

    $("#form-set-dokter").docoForm("submit",{
        success : function(data) {
            window.location = data.url;
            table.draw();
            $("#modal_backdrop").modal("toggle");
            $("#batal-form").hide();
        }
    });
</script>

<?php
$this->registerJs($this->render('js/_set_dokter.js'));
?>