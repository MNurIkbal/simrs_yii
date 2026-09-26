<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
use yii\helpers\Html;

?>


<style type="text/css">
textarea {
    resize: none;
}
.datepicker>div{
    display:block;
}
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $titleform ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
        'id' => 'form',
        'enableAjaxValidation'=>false,
        'enableClientValidation'=>false,
        'type' => ActiveForm::TYPE_VERTICAL,
        'formConfig' => [
            'labelSpan' => 3,
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
    ]);
    ?>
    <div class="row">
        <div class="col-md-12">
           <div class="form-group">
                <?= $form->field($model, 'tgl_transaksi', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ]
                ])->widget(DatePicker::classname(), [
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'value' => date('Y-m-d'),
                    'readonly' => true,
                    'language' => 'en',
                    'pluginOptions' => [
                        'endDate' => '0d',
                        'autoclose' => true,
                        'todayBtn' => true,
                        'format' => 'dd-M-yyyy',
                    ]
                ]); ?>
           </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= $form->field($model, 'jasadokter_id',[
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-8'
                    ],
                ])->dropDownList([],[
                    'class' => 'form-control select2',
                    'id' => 'jasadokter_id',
                ])->label($model->attributeLabels()['jasadokter_id']); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= $form->field($model, 'pegawai_id',[
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-8'
                    ],
                ])->dropDownList([],[
                    'class' => 'form-control select2',
                    'id' => 'pegawai_id',
                ])->label($model->attributeLabels()['pegawai_id']); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?=$form->field($model, 'total_jasa',[
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-8'
                    ],
                ])->textInput([
                    'class' => 'form-control total_jasa ',
                ])->label($model->attributeLabels()['total_jasa']); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= $form->field($model, 'deskripsi',[
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-8'
                    ],
                ])->textarea([
                    'class' => 'form-control',
                    'id' => 'deskripsi',
                    'rows' => '6',
                ])->label($model->attributeLabels()['deskripsi']); ?>
            </div>
        </div>
    </div>
<hr>
<div class="modal-footer">
        <?= Html::submitButton('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), [
            'class' => 'btn bg-teal'
        ]) ?>
        <?= Html::button('<b><i class="fa fa-arrow-left"></i></b>'.Yii::t('fe', ' Kembali'),[
            'class' => 'btn bg-slate',
            'data-dismiss' => 'modal'
        ]) ?>
</div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">

$('#form').docoForm('submit',{
        success : function(data) {
            this.formInput[0].reset();
            $('#modal_backdrop').modal('hide');
            table.draw();
        }
});

$(document).ready(function(){
    // infinity scroll Select2 with helper docoHealth.js
    // config = {} : untuk melakukan custom config pada js untuk kebutuhan data di select2 / modifikasi response ajax
    $("#jasadokter_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Pilih Transaksi --',      // custom placeholder (optional) default null
            _api : '/kasir/master-api/get-data-jasa-dokter',   // get data
        }
    )

    $("#pegawai_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Pilih Dokter --',      // custom placeholder (optional) default null
            _api : '/kasir/master-api/get-data-tenaga-medis',   // get data
        }
    )
});
</script>
