<?php

/**
 * @Author: Budi
 * @Date:   2020-01-28 17:06:50
 * @Description:
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$form = ActiveForm::begin([
    'id' => 'form-periksa',
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
            <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Waktu periksa'); ?></label>
        </div>
        <div class="col-md-8">
            <b><span id="tgl_masukperiksa"></span></b>
            <?= Html::hiddenInput('PendaftaranForm[tgl_masukperiksa]', $modelPendaftaran->tgl_masukperiksa);?>
        </div>
    </div>
    <?=$form
        ->field($modelPendaftaran, 'pegawai_id', ['labelOptions' => ['class' => 'text-left']])
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
    $("#form-periksa").docoForm("submit",{
        success : function(data) {
            window.location = data.url;
            table.draw();
            $("#modal_backdrop").modal("toggle");
            $("#batal-form").hide();
        }
    });
</script>

<?php
$this->registerJs($this->render('js/_informasi.js'));
?>