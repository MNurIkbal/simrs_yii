<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin([
    'id' => 'form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableClientValidation' => false,
    'formConfig' => [
        'labelSpan' => 4,
        'deviceSize' => ActiveForm::SIZE_SMALL
    ],
    'options' => [
        'skip-confirm' => true
    ]
]) ?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body">
    <div class="form-group">
        <input
            type="hidden"
            id="konfigrakform-obatalkes_id"
            class="form-control input-sm"
            name="KonfigRakForm[obatalkes_id]"
            value="<?= $model->obatalkes_id ?>"
            readonly="true">

        <?= $form->field($model, 'obatalkes_nama', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-8'
                ]
            ])->textInput([
                'class' => 'form-control input-sm',
                'readonly' => true
            ]) ?>
    </div>

    <div class="form-group">
        <?= $form->field($model, 'min_stok', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-3'
                ],
                'addon' => [
                    'append' => [
                        'content' => $model->satuan_kecil
                    ]
                ]
            ])->textInput([
                'class' => 'form-control input-sm',
                'tabindex' => 1,
                'autofocus' => true,
                'id' => 'min_stok'
            ]) ?>
    </div>

    <div class="form-group">
        <?= $form->field($model, 'max_stok', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-3'
                ],
                'addon' => [
                    'append' => [
                        'content' => $model->satuan_kecil
                    ]
                ]
            ])->textInput([
                'class' => 'form-control input-sm',
                'tabindex' => 2,
                'id' => 'max_stok'
            ]) ?>
    </div>

    <div class="form-group">
        <?= $form->field($model, 'rakobat_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-6'
                ],
            ])->dropDownList($rakobat_list,[
                'class' => 'form-control select2',
                'prompt' => Yii::t('fe', '— Pilih  —'),
                'id' => 'rakobat_list',
            ])->label(Yii::t('fe', 'Rak / Locator')); ?>
    </div>
</div>
<hr>

<div class="modal-footer">
    <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
        'class' => 'btn btn-info btn-labeled btn-xs',
        'data-dismiss' => 'modal'
    ]); ?>
    <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
        'class' => 'btn btn-info btn-labeled btn-xs',
        'id' => 'btn-submit'
    ]) ?>
</div>

<?php ActiveForm::end(); ?>

<script type="text/javascript">
var id = '<?= $id ?>';
$("#btn-submit").on("click", function(event) {
    event.preventDefault();
    var data = $("#form").serializeArray();
    $(this).docoForm('click',{
        url: '/gudang/konfig-obat-ruangan/mapping?id='+id,
        data: data,
        skipConfirm: true,
        success : function(data) {
            // $('.data-reset').click()
            $("#form")[0].reset();
            table.draw();
            $('#modal_backdrop').modal('toggle');
        }
    });
});
</script>