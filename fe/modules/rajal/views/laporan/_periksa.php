<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 14:40:50
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-01-25 16:01:57
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
    <?=$form
        ->field($modelPendaftaran, 'pegawai_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($data_pegawai, 'id_pegawai', 'nama_pegawai'), [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
            'options' => [$modelPendaftaran->pegawai_id => ['selected' => true]]
        ]);
    ?>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', 'Simpan'), ['class' => 'btn btn-success btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', 'Kembali'),['class' => 'btn btn-default btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $("#form-periksa").docoForm("submit",{
        success : function(data) {
            tabel.draw();
            $("#modal_backdrop").modal("toggle");
        }
    });
</script>

<?php 
$this->registerJs($this->render('js/_informasi.js'));
?>