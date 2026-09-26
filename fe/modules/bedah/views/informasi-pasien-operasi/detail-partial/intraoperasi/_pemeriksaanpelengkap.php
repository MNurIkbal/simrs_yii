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
    'type' => ActiveForm::TYPE_VERTICAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">

    <?=$form->field($model, 'instalasi_id')->dropDownList($listInstalasi, [
        'class' => 'select2', 
        'id' => 'instalasi-pelengkap',
        'disabled' => true,
        'prompt' => ''
    ]); ?>

    <?=Html::activeHiddenInput($model, 'daftartindakan_id',[
        'id' => 'daftartindakan_id'
    ])?>

    <?=$form->field($model, 'ruangan_id')->dropDownList($listRuangan, [
        'class' => 'select2',
        'id' => 'ruangan-pelengkap', 
        'prompt' => '-- Pilih --'
    ]); ?>

    <?=$form->field($model, 'tariftindakan_id')->dropDownList($options, [
        'class' => 'select2 select-tindakan', 
        'id' => 'tindakan-pelengkap',
        'disabled' => true,
        'prompt' => '-- Pilih --'
    ]); ?>

    <?=$form->field($model, 'nama_jaringan')->textInput(); ?>

    <?=$form->field($model, 'qty')->textInput(); ?>

    <?= $form->field($model, 'is_cyto')->checkbox([
        'id' => 'is_cyto',
        'class' => 'styled',
    ])->label(Yii::t('fe', 'Cyto')); ?>

    <?=Html::activeHiddenInput($model, 'inpostoperasi_id', [
        'class' => 'pegawai-inpost-id'
    ])?>

    <?=Html::activeHiddenInput($model, 'pasienmasukpenunjang_id', [
        'class' => 'pegawai-pasienmasukpenunjang'
    ])?>

    <?=Html::activeHiddenInput($model, 'daftartindakan_nama', [
        'class' => 'daftartindakan-nama'
    ])?>

    <?=Html::activeHiddenInput($model, 'tarif_satuan', [
        'class' => 'tarif-satuan'
    ])?>

    <?=Html::activeHiddenInput($model, 'tarif_tindakan', [
        'class' => 'tarif-tindakan'
    ])?>

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
        var _regisId = $('#pendaftaran_id').val()
        var _ruanganId = null;
        var _arr = {};
        $('.pegawai-inpost-id').val($('.inpostoperasi-id').val())
        $('.pegawai-pasienmasukpenunjang').val($('.pasienmasukpenunjang-id').val())

        $('.select-tindakan').on('change', function(){
            var _selected = $('.select-tindakan').select2('data')[0];
            var _valSeleted = typeof _selected.datavalue != 'undefined' ? _selected.datavalue : {};
            $('.daftartindakan-nama').val( _selected.text )
            $('#daftartindakan_id').val( _valSeleted.daftartindakan_id )
            $('.tarif-satuan').val( parseFloat(_valSeleted.harga_tariftindakan))
            $('.tarif-tindakan').val( parseFloat(_valSeleted.harga_tariftindakan) )
        })

        $('#ruangan-pelengkap').on('change', function () {
            _ruanganId = $(this).val()
            $('.select-tindakan').val('').trigger('change')
            if (_ruanganId) {
                $('#tindakan-pelengkap').prop('disabled', false);
            } else {
                $('#tindakan-pelengkap').val('').trigger('change');
                $('#tindakan-pelengkap').prop('disabled', true);
            }
        });

        $('.select-tindakan').docoPaginationSelec2({
            _api: baseUrl+"bedah/informasi-pasien-operasi/get-tindakan-tarif",
            placeholder: "— Pilih —",
            minimumInputLength: 0,
            ajax : {
                dataType: 'json',
                quietMillis: 250,
                data: function(params) { 
                    params.id = _regisId;
                    return {
                        kelas_pelayanan: _kelaspelayanan,
                        id:_regisId,
                        ruangan_id:_ruanganId,
                        q:params.term, 
                        page:params.page || 1
                    };
                },
            }
        })

    })
</script>