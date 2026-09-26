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
use app\components\DocoConstants;
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
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<style>
    .modal-dialog{
        width:75%;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="col-sm-4">
                <?= $form->field($model, 'daftartindakan_id')->dropDownList([], ['id' => 'operasi-id', 'class' => 'select2 select-tindakan']); ?>
            </div>
            <div class="col-sm-3">
                <div class="form-group required">
                    <div class="row">
                        <label class="control-label has-star col-sm-8">Kegiatan Operasi</label>
                    </div>
                    <div class="row">
                        <label class="label-value col-sm-8" id="jenisoperasi-value">-</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group required">
                    <div class="row">
                        <label class="control-label has-star col-sm-8">Golongan Operasi</label>
                    </div>
                    <div class="row">
                        <label class="label-value col-sm-8" id="klasifikasi-value">-</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            <?= $form->field($model, 'is_cyto', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label text-bold',
                    'wrapper' => 'col-md-2'
                ]
            ])->checkbox($params); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'penyulit', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label text-bold',
                    'wrapper' => 'col-md-2'
                ]
            ])->checkbox(['disabled'  => $dis]); ?>
        </div>
    </div>
    <hr>
    <h2>Tim Operasi</h2>
    
    <div class="row">
        <div class="col-md-12">
            <br>
            <table id="table-tim-operasi-v2" class="table table-striped table-condensed " style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="10" data-key="no">No</th>
                        <th data-key="nama_pegawai"><?=Yii::t('fe', 'Nama pegawai')?></th>
                        <th data-key="posisi_tim_nama"><?=Yii::t('fe', 'Posisi tim operasi')?></th>
                        <th data-key="posisi_tim_nama"><?=Yii::t('fe', 'Prosentase')?></th>
                        <th data-key="pegawai_input"><?=Yii::t('fe', 'Pegawai Input')?></th>
                        <th data-key="hapus"><?=Yii::t('fe', 'Aksi')?></th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
    
    <?= ""; $form->field($model, 'pegawai_id')->dropDownList([], ['id' => 'pegawai-id', 'prompt' => '--Pilih Dokter--']); ?>
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
    <div class="form-group">
        <?= Html::activeHiddenInput($model, 'golonganoperasi_id', ['id' => 'golonganoperasi-id']) ?>
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
    <?= Html::activeHiddenInput($model, 'kegiatanoperasi_id', ['class' => 'kegiatanoperasi_id']) ?>
    <?= Html::activeHiddenInput($model, 'kegiatanoperasi_nama', ['class' => 'kegiatanoperasi_nama']) ?>
    <?= Html::activeHiddenInput($model, 'pegawai_id', ['class' => 'pegawai-id']) ?>
    <?= Html::hiddenInput('ruangan_id', '', ['id'=>'ruangan-id-item'])?>

</div>
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-floppy-o'></i> " . Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal', 'id' => 'btn-submit-tindakan']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'></i> " . Yii::t('fe', 'Kembali'), [
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    var _dokterBedahId = "<?=DocoConstants::TIM_DOKTER_BEDAH;?>";
    var _arrOptions = [];
    var _listTimOperasi = [];
    var tmpDaftartindakanId = $('.select-tindakan').val();
    var ajaxUrlpegawaiOperasi = baseUrl + 'bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cacheoperasi +'&id=' + tmpDaftartindakanId;

    $("#btn-submit-tindakan").bind('click', () => {
        $("#pegawai-id").prop('disabled', false)
    })

    // $("#intra-item-operasi-form").docoForm("submit", {
    //     success: function(data) {
    //         _tableitemoperasi.draw();
    //         _tablepenggunaanbmhp.draw();
    //         $('#modal_backdrop').modal('toggle')
    //     },
    //     error: () => {
    //         if (dokterBedah.length == 1) {
    //             $("#pegawai-id").prop('disabled', true)
    //         }
    //     }
    // });

    $("#btn-submit-tindakan").on("click", function(){
        $().docoForm("click",{
            data : $("#intra-item-operasi-form").serializeArray(),
            url : $("#intra-item-operasi-form").attr("action"),
            success: function(data) {
                _tableitemoperasi.draw();
                _tablepenggunaanbmhp.draw();
                $('#modal_backdrop').modal('toggle')
            },
            error: () => {
                if (dokterBedah.length == 1) {
                    $("#pegawai-id").prop('disabled', true)
                }
            }
        })
    })    

    $(document).ready(function() {
        if ($('.opsi-operasi').val() !== '') {
            var _opsi = $.parseJSON($('.opsi-operasi').val())
            var _options = new Option(_opsi.name, _opsi.id, false, false);
            $('.select-tindakan').append(_options).val(_opsi.id).trigger('change').prop('disabled', true)
        }
        var _persencyto = false;
        var _penjamin = $('.penjamin-id').val()
        var _kelaspelayanan = $('.kelaspelayanan-id').val()
        var _ruanganId = $('.ruangan-id').val()
        $('.pegawai-inpost-id').val($('.inpostoperasi-id').val())
        $('.pegawai-pasienmasukpenunjang').val($('.pasienmasukpenunjang-id').val())
        $('#ruangan-id-item').val($('.ruangan-id').val())
        
        $('.select-tindakan').select2InfinityScroll({
            url: '/bedah/informasi-pasien-operasi/tindakan',
            callbackData: (param) => {
                return {
                    payload: {
                        ...param,
                        kelaspelayanan_id: kelasPelayanan,
                        penjamin_id: penjaminId,
                        ruangan_id : _ruanganId,
                    }
                }
            }
        })
        $('.select-tindakan').on('select2:select', function() {
            var _tarifcyto = 0
            const data = $('#operasi-id').select2('data')[0];
            if (_persencyto) {
                _tarifcyto = (parseInt(data.persencyto_tindakan) * parseInt(data.harga_tariftindakan)) / 100;
            }
            $('#operasi-nama').val(data.text)
            $('.tarif-satuan').val(data.harga_tariftindakan)
            $('.tarif-tindakan').val(data.harga_tariftindakan)
            $('.tarif-cyto').val(_tarifcyto)
            $('.daftartindakan-nama').val(data.daftartindakan_nama)
            $('#jenisoperasi-value').text(data.kegiatanoperasi_nama)
            $('#daftartindakan-id').val(data.operasi_id)
            $('#golonganoperasi-id').val(data.golonganoperasi_id)
            $('#golonganoperasi-nama').val(data.golonganoperasi_nama)
            $('#klasifikasi-value').text(data.golonganoperasi_nama)
            $('.kegiatanoperasi_nama').val(data.kegiatanoperasi_nama);
            $('.kegiatanoperasi_id').val(data.kegiatanoperasi_id);
            tmpDaftartindakanId = data.id;
            $.ajax({
                url: baseUrl + 'bedah/informasi-pasien-operasi/check-cache-item-operasi?cacheName=' + _cacheitemoperasi +'&id=' + tmpDaftartindakanId, 
                success: function(result){
                    ajaxUrlpegawaiOperasi = baseUrl + 'bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cacheoperasi +'&id=' + tmpDaftartindakanId;
                    _tableoperasi.ajax.url(ajaxUrlpegawaiOperasi).load();
                },
                 error: function (data, status, error) {
                    docoHelper.listen = false;
                    var errMsg = 'Tindakan yang sudah diinput, tidak dapat dipilih kembali.';
                    var errTitle = 'Proses Gagal !';
                    docoNotification('error', errTitle, errMsg);
                    $('.select-tindakan').val(null).trigger('change');
                    let _parent = $('[name="IntraItemOperasiForm[daftartindakan_id]"]');
                    _parent.parent('div').find('.help-block').remove();
                }
            });
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

    /** Penyesuaian tabel tim operasi disatukan dengan form tindakan operasi */
    
    _tableoperasi = $('#table-tim-operasi-v2').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: ajaxUrlpegawaiOperasi,
        columns: [{
            title: 'No',
            data: 'rowNum',
            searchable: false,
            orderable: false
        },
        {
            title: 'Nama pegawai',
            data: 'pegawai_nama',
        },
        {
            title: 'Posisi tim',
            data: 'posisi_tim_nama',
        },
        {
            title: 'Prosentase',
            visible: false,
            data: null,
            render: (data) => {
                var prosentase = (data.posisi_tim == ID_DOKTER_OP) ? 100 : data.prosentase;
                return data.slug_posisi == 'dokter-anastesi' ?
                    `<div class="row">
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input name='dynamicProsentase' class="form-control" value="${parseInt(prosentase)}" data-pegawai="${data.pegawai_id}">
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>
                    </div>` : `${parseInt(prosentase)}%`
            }
        },
        {
            title: 'Pegawai Input',
            data: 'pegawai_input',
            visible: false,
        },
        {
            title: 'Aksi',
            data: 'aksi',
        },
        ],
        drawCallback: (settings) => {
            dokterBedah = []
            const dataRes = settings.jqXHR.responseJSON
            dataRes.data.map((employee) => {
                if (employee.posisi_tim == idDokterBedah) {
                    dokterBedah.push(employee)
                }
            })

            _formTim = $("#formTim");
            if(_formTim.length==0 && tmpDaftartindakanId != null){
                $("#table-tim-operasi-v2").find("tbody").prepend(`
                    <tr>
                        <td colspan ="6"> <div id="formTim"> </div> </td>
                    </tr>
                `);
                getTambahTimOperasi();
            }
        }
    });

    function getTambahTimOperasi(){
        $("#formTim").docoLoad({
        url: '/bedah/informasi-pasien-operasi/intra-tambah-pegawai',
        dataType: 'html',
        success : function(data) {
            // $(" .select2 ").select2();
            $('.pickadate').pickadate({
                    format: 'dd mmm, yyyy',
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd',
                });
            var table_komponen;
            }
        });
    }

</script>