<?php
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\Select2;
use yii\web\JsExpression;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'ajax-form',
    'type' => ActiveForm::TYPE_VERTICAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'kegiatanoperasi_id')->dropDownList($kegiatanoperasi, [
    	'placeholder' => $model->getAttributeLabel('kegiatanoperasi_id'),
    	'class' => 'form-control input-sm selectKegiatan select2',
    	'prompt' => \Yii::t('fe', $model->getAttributeLabel('kegiatanoperasi_id')),
    ]); ?>
    <?=$form->field($model, 'golonganoperasi_id')->dropDownList($golonganoperasi, [
    	'placeholder' => $model->getAttributeLabel('golonganoperasi_id'),
    	'class' => 'form-control input-sm selectGolongan select2',
    	'prompt' => \Yii::t('fe', $model->getAttributeLabel('golonganoperasi_id'))
    ]); ?>
    <?=$form->field($model, 'daftartindakan_id')->dropDownList($daftartindakan, [
    	'placeholder' => $model->getAttributeLabel('daftartindakan_id'),
    	'class' => 'form-control input-sm selectOperasi select2',
    	'prompt' => \Yii::t('fe', $model->getAttributeLabel('daftartindakan_id'))
    ]); ?>
    <?=$form->field($model, 'operasi_kode')->textInput([
    	'placeholder' => $model->getAttributeLabel('operasi_kode'),
    	'class' => 'form-control input-sm'
    ]); ?>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
            $('#modal_backdrop').modal('toggle');
            table.draw();
        }
    });

    $(document).ready(function(){
    	$(".selectKegiatan").select2({
	        placeholder: "Nama Kegiatan Operasi",
	        // minimumInputLength: 3,
	        ajax: {
	            url: "/master/end-point/get-data-kegiatan-operasi?assign_id=true",
	            dataType: "json",
	            quietMillis: 250,
	            processResults: function (data) {
	                return {
	                    results: data.result
	                };
	            }
	        },
	        dropdownCssClass: "bigdrop",
	        escapeMarkup: function (m) { return m; },
	    }).on('select2:select', function(e) {
	    	var data = e.params.data;
    		console.log(data);
	    });

	    $(".selectGolongan").select2({
	        placeholder: "Golongan Operasi",
	        // minimumInputLength: 3,
	        ajax: {
	            url: "/master/end-point/get-data-golongan-operasi?assign_id=true",
	            dataType: "json",
	            quietMillis: 250,
	            processResults: function (data) {
	                return {
	                    results: data.result
	                };
	            }
	        },
	        dropdownCssClass: "bigdrop",
	        escapeMarkup: function (m) { return m; },
	    });
	    $(".selectOperasi").select2({
	        placeholder: "Nama Daftar Tindakan",
	        // minimumInputLength: 3,
	        ajax: {
	            url: "/master/end-point/get-daftar-tindakan?assign_id=true&kelompoktindakan_id=<?= $kelompoktindakan_id ?>&using_kode=true",
	            dataType: "json",
	            quietMillis: 250,
	            processResults: function (data) {
	                return {
	                    results: data.result
	                };
	            }
	        },
	        dropdownCssClass: "bigdrop",
	        escapeMarkup: function (m) { return m; },
	    });

	    $(".reset").on("click", function(){
	        resetForm($("#ajax-form"));
	    });

	    function resetForm($form) {
	        $form.find("input:text, input:password, input:file, select, textarea").val("");
	        $form.find("input:radio")
	             .removeAttr("checked").removeAttr("selected");
	        $(".select2").val(null).trigger("change");
	    }
    });
</script>