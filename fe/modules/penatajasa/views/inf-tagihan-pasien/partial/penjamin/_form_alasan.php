<?php 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;

use app\components\DocoHelpers;

?>
<div class="modal-header bg-inverse">
    <h4 class="modal-title"><?= $title ?></h4>
</div>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'penjamin-batal-form', 
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]); 
    ?>
    <?=Html::activeHiddenInput($model, 'pendaftaranpenjamin_id')?>
    <?=$form->field($model, 'alasan')->textArea()?>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-info btn-labeled btn-xs" id="btn-delete-penjamin"><b><i class="fa fa-save fa-xs"></i></b> Simpan</button>
    <button type="button" class="btn btn-info btn-labeled btn-xs" data-dismiss="modal"><b><i class="fa fa-close fa-xs"></i></b> Batal</button>
    <?php ActiveForm::end() ?>
</div>
<script type="text/javascript">
    $('#btn-delete-penjamin').on('click', function(e){
        e.preventDefault();
        var _form = $('#penjamin-batal-form');
        $(this).docoForm('click', {
            data: _form.serializeArray(),
            url: _form.attr('action'),
            success: function(){
                $('#modal_backdrop').modal('toggle');
                // tblPenjamin.ajax.url('get-list-penjamin?pendaftaran_id='+ pendaftaran_id +'&get_session=true').load();
                penjamin.draw()
            }
        })
    });
</script>