<?php
/**
 * @author : Ardi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\web\JsExpression;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;
use kartik\widgets\ActiveForm;
?>
<style>
    .datepicker>div{
        display:block;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <?php $form = ActiveForm::begin([   
            'id' => 'form-margin-khusus', 
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'validateOnSubmit' => false, 
            'formConfig' => [
                'labelSpan' => 4,
                'deviceSize' => ActiveForm::SIZE_MEDIUM
            ],
            'options' => [
                'class' => 'form-horizontal',
                'role' => 'form',
            ]
        ]); 
    ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($mMarginKhusus, 'nama', [
            'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-8'
                ]
            ])->textInput([
                'class' => 'form-control',
            ])->label(Yii::t('fe', 'Nama')); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($mMarginKhusus, 'perda', [
            'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-8'
                ]
            ])->textInput([
                'class' => 'form-control',
            ])->label(Yii::t('fe', 'Perda/SK')); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php $mMarginKhusus->mulai_berlaku = isset($mMarginKhusus->mulai_berlaku) ? date('d-M-Y', strtotime($mMarginKhusus->mulai_berlaku)) :  date('d-M-Y'); ?>
            <?= $form->field($mMarginKhusus, 'mulai_berlaku', [
            'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-8'
                ]
            ])->widget(DatePicker::classname(), [
                'name' => 'date_12',
                'value' => date('Y-m-d'),
                'language' => 'en',
                'pluginOptions' => [
                    'startDate' => new JsExpression("new Date('" . date('m/d/y') . "')"),
                    'minDate' => 0,
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy'
                ],
            ])->label(Yii::t('fe', 'Mulai Berlaku')); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <table id="temp_margin_khusus" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th style="text-align: left;"><?=\Yii::t("fe", "Jenis Obat Alkes");?></th>
                <th><?=\Yii::t("fe", "Margin (%)");?></th>
                <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
    <hr>
    <div class="modal-footer">
        <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
            'class' => 'btn btn-info btn-labeled btn-xs',
            'id' => 'btn-submit-margin-khusus'
        ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
            'class' => 'btn btn-info btn-labeled btn-xs',
            'data-dismiss' => 'modal'
        ]); ?>
    </div>

</div>

<script type="text/javascript">
    jenisobatalkes = `<div><?= Html::dropDownList('MarginKhususDetailForm[0][jenisobat_id]','',$jenis_obat,['class'=>'form-control dropdown-jenisobatalkes','prompt'=>'Pilih'])?><span class="help-block"></span></div>`;

$(function(){
    tableTemp = $('#temp_margin_khusus').DataTable({
        data: {},
        ordering : false,
        paging: false,
        searching: false,
        info: false,
        columns: [
            {data: 'no'},
            {data: 'jenisobatalkes', className : 'text-right'},
            {data: 'margin', className : 'text-right'},
            {data: 'action', className : 'text-center'},
        ],
        fnRowCallback : function (nRow, aData, index) {
            $('td:eq(0)',nRow).html(index + 1);
            return nRow;
        }
    });
    tableTemp.row.add({
        'no':'',
        'jenisobatalkes':jenisobatalkes,
        'margin':'<div><input type="text" name="MarginKhususDetailForm[0][margin]" class="text-right form-control margin" onchange="persenMarginKhusus( this );"><span class="help-block"></span></div>',
        'action':'<button class="btn btn-success addrow-margin-khusus btn-sm"><i class="fa fa-plus"></i></button>'
    }).draw(false);
    $('.dropdown-jenisobatalkes').select2();


    $('#btn-submit-margin-khusus').on('click', function (event) {
        event.preventDefault();
        var dataDetail = tableTemp.$('.dropdown-jenisobatalkes,input').serializeArray();
        var dataHeader = $("#form-margin-khusus").serializeArray();
        var dataHeaderDetail = $.merge( $.merge( [], dataHeader ), dataDetail );
        $(this).docoForm('click', {
            url: $('#form-margin-khusus').attr('action'),
            data: dataHeaderDetail,
            success : function(data) {
                $("#modal_backdrop").modal("toggle");
                tableTemp.draw();
                table.draw();
            }
        });
    });

    $('#temp_margin_khusus tbody').on('change', '.dropdown-jenisobatalkes,.margin', function () {
        $(this).closest('tr').css('background','');
        $(this).closest('div').find('.help-block').html('');
    })
});
</script>