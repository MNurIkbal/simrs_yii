<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\View;
    use app\components\DocoConstants;
    use app\components\DocoHelpers;
?>

<style type="text/css">
    .modal-dialog {
        width: 35% !important;
        margin: 30px auto;
    }

    button#button-back {
        height: 30px;
        padding-top: 5px;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body">
    <?php $form = ActiveForm::begin([
        'id' => 'ajax-form', 
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'validateOnSubmit' => false, 
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]); 
    ?>
        <div class="form-group">
            <label class="control-label text-left control-label col-sm-3">Nama Obat Alkes</label>
            <div class="col-md-5">
                <p style="margin-top: 9px;"><?= $dataObat['obatalkes_nama'] ?></p>
            </div>
        </div>
        <?=
        $form->field($model, 'zataktif_id', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-3',
                'wrapper' => 'col-md-5'
            ]
        ])->dropDownList($listZatAktifObat, [
            'class' => 'select2 zataktif_id',
            'id'=>'zataktif',
            'prompt' => '— Pilih Zat Aktif —',
        ])->label(Yii::t('fe', 'Zat Aktif'));
    ?>
    <?php ActiveForm::end(); ?>
</div>

<div class="modal-footer">
    <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
        'class' => 'btn btn-info btn-labeled btn-xs',
        'id' => 'btn-submit'
    ]) ?>
</div>

<?php 
$this->registerJs("
    var id = '".$id."';

    $('#btn-submit').on('click', function (event) {
        $(this).docoForm('click', {
            url : '/master/zat-aktif-obat/create?id=' + id,
            method : 'POST',
            data : {
                'obatalkes_id': id,
                'zataktif_id': $('#zataktif').val()
            },
            type: 'json',
            success : function(data) {
                $('#modal_backdrop').modal('toggle');
            
                setInterval(function() {
                    location.reload();
                }, 3000);
            },
            error : function (xhr) {
            }
        });
    });
", View::POS_END, 'b-index');
?>
