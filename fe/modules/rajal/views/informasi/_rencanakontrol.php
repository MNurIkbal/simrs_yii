<?php

/**
 * @Author: afil
 * @Date:   2018-01-17 11:41:12
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-16 10:01:55
 * @Description: 
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$form = ActiveForm::begin([
    'id' => 'form-rencanaKontrol', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableAjaxValidation'=>false, 
    'enableClientValidation'=>false,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($modelInformasi, 'no_rekam_medik', ['labelOptions' => ['class' => 'text-left']])
        ->textInput([
            'class' => 'form-control input-sm',
            'readonly' => 'readonly'

        ]);
    ?>
    <?=$form->field($modelInformasi, 'no_pendaftaran', ['labelOptions' => ['class' => 'text-left']])
        ->textInput([
            'class' => 'form-control input-sm',
            'readonly' => 'readonly'

        ]);
    ?>
    <?=$form->field($modelInformasi, 'nama_pasien', ['labelOptions' => ['class' => 'text-left']])
        ->textInput([
            'class' => 'form-control input-sm',
            'readonly' => 'readonly'

        ]);
    ?>
    <?=$form->field($modelJanjiPoli, 'tgl_jadwal', ['labelOptions' => ['class' => 'text-left']])
        ->textInput([
            'class' => 'form-control input-sm date',
        ]);
    ?>
    <?=Html::hiddenInput('BuatJanjiPoliForm[pendaftaran_id]', $data_pasien["pendaftaran_id"]);?>
    <?=Html::hiddenInput('BuatJanjiPoliForm[pasien_id]', $data_pasien["pasien_id"]);?>
    <?=Html::hiddenInput('BuatJanjiPoliForm[antrian_id]', $data_pasien["antrian_id"]);?>
    <?=Html::hiddenInput('BuatJanjiPoliForm[pegawai_id]', $data_pasien["pegawai_id"]) ?>
    <?=Html::hiddenInput('BuatJanjiPoliForm[carabayar_id]', $data_pasien["carabayar_id"]) ?>
    <?=Html::hiddenInput('BuatJanjiPoliForm[penjamin_id]', $data_pasien["penjamin_id"]) ?>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', 'Simpan'), ['class' => 'btn btn-success btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', 'Kembali'),['class' => 'btn btn-default btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $("#form-rencanaKontrol").docoForm("submit",{
        success : function(data) {
            tabel.draw();
            $("#modal_backdrop").modal("toggle");
        }
    });
</script>

<?php 
$this->registerJs($this->render('js/_informasi.js'));
?>