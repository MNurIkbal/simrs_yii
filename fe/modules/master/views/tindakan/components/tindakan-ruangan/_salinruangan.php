<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-27 17:17:40
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-08-07 14:39:25
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;

?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form-tindakan-ruangan',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);
?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <label class="control-label col-sm-2" style="padding: 10px">Salin Dari</label>
            <div class="col-sm-9" style="padding: 10px">
                <b><?=Yii::t('fe', 'Ruangan'). ' '.$ruangan_nama?></b>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <label class="control-label col-sm-2" style="padding: 10px">Salin untuk</label>
            <div class="col-sm-7">
                <?=Html::dropDownlist('ruangan_tujuan','', $ruangan, ['class'=>'form-control select2 select-ruangan-tujuan', 'prompt'=>'Pilih Ruangan'])?>
            </div>
        </div>
    </div>
    <?=Html::hiddenInput('ruangan_asal', $id, ['class'=>'ruangan-asal'])?>
</div>
<div class="modal-footer">
        <button class="btn btn-info btn-xs btn-labeled simpan-salin-btn"><b class="fa fa-md fa-floppy-o"></b> <?=Yii::t('fe','Simpan')?></button>
        <button class="btn btn-warning btn-xs btn-labeled reset-salin-btn" data-dismiss="modal"><b class="fa fa-md fa-arrow-left"></b> <?=Yii::t('fe','Kembali')?></button>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
$(document).on('click', '.simpan-salin-btn', function(){
    var ruangan_asal = $('.ruangan-asal').val()
    var ruangan_tujuan = $('.select-ruangan-tujuan').val()
    if(ruangan_asal == ruangan_tujuan){
        docoNotification('error', 'Terjadi Kesalahan', 'Ruangan asal tidak boleh sama dengan ruangan tujuan')
    }else if(ruangan_tujuan == ''){
        docoNotification('error', 'Terjadi Kesalahan', 'Ruangan tujuan belum terisi')
    }else{
        $().docoForm('click',{
            url: '/master/tindakan/salin-tindakan-ruangan?',
            data: {ruangan_asal: ruangan_asal, ruangan_tujuan: ruangan_tujuan},
            type: 'POST',
            success: function(response){
                console.log(response, 'sukses')
                docoNotification('success', 'Sukses', 'Salin tindakan ruangan berhasil')
            },
            error: function(response){
                console.log(response, 'error')
            }
        })
        
    }
})
$("#ajax-form-tindakan-ruangan").docoForm("submit",{
    success : function(data) {
        if (data.status == 201)
            this.formInput[0].reset();
        // table.draw();
        $("#modal_backdrop").modal('hide');

    },
    error: function(data){
        // $("#modal_backdrop").modal('hide');
    }
});
</script>