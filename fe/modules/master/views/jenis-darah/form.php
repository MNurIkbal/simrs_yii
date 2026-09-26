<?php
// Author : Naufal Ziyad L
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'form', 
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-4 control-label"><?=Yii::t('fe', 'Nama Jenis Darah')?></label>
        <div class="col-lg-7">
            <?= $form->field($model, 'jenisdarah_nama')
                ->textInput(['class' => 'form-control', 'readonly' => isset($id) ? true : false])
                ->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-4 control-label"><?=Yii::t('fe', 'Lama Penyimpanan (Hari)')?></label>
        <div class="col-lg-7">
            <?= $form->field($model, 'lama_penyimpanan')
                ->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-4 control-label"><?=Yii::t('fe', 'Suhu Penyimpanan (&#8451;)')?></label>
        <div class="col-lg-7">
            <?= $form->field($model, 'suhu_penyimpanan')
                ->textInput(['class' => 'form-control suhu_penyimpanan', 'maxlength' => 7])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-4 control-label"><?=Yii::t('fe', 'Harga')?></label>
        <div class="col-lg-7">
            <?= $form->field($model, 'harga')
                ->textInput(['class' => 'form-control doco-number'])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="inputPassword" class="col-lg-4 control-label">Status</label>
        <div class="col-lg-7">
            <?= $form->field($model, 'is_active')->checkbox(['label' => false])->label(false); ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
        <?= Html::submitButton("<i class='fa fa-floppy-o'> Simpan</i>", ['class' => 'btn bg-teal']) ?>
        <?= Html::button("<i class='fa fa-arrow-left'> Kembali</i>",[
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
        ]); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $('#form').docoForm('submit',{
        success : function(data) {
            table.draw();
            $("#modal_backdrop").modal("toggle");
        }
    });
    $(document).on("change",".suhu_penyimpanan", function (e) {
        e.preventDefault();
        docoHelper.convertToDecimal(this, '.', '.' );
    });
</script>