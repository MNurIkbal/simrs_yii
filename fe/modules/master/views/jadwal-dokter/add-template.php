<?php

/**
 * @author Randy Vianda Putra
 * @todo Modal Input speciment laboratorium
 * @copyright 11 Juli 2018 aweutist
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\components\DocoHelpers;

?>

<!-- Modal header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= Yii::t('fe', 'Tambah Notifikasi') ?></h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <div class="row">
        <div class="col-md-10">
            <?php $form = ActiveForm::begin([
                'id' => 'form-save-template',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]) ?>
                <?= 
                    $form->field($model, 'judul_temp', [
                        'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-3',
                            'wrapper' => 'col-md-8'
                        ]])
                        ->textInput([
                            'class' => 'form-control ',
                        ]);
                ?>
                <?= 
                    $form->field($model, 'notifikasi', [
                        'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-3',
                            'wrapper' => 'col-md-8'
                        ]])
                        ->textarea([
                            'class' => 'form-control ',
                        ]);
                ?>
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-8">
                        <?= Html::submitButton("<b><i class='fa fa-floppy-o'></i></b>". Yii::t('fe', 'Simpan'), [
                            'class' => 'btn btn-info btn-xs btn-labeled',
                            'id' => 'btn-add',
                            'data-id' => $id
                        ]) ?>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<!-- Modal footer -->
<div class="modal-footer">
    <div class="pull-left">
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>". Yii::t('fe', 'Kembali'), [
            'class' => 'btn bg-teal btn-xs btn-labeled',
            'id' => 'btn-back',
            'data-id' => $id
        ]) ?>
    </div>
</div>
<!-- Javascript -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#btn-back').on("click", function(e){
            const id = $(this).attr('data-id');
            $('#content').docoLoad({
                url: '/master/jadwal-dokter/pilih-template?id=' + id,
                dataType: 'html',
                success : function(data) {
                }
            });
        });

        $('#form-save-template').submit(function(e) {
            e.preventDefault();
            const dataPost = $(this).serializeArray();
            const id = $(this).attr('data-id');
            $(this).docoForm("submit", {
                url: '/master/jadwal-dokter/save-template',
                data: dataPost,
                success: function(data) {
                    $( '#form-save-template' ).each(function(){
                        this.reset();
                    });
                    $('#content').docoLoad({
                        url: '/master/jadwal-dokter/pilih-template?id=' + id,
                        dataType: 'html',
                    });
                }
            });
        });

    });
</script>
