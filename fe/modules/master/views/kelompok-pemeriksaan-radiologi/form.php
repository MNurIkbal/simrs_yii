<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-23 14:11:00
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-07-05 13:14:17
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<!-- Start avtive form -->
<?php $form = ActiveForm::begin([
	'id' => 'form', 
	'type' => ActiveForm::TYPE_HORIZONTAL,
	'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<!-- Modal header -->
<div class="modal-header bg-inverse">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h5 class="modal-title"><?= $model->kode_kelompok == null ? Yii::t('fe', 'Tambah Data') : Yii::t('fe', 'Ubah Data') ?></h5>
</div>

<!-- Modal body -->
<div class="modal-body">
	<?= $form->field($model, 'kode_kelompok', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm kode_unique']) ?>
	<?= $form->field($model, 'nama_kelompok', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm remove_space']) ?>
</div>

<!-- Modal footer -->
<div class="modal-footer">
	<?= Html::submitButton("<i class='fa fa-floppy-o'></i> ". Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm']) ?>
	<?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
		'class' => 'btn bg-slate btn-sm',
		'data-dismiss' => 'modal'
	]) ?>
</div>
<?php ActiveForm::end(); ?>

<!-- Javascript -->
<script type="text/javascript">


$(document).ready(function(){
	if($('#kelompokpemeriksaanradiologiform-kode_kelompok').val() != ""){
		$('#kelompokpemeriksaanradiologiform-kode_kelompok').attr('readonly','readonly');
	}
	if($('#kelompokpemeriksaanradiologiform-kode_kelompok').val() == "" ){
		$('#kelompokpemeriksaanradiologiform-kode_kelompok').removeAttr('readonly');
	}
});

$("#form").docoForm("submit", {
	success : function(data) {
			$('#modal_backdrop').modal('toggle');
			$("#btn-edit").prop("disabled", true);
        	$("#btn-delete").prop("disabled", true);
		// Check data status
		if (data.metadata.status == 201) {
			// Reset form
			$("#form")[0].reset();

			// Draw table
			table.draw();
			$("#btn-edit").prop("disabled", true);
			$("#btn-delete").prop("disabled", true);

		}
		else if (data.metadata.status == 200) {
			// Draw table
			table.draw();
			$("#btn-edit").prop("disabled", true);
			$("#btn-delete").prop("disabled", true);
		}
	}
});
</script>