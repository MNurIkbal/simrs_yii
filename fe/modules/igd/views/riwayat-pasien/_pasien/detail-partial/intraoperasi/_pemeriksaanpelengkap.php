<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-14 10:32:08
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-29 14:41:14
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'intra-pemeriksaan-pelengkap-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'daftartindakan_id')->dropDownList($options, ['class'=>'select2 select-tindakan', 'prompt'=>'']); ?>
    <?=$form->field($model, 'nama_jaringan')->textInput(); ?>
    <?=$form->field($model, 'qty')->textInput(); ?>
    <?=Html::activeHiddenInput($model, 'inpostoperasi_id', ['class'=>'pegawai-inpost-id'])?>
    <?=Html::activeHiddenInput($model, 'pasienmasukpenunjang_id', ['class'=>'pegawai-pasienmasukpenunjang'])?>
    <?=Html::activeHiddenInput($model, 'daftartindakan_nama', ['class'=>'daftartindakan-nama'])?>
    <?=Html::activeHiddenInput($model, 'tarif_satuan', ['class'=>'tarif-satuan'])?>
    <?=Html::activeHiddenInput($model, 'tarif_tindakan', ['class'=>'tarif-tindakan'])?>
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
    $("#intra-pemeriksaan-pelengkap-form").docoForm("submit",{
        success : function(data) {
            _tablepemeriksaanpelengkap.draw();
            $('#modal_backdrop').modal('toggle')
        }
    });
    $(document).ready(function(){
        var _penjamin = $('.penjamin-id').val()
        var _kelaspelayanan = $('.kelaspelayanan-id').val()
        var _arr = {};
        $('.pegawai-inpost-id').val($('.inpostoperasi-id').val())
        $('.pegawai-pasienmasukpenunjang').val($('.pasienmasukpenunjang-id').val())
        $('.select-tindakan').on('change', function(){
            $('.daftartindakan-nama').val( $('.select-tindakan').select2('data')[0].text )
            $('.tarif-satuan').val( _arr[$('.select-tindakan').val()].harga_tariftindakan )
            $('.tarif-tindakan').val( _arr[$('.select-tindakan').val()].harga_tariftindakan )
        })
        $('.select-tindakan').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '/igd/riwayat-pasien/get-tindakan-tarif?penjamin='+_penjamin+'&kelaspelayanan='+_kelaspelayanan,
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  _arr = {};
                  $.each(data.result, function(k, v){
                    _arr[v.id] = {harga_tariftindakan: v.harga_tariftindakan};
                  })
                  return {
                    results: data.result
                  };
                }
            },
            templateSelection: function (data, container) {
                $(data.element).attr('datatarif_tindakan', data.harga_tariftindakan);
                return data.text;
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });
    })
</script>