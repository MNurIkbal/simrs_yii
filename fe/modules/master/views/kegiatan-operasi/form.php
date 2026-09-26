<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-23 14:11:00
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-07-09 17:39:16
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
	<h5 class="modal-title"><?= $model->kegiatanoperasi_kode == null ? Yii::t('fe', 'Tambah Data') : Yii::t('fe', 'Ubah Data') ?></h5>
</div>

<!-- Modal body -->
<div class="modal-body">
	<?= $form->field($model, 'kegiatanoperasi_kode', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm kode_unique']) ?>
	<?= $form->field($model, 'kegiatanoperasi_nama', ['labelOptions' => ['class' => 'text-right']])->textInput(['class' => 'form-control input-sm']) ?>
</div>

<!-- Modal footer -->
<div class="modal-footer">
	<div class="col-md-4">
		<?= Html::submitButton("<i class='fa fa-floppy-o'></i> " . Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm']) ?>
			<?= Html::button("<i class='fa fa-arrow-left'></i> " . Yii::t('fe', 'Batal'), [
			'class' => 'btn bg-slate btn-sm',
			'data-dismiss' => 'modal'
		]) ?>
	</div>
	<div class="col-md-8">
		<?= Html::button("<i class='fa fa-refresh'></i> " . Yii::t('fe', 'Ulang'), [
			'class' => 'btn bg-teal btn-sm',
			'onclick' => 'ulang()'
		]) ?>
	</div>
	
</div>
<?php ActiveForm::end(); ?>

<!-- Javascript -->
<script type="text/javascript">

var ulang = function(){
	$('#kegiatanoperasiform-kegiatanoperasi_kode').val('');
	$('#kegiatanoperasiform-kegiatanoperasi_nama').val('');
}

$(document).ready(function(){
	/*if($('#kelompokpemeriksaanradiologiform-kode_kelompok').val() != ""){
		$('#kelompokpemeriksaanradiologiform-kode_kelompok').attr('readonly','readonly');
	}
	if($('#kelompokpemeriksaanradiologiform-kode_kelompok').val() == "" ){
		$('#kelompokpemeriksaanradiologiform-kode_kelompok').removeAttr('readonly');
	}*/
});

$("#form").docoForm("submit", {
	success : function(data) {
			$('#modal_backdrop').modal('toggle');
			// $("#btn-edit").prop("disabled", true);
        	$("#btn-delete").prop("disabled", true);
		// Check data status
		if (data.metadata.status == 201) {
			// Reset form
			$("#form")[0].reset();

			// Draw table
			table.draw();
		}
		else if (data.metadata.status == 200) {
			// Draw table
			table.draw();
		}
	}
});
</script>