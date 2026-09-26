<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;

?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Sync Antrean BPJS</h5>
</div>
<div class="modal-body bodyMin">
    <div class="col-md-12">
      <div class="row">
        <?php
        $form = ActiveForm::begin([
            'id' => 'sync-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
            'options' => [
                'role' => 'form',
            ]
        ]);
        ?>
        
        <div class='input-group dateRangeSync row'>
            <div class="col-sm-6">
                <div class="input-group">
                    <label for="">Tanggal Awal</label>
                    <input type='text' name="tanggalawal" id='startDateSync' class='form-control startDateSync'/>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="input-group">
                    <label for="">Tanggal Akhir</label>
                    <input type='text' name="tanggalakhir" id='endDateSync' class='form-control endDateSync'/><input type='text' style='display:none' class='targetDateSync' col-index=2/>
                </div>
            </div>
            <!-- <label>Tanggal Awal :</label>
            <input type='text' name="tanggalawal" id='startDateSync' class='form-control startDateSync'/> -->
            <!-- <span class='input-group-addon' style='border:0'></span>
            <label>Tanggal Akhir :</label>
            <input type='text' name="tanggalakhir" id='endDateSync' class='form-control endDateSync'/><input type='text' style='display:none' class='targetDateSync' col-index=2/> -->
        </div>
        
        <button type="button" class="btn btn-success btn-xs btn-labeled" id="btn-sync"><b><i class="fa fa-refresh"></i></b> <?= Yii::t('fe', 'Sync') ?></button>
        <?php ActiveForm::end(); ?>
      </div>
    </div>
</div>
<div class="modal-footer"></div>

<script type="text/javascript">
   $(document).ready(function() {

    $("#btn-sync").on('click',function(e){
        if($('#startDateSync').val() =='' || $('#endDateSync').val() == ''){
            $('.dateRangeSync').addClass('has-error');
        }else{
            $('.dateRangeSync').removeClass('has-error');
            $('.input-group').removeClass('has-error');
            $('span.help-block.error').remove();
            var startDateSync = $('#startDateSync').val();
            var endDateSync = $('#endDateSync').val();
            $(this).docoForm("click", {
                url: "/pendaftaran/informasi-antrean-bpjs/sync",
                data: {
                    tanggalawal: startDateSync,
                    tanggalakhir: endDateSync
                },
                skipConfirm:true,
                success: function(res){
                    $('#modal_backdrop').modal('hide');
                    table.draw();
                }
            })
        }
    });
    $('.startDateSync').AnyTime_noPicker().AnyTime_picker({
        format: '%e-%b-%Y',
    });
    $('#AnyTime--startDateSync').appendTo('div#modal_backdrop');
    $('.endDateSync').AnyTime_noPicker().AnyTime_picker({
        format: '%e-%b-%Y',
    });
    $('#AnyTime--endDateSync').appendTo('div#modal_backdrop');
   });
</script>