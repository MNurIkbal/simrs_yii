<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-12 16:00:39
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-19 10:11:42
 */
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'gol_umurlab_nama', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('gol_umurlab_nama'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'gol_umurlab_namalainnya', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('gol_umurlab_namalainnya'),'class' => 'form-control input-sm']); ?>

    <div class="row">
        <label class="text-left control-label col-sm-3"><?=Yii::t('fe', 'Umur minimal')?> <font color="red">*</font> </label>
        <div class="col-sm-3">
            <?= $form->field($model, 'tahun_minimal')->dropDownList($arrTahun['tahun'], [
                'class' => 'form-control input-sm', 
                'prompt' => 'Tahun',
                'value' => $tahunMin
            ])->label(\Yii::t('fe', 'Tahun')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'bulan_minimal')->dropDownList($arrTahun['bulan'], [
                'class' => 'form-control input-sm', 
                'prompt' => 'Bulan',
                'value' => $bulanMin
            ])->label(\Yii::t('fe', 'Bulan')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'hari_minimal')->dropDownList($arrTahun['hari'], [
                'class' => 'form-control input-sm', 
                'prompt' => 'Hari',
                'value' => $hariMin
            ])->label(\Yii::t('fe', 'Hari')) ?>
        </div>
        <div class="row">
            <div class="col-md-9 col-md-offset-3">
                <div id="error_GolonganUmurLabFormgol_umurlab_minimal"></div>
            </div>
        </div>
        
    </div>

    <div class="row">
        <label class="text-left control-label col-sm-3"><?=Yii::t('fe', 'Umur maksimal')?> <font color="red">*</font> </label>
        <div class="col-sm-3">
            <?= $form->field($model, 'tahun_maksimal')->dropDownList($arrTahun['tahun'], [
                'class' => 'form-control input-sm', 
                'prompt' => 'Tahun',
                'value' => $tahunMax
            ])->label(\Yii::t('fe', 'Tahun')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'bulan_maksimal')->dropDownList($arrTahun['bulan'], [
                'class' => 'form-control input-sm', 
                'prompt' => 'Bulan',
                'value' => $bulanMax
            ])->label(\Yii::t('fe', 'Bulan')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'hari_maksimal')->dropDownList($arrTahun['hari'], [
                'class' => 'form-control input-sm', 
                'prompt' => 'Hari',
                'value' => $hariMax
            ])->label(\Yii::t('fe', 'Hari')) ?>
        </div>
        <div class="row">
            <div class="col-md-9 col-md-offset-3">
                <div id="error_GolonganUmurLabFormgol_umurlab_maksimal"></div>
            </div>
        </div>
    </div>
    <?=$form->field($model, 'is_active', ['labelOptions' => ['class' => 'text-left']])->checkbox(); ?>
    <hr>
    <div class="modal-footer" style="padding:0px !important;">
        <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
                'class' => 'btn btn-info btn-labeled btn-xs',
                'id' => 'btn-submit'
            ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'data-dismiss' => 'modal'
            ]); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $('#btn-submit').on('click', function (event) {
        var _form = $('#ajax-form');
        $(this).docoForm("click", {
            url : _form.attr('action'),
            data : _form.serializeArray(),
            success : function(data) {
                $('.data-reset').click()
                if (data.status == 201)
                    this.formInput[0].reset();
                $('#modal_backdrop').modal('toggle');
                table.draw();
            }
        });
    });
</script>