<?php

/*
* @Author: Sunarko / Master Kondisi Keluar
* @Date:   2018-07-30 13:22:24
* @Last Modified by:  
* @Last Modified time: 
*/

    use yii\widgets\ActiveForm;
    use yii\helpers\Html;

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
        'id' => 'kondisikeluar-form', 
        'options' => [
                'class' => 'form-horizontal', 
                'enableAjaxValidation' => true,
                'role' => 'form'
            ],
        ]); 
    ?>
    <div class="form-group">
        <label for="kondisikeluar_kode" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Kode'); ?><b style="color:red;"> * </b>
        </label>
        <div class="col-lg-6">
            <?=$form->field($model, 'kondisikeluar_kode')
                ->textInput([
                    'class' => 'form-control input-sm'
                ])->label(false); 
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="carakeluar_nama" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Cara Keluar'); ?><b style="color:red;"> * </b>
        </label>
        <div class="col-lg-6">
            <?=
                $form->field($model, 'carakeluar_id')->dropDownList(
                $carakeluar_data,[
                    'prompt' => \Yii::t('fe', '--Pilih--'), 
                    'class' => 'form-control select2',
                ]
                )->label(false);
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="kondisikeluar_nama" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Kondisi Keluar'); ?><b style="color:red;"> * </b>
        </label>
        <div class="col-lg-6">
            <?=$form->field($model, 'kondisikeluar_nama')
                ->textInput([
                    'class' => 'form-control input-sm'
                ])->label(false); 
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="kondisikeluar_namalain" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Nama Lainnya'); ?>
        </label>
        <div class="col-lg-6">
            <?=$form->field($model, 'kondisikeluar_namalain')
                ->textInput([
                    'class' => 'form-control input-sm'
                ])->label(false); 
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="is_active" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Status'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($model, 'is_active')->checkbox()->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="catatan" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Catatan'); ?>
        </label>
        <div class="col-lg-6">
            <?= $form->field($model, 'catatan')->textarea(['rows' => '4'],['class' => 'form-control'])->label(false); ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
            <?= Html::submitButton('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), [
                'class' => 'btn bg-teal'
            ]) ?>
            <!-- <?= Html::resetButton('<b><i class="fa fa-repeat"></i></b>'.Yii::t('fe', ' Ulang'), [
                'class' => 'btn btn-aqua',
                'id' => 'data-reset'
            ]) ?> -->
            <?= Html::button('<b><i class="fa fa-arrow-left"></i></b>'.Yii::t('fe', ' Kembali'),[
                'class' => 'btn bg-slate',
                'data-dismiss' => 'modal'
            ]) ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $('#kondisikeluar-form').docoForm('submit',{
        success : function(data) {
            this.formInput[0].reset();
            $('#modal_backdrop').modal('hide');
            table.draw();
        }
    });
    
    $('#data-reset').on("click", function(){
        var datanya = $('#carakeluar_id').val();
        console.log(datanya);
        $('.select2').val('').trigger('change');
    });

</script>
