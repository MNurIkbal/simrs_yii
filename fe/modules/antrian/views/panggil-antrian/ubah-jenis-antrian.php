<?php

/**
 * @author Randy Vianda Putra
 * @todo ubah jenis antrian
 * @copyright 19 November 2018 aweutist
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\components\DocoHelpers;
use yii\helpers\Url;

?>


<!-- Modal header -->
<div id="content">
    <div class="modal-header bg-inverse">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h5 class="modal-title"><?= Yii::t('fe', 'Ubah Jenis Pengambilan Antrian Pendaftaran') ?></h5>
    </div>

    <?php 
        $form = ActiveForm::begin([
            'id'=>'loket-form',
            'enableAjaxValidation'=>false, 
            'enableClientValidation'=>false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 3, 
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
            'options' => [

            ]
        ]);
    ?>
    <!-- Modal body -->
    <div class="modal-body">
        <div class="row">
            <div class="col-md-8 list-pengambilan-checkbox">
            </div>
        </div>
    </div>

    <!-- Modal footer -->
    <div class="modal-footer">
        <div class="pull-left">
            <?= Html::submitButton("<b><i class='fa fa-edit'></i></b>". Yii::t('fe', 'Ubah'), [
                'class' => 'btn btn-info btn-xs btn-labeled',
                'id' => 'btn-ubah',
            ]) ?>
            <?= Html::button("<b><i class='fa fa-close'></i></b>". Yii::t('fe', 'Batal'), [
                'class' => 'btn btn-danger btn-xs btn-labeled',
                'data-dismiss' => 'modal',
            ]) ?>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>
<script type="text/javascript">
    function cekJenisPengambilan(idx) {
        var atLeastOneIsChecked = $('input[name="LoketForm[konfigantrian_id][]"]:checked').length > 0;
        if (atLeastOneIsChecked) {
            var check_id =  $(idx).parent().attr('id');
            $(".list-pengambilan-checkbox input").attr("disabled", true);
            $("#"+check_id+" input").attr("disabled", false);
        } else {
            $(".list-pengambilan-checkbox input").attr("disabled", false);
        };

    }
</script>
<?php
    $urlJenis = Url::to(['/master/loket/add-item-konfig','id' => $val_konfig]);
    if (!empty($val_konfig)) {
        $decrypted_val_konfig = DocoHelpers::decrypt($val_konfig);
    } else {
        $decrypted_val_konfig = json_encode($val_konfig);
    }
    $this->registerJs("
        $('#loket-form').docoForm('submit',{
            success : function(data) {
                location.reload()
            }
        });
        var val_konfig = ".$decrypted_val_konfig.";
        $.ajax({
            type: 'POST',
            url: '{$urlJenis}',
            data: {
                jenisantrian_id: '{$jenisantrian_id}'
            },
            error: function() {
                console.log('An error has occurred');
            },
            dataType: 'json',
            success: function(data) {
                let input_id = [];
                $('.list-pengambilan-checkbox').html('');
                $.each(data.output, function(i,v){
                    if(jQuery.inArray(v.konfigantrian_id, val_konfig) !== -1) {
                        $('.list-pengambilan-checkbox').append('<div class=\'col-sm-12 data-pengambilan\' id=\'jpa-'+v.kode_antrian+'\'><input type=\'checkbox\' id=\'jpa-'+v.konfigantrian_id+'-'+v.kode_antrian+'\' name=\'LoketForm[konfigantrian_id][]\' onchange=\'cekJenisPengambilan(this)\' value='+v.konfigantrian_id+' checked> '+ v.konfig_name +'</div>');

                        input_id.push('#jpa-'+v.konfigantrian_id+'-'+v.kode_antrian);
                    } else {
                        $('.list-pengambilan-checkbox').append('<div class=\'col-sm-12 data-pengambilan\' id=\'jpa-'+v.kode_antrian+'\'><input type=\'checkbox\' id=\'jpa-'+v.konfigantrian_id+'-'+v.kode_antrian+'\' name=\'LoketForm[konfigantrian_id][]\' onchange=\'cekJenisPengambilan(this)\' value='+v.konfigantrian_id+'> '+ v.konfig_name +'</div>');
                    }
                });

                if (input_id.length > 0) {
                    $.each(input_id, function (key, value) {
                        console.log(value)
                        $(value).trigger('change');
                    });
                }
            },
        });
    ");
?>