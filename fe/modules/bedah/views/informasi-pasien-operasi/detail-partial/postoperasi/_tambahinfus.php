<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-21 15:16:18
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-12 16:57:22
 */

use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;
use yii\helpers\Html;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'post-tambah-infus-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'jeniscairan_id')->dropdownList([], ['class'=>'select2 select-cairan']); ?>
    <?=$form->field($model, 'tgl_pemasangan')->widget(DateTimePicker::classname(), [
            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
            'options' => ['placeholder' => '', 'readonly'=>true],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'dd M yyyy hh:ii',
            ]
        ]);?>
    <?=$form->field($model, 'jumlah_tetes')->textInput(); ?>
    <?=Html::activeHiddenInput($model, 'inpostoperasi_id', ['class'=>'pegawai-inpost-id'])?>
    <?=Html::activeHiddenInput($model, 'pasienmasukpenunjang_id', ['class'=>'pegawai-pasienmasukpenunjang'])?>
    <?=Html::activeHiddenInput($model, 'jeniscairan_nama', ['class'=>'jenis-cairan-nama'])?>
</div>
<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-floppy-o'> ". Yii::t('fe', 'Simpan')."</i>", ['class' => 'btn bg-teal']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'> ". Yii::t('fe', 'Kembali')."</i>",[
                        'class' => 'btn bg-slate',
                        'data-dismiss' => 'modal'
                        ]); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $("#post-tambah-infus-form").docoForm("submit",{
        success : function(data) {
            _tablepemasanganinfus.draw();
            $('#modal_backdrop').modal('toggle')
        }
    });
    $(document).ready(function(){
        $('.pegawai-inpost-id').val($('.inpostoperasi-id').val())
        $('.pegawai-pasienmasukpenunjang').val($('.pasienmasukpenunjang-id').val())
        $('.select-cairan').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '/bedah/informasi-pasien-operasi/get-jenis-alat',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });
        $('.select-cairan').on('change', function(){
            $('.jenis-cairan-nama').val( $('.select-cairan').select2('data')[0].text )
        })
    })
</script>