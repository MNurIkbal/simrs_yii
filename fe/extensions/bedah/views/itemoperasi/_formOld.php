<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-13 11:03:47
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-07 10:29:22
 */

use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php
$dis = false;
$params = ['class' => 'cyto-check'];
if ($key != '') {
    $dis = true;
    $params = ['class' => 'cyto-check', 'disabled' => 'disabled'];
}
$form = ActiveForm::begin([
    'id' => 'intra-item-operasi-form',
    'type' => ActiveForm::TYPE_VERTICAL,
    'formConfig' => [
        'labelSpan' => 3, 
        'deviceSize' => ActiveForm::SIZE_SMALL
    ],
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <?= $form->field($model, 'golonganoperasi_id')
             ->dropDownList([], [ 
                'prompt' => '--Pilih--', 
                'class' => 'select2 select-golongan'
            ]); ?>
    <?= $form->field($model, 'daftartindakan_id')
                ->dropDownList([], [
                    'id' => 'operasi-id', 
                    'class' => 'select2 select-tindakan', 
                    'prompt' => '--Pilih--'
                ]); ?>
    <?= $form->field($model, 'kegiatanoperasi_id')
             ->dropDownList([], [ 
                'prompt' => '--Pilih--', 
                'class' => 'select2 select-kegiatan'
            ]); ?>

    <?= $form->field($model, 'pegawai_id')->dropDownList([], ['id' => 'pegawai-id', 'prompt' => '--Pilih Dokter--']); ?>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'is_cyto', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label text-bold',
                    'wrapper' => 'col-md-12'
                ]
            ])->checkbox($params); ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'penyulit', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label text-bold',
                    'wrapper' => 'col-md-12'
                ]
            ])->checkbox(['disabled'  => $dis]); ?>
        </div>
    </div>
    <?php
    if ($key != '') {
        echo Html::activeHiddenInput($model, 'is_cyto');
    }
    ?>
    <?= Html::activeHiddenInput($model, 'pegawai_nama', ['id' => 'pegawai-nama']) ?>
    <?= Html::activeHiddenInput($model, 'operasi_nama', ['id' => 'operasi-nama']) ?>
    <?= Html::activeHiddenInput($model, 'daftartindakan_nama', ['id' => 'daftartindakan-nama']) ?>
    <div class="form-group">
        <?= Html::activeHiddenInput($model, 'operasi_id', ['id' => 'daftartindakan-id']) ?>
    </div>
    <?= Html::activeHiddenInput($model, 'golonganoperasi_nama', ['id' => 'golonganoperasi-nama']) ?>
    <?= Html::activeHiddenInput($model, 'daftartindakan_nama', ['class' => 'daftartindakan-nama']) ?>
    <?= Html::activeHiddenInput($model, 'jenisanastesi_nama', ['class' => 'jenisanastesi-nama']) ?>
    <?= Html::activeHiddenInput($model, 'jenis_luka_nama', ['class' => 'jenis-luka-nama']) ?>
    <?= Html::activeHiddenInput($model, 'tarif_satuan', ['class' => 'tarif-satuan']) ?>
    <?= Html::activeHiddenInput($model, 'tarif_tindakan', ['class' => 'tarif-tindakan']) ?>
    <?= Html::activeHiddenInput($model, 'tarif_cyto', ['class' => 'tarif-cyto']) ?>
    <?= Html::activeHiddenInput($model, 'inpostoperasi_id', ['class' => 'pegawai-inpost-id']) ?>
    <?= Html::activeHiddenInput($model, 'cyto', ['class' => 'cyto-txt']) ?>
    <?= Html::activeHiddenInput($model, 'pasienmasukpenunjang_id', ['class' => 'pegawai-pasienmasukpenunjang']) ?>
    <?= Html::activeHiddenInput($model, 'default') ?>
    <?= Html::hiddenInput('opsi_operasi', $opsi['daftartindakan'], ['class' => 'opsi-operasi']) ?>
    <?= Html::activeHiddenInput($model, 'kegiatan_id', ['id' => 'kegiatan-id']) ?>
    <?= Html::activeHiddenInput($model, 'kegiatanoperasi_nama', ['id' => 'kegiatan-nama']) ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-floppy-o'></i> " . Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal', 'id' => 'btn-submit-tindakan']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'></i> " . Yii::t('fe', 'Kembali'), [
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    var _arrOptions = [];
    $("#btn-submit-tindakan").bind('click', () => {
        $("#pegawai-id").prop('disabled', false)
    })
    $("#intra-item-operasi-form").docoForm("submit", {
        success: function(data) {
            // console.log(data)
            _tableitemoperasi.draw();
            _tablepenggunaanbmhp.draw();
            $('#modal_backdrop').modal('toggle')
        },
        error: () => {
            if (dokterBedah.length == 1) {
                $("#pegawai-id").prop('disabled', true)
            }
        }
    });
    $(document).ready(function() {
        if ($('.opsi-operasi').val() !== '') {
            var _opsi = $.parseJSON($('.opsi-operasi').val())
            var _options = new Option(_opsi.name, _opsi.id, false, false);
            $('.select-tindakan').append(_options).val(_opsi.id).trigger('change').prop('disabled', true)
        }
        var _persencyto = false;
        var _penjamin = $('.penjamin-id').val()
        var _kelaspelayanan = $('.kelaspelayanan-id').val()
        
        $('.pegawai-inpost-id').val($('.inpostoperasi-id').val())
        $('.pegawai-pasienmasukpenunjang').val($('.pasienmasukpenunjang-id').val())

        $('.select-golongan').select2InfinityScroll({
            url: '/bedah/informasi-pasien-operasi/golongan',
        });

        $('.select-tindakan').select2InfinityScroll({
            url: '/bedah/informasi-pasien-operasi/tindakan-operasi',
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                        kelaspelayanan_id: kelasPelayanan,
                        penjamin_id: penjaminId,
                        golonganoperasi_id: $('.select-golongan').select2('data')[0].id,
                    }
                }
            }
        });

        $('.select-kegiatan').select2InfinityScroll({
            url: '/bedah/informasi-pasien-operasi/kegiatan',
            callbackData : (param) => {
                return {
                    payload: {
                        ...param,
                        golonganoperasi_id: $('.select-golongan').select2('data')[0].id,
                        daftartindakan_id: $('.select-tindakan').select2('data')[0].id,
                    }
                }
            }
        });

        $('.select-kegiatan').on('select2:select', function(){
            const data = $('.select-kegiatan').select2('data')[0];
            $('#kegiatan-id').val(data.id);
            $('#kegiatan-nama').val(data.text);
            $('#daftartindakan-id').val(data.operasi_id)
        });

        $('.select-golongan').on('select2:select', function(){
            const data = $('.select-golongan').select2('data')[0];
            $('.select-tindakan').val(null).trigger('change');
            $('.select-kegiatan').val(null).trigger('change');
        });

        $('.select-tindakan').on('select2:select', function() {
            var _tarifcyto = 0
            const data = $('#operasi-id').select2('data')[0]
            if (_persencyto) {
                _tarifcyto = (parseInt(data.persencyto_tindakan) * parseInt(data.harga_tariftindakan)) / 100;
            }
            $('#operasi-nama').val(data.text)
            $('.tarif-satuan').val(data.harga_tariftindakan)
            $('.tarif-tindakan').val(data.harga_tariftindakan)
            $('.tarif-cyto').val(_tarifcyto)
            $('.daftartindakan-nama').val(data.daftartindakan_nama)
            $('#jenisoperasi-value').text(data.daftartindakan_nama)
            $('#golonganoperasi-id').val(data.golonganoperasi_id)
            $('#golonganoperasi-nama').val(data.golonganoperasi_nama)
            $('#klasifikasi-value').text(data.golonganoperasi_nama)
        })
        $('.golonganoperasi-id').on('change', function() {
            $('.golonganoperasi-nama').val($('.golonganoperasi-id').select2('data')[0].text)
            $('.operasi-id').val(_arrOptions[$(this).val()])
        })
        $('.select-jenisanastesi').on('change', function() {
            $('.jenisanastesi-nama').val($('.select-jenisanastesi').select2('data')[0].text)
        })
        $('.select-jenisluka').on('change', function() {
            $('.jenis-luka-nama').val($('.select-jenisluka').select2('data')[0].text)
        })
        $('.cyto-check').on('click', function() {
            if ($(this).is(':checked')) {
                _persencyto = true;
            } else {
                _persencyto = false;
            }

            if (_persencyto) {
                const data = $('#operasi-id').select2('data')[0]
                var _tarifcyto = (parseInt(data.persencyto_tindakan) * parseInt(data.harga_tariftindakan)) / 100;
                $('.tarif-cyto').val(_tarifcyto)
                $('.cyto-txt').val('✓')
            } else {
                $('.tarif-cyto').val(0)
                $('.cyto-txt').val('-')
            }
        })
        var dataDokter = []
        dokterBedah.map((dokter) => {
            dataDokter.push({
                id: dokter.pegawai_id,
                text: dokter.pegawai_nama
            })
        })
        $("#pegawai-id").select2({
            data: dataDokter
        })
        $("#pegawai-id").on('change', () => {
            $("#pegawai-nama").val($("#pegawai-id").select2('data')[0].text)
        })
        $("#pegawai-id").val(dataDokter[0].id).trigger('change')
        if (dataDokter.length == 1) {
            $("#pegawai-id").prop('disabled', true)
        }
    })
</script>