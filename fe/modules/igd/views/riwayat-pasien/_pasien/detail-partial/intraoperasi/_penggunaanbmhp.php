<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-15 11:30:44
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-07 10:53:07
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'intra-penggunaan-bmhp-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'obatalkes_id')->dropDownList([], ['class'=>'select-bmhp']); ?>
    <?=$form->field($model, 'persediaan')->textInput(['class'=>'persediaan','readonly'=>'true']); ?>
    <?=$form->field($model, 'tambahan')->textInput(['class'=>'tambahan']); ?>
    <?=$form->field($model, 'terpakai')->textInput(['class'=>'terpakai']); ?>
    <?=$form->field($model, 'sisa')->textInput(['class'=>'sisa','readonly'=>'true']); ?>
    <?=$form->field($model, 'is_ditagihkan')->checkbox(['class'=>'ditagihkan-check']); ?>
    <?=Html::activeHiddenInput($model, 'inpostoperasi_id', ['class'=>'pegawai-inpost-id'])?>
    <?=Html::activeHiddenInput($model, 'pasienmasukpenunjang_id', ['class'=>'pegawai-pasienmasukpenunjang'])?>
    <?=Html::activeHiddenInput($model, 'is_available')?>
    <?=Html::activeHiddenInput($model, 'msg')?>
    <?=Html::activeHiddenInput($model, 'daftartindakan_id')?>
    <?=Html::activeHiddenInput($model, 'operasi_key')?>
    <?php 
    if($key != ''){
        echo Html::activeHiddenInput($model, 'obatalkes_id');
    }
    ?>
    <?=Html::activeHiddenInput($model, 'obatalkes_nama', ['class'=>'obatalkes-nama'])?>
    <?=Html::activeHiddenInput($model, 'ditagihkan', ['class'=>'ditagihkan-nama'])?>
    <?=Html::hiddenInput('opsi', $opsi, ['class'=>'opsi-obat'])?>
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
    $("#intra-penggunaan-bmhp-form").docoForm("submit",{
        success : function(data) {
            _tablepenggunaanbmhp.draw();
            $('#modal_backdrop').modal('toggle')
        }
    });
    $(document).ready(function(){
        if( $('.opsi-obat').val() !== ''){
            var _opsi = $.parseJSON($('.opsi-obat').val())
            var _options = new Option(_opsi.name, _opsi.id, false, false)
            $('.select-bmhp').append(_options).val(_opsi.id).trigger('change').prop('disabled', true)
        }
        $('.pegawai-inpost-id').val($('.inpostoperasi-id').val())
        $('.pegawai-pasienmasukpenunjang').val($('.pasienmasukpenunjang-id').val())
        $('.select-bmhp').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '/igd/riwayat-pasien/get-bmhp',
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
            templateSelection: function (data, container) {
                $(data.element).attr('data-qty', data.qty_tersedia);
                return data.text;
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });
        $('.select-bmhp').on('change', function(){
            $('.persediaan').val( parseInt($('.select-bmhp').select2('data')[0].element['attributes'][1].value) )
            $('.sisa').val( parseInt($('.select-bmhp').select2('data')[0].element['attributes'][1].value) )
            $('.obatalkes-nama').val(  $('.select-bmhp').select2('data')[0].text )
        })
        $('.ditagihkan-check').on('click', function(){
            if($(this).is(':checked')){
                $('.ditagihkan-nama').val('✓')
            }else{
                $('.ditagihkan-nama').val('')
            }
        })
        $('.terpakai').on('keyup', function(){
            var _persediaan = parseInt( $('.persediaan').val() )
            var _tambahan = ($('.tambahan').val() !== '') ? parseInt( $('.tambahan').val() ) : 0
            var _terpakai = $(this).val()
            var _sisa = _persediaan;
            if(_terpakai !== ''){
                _terpakai = parseInt( _terpakai )
                _sisa = (_persediaan + _tambahan) - _terpakai
                if(_sisa < 0){
                    _sisa = 0;
                }
            }
            $('.sisa').val( _sisa )
        })
        $('.tambahan').on('keyup', function(){
            var _persediaan = parseInt( $('.persediaan').val() )
            var _terpakai = ($('.terpakai').val() !== '') ? parseInt( $('.terpakai').val() ) : 0;
            var _tambahan = $(this).val()
            var _sisa = _persediaan
            if(_tambahan !== ''){
                _tambahan = parseInt( _tambahan )
                _sisa = (_persediaan + _tambahan) - _terpakai
                if(_sisa < 0){
                    _sisa = 0;
                }
            }
            $('.sisa').val( _sisa )
        })
    })
</script>