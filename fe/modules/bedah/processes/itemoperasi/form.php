<?php

/**
 * @Author: Dede Herdiana
 * @Date:   2022-03-24 11:13:
 * @Last Modified by:   
 * @Last Modified time: 
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
if ($is_edit) {
    $dis = false;
    $params = ['class' => 'cyto-check'];
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
    <h5 class="modal-title"><?= $title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="col-sm-4">
                <?= $form->field($model, 'daftartindakan_id')->dropDownList([], ['id' => 'operasi-id', 'class' => 'select2 select-tindakan']); ?>
            </div>
            <?php if($show_kegiatan_golongan_operasi==1): ?>
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
            <?php endif; ?>
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
    <?= Html::activeHiddenInput($model, 'kegiatanoperasi_nama', ['class' => 'kegiatanoperasi_nama']) ?>
    <?= Html::activeHiddenInput($model, 'kegiatanoperasi_id', ['id' => 'kegiatanoperasi_id']) ?>
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
   
    /**Blok Kondisi Edit */
    $('.btn[data-dismiss="modal"]').addClass('btn-popup-kembali');
    
    var isEdit = '<?=!empty($is_edit) ? $is_edit : false;?>';
    var isChange = false;
    
    var dataTindakan = <?=json_encode($model->attributes);?>; //specific of cache tindakan
    var dataTindakanOrigin = <?=json_encode($data);?>; // all of cache tindakan 
    var dataPegawaiOrigin = <?=json_encode($dataPegawai);?>; // all of cache pegawai
    var timOperasi = JSON.parse('<?= $timOperasi ?>')
    var _cacheNamePegawai = '<?=$cacheNamePegawai?>'; //cachepegwaai
    var _cacheNameTindakan = '<?=$cacheName?>'; //cache tindakan
    var _keyTindakan = '<?=$key?>'; //key tindakan
    var pegawai_input = '<?=$pegawai_input?>'; //key tindakan
    
    if(isEdit){
        $(".select-tindakan").append(new Option(dataTindakan.daftartindakan_nama, dataTindakan.daftartindakan_id));
        $('#jenisoperasi-value').text(dataTindakan.kegiatanoperasi_nama)
        $('#klasifikasi-value').text(dataTindakan.golonganoperasi_nama)

        var tmpDaftartindakanId = $('.select-tindakan').val();
        var ajaxUrlpegawaiOperasi = baseUrl + 'bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cacheoperasi +'&id=' + tmpDaftartindakanId;
        var dataPegawaiTindakan  = dataPegawaiOrigin[tmpDaftartindakanId]; // specific of cache pegawai
        dataPegawaiOrigin = JSON.stringify(dataPegawaiOrigin); // untuk mengamankan data original supaya tidak ikut terhapus ketika unset array temporary

    }
    
    $("#btn-submit-tindakan").bind('click', () => {
        $("#pegawai-id").prop('disabled', false)
    })

    $("#btn-submit-tindakan").on("click", function(){
        let url = $("#intra-item-operasi-form").attr("action")
        if(isEdit){
            let addVar = '&key=' + _keyTindakan
            if($('.select-tindakan').val() != tmpDaftartindakanId){
                addVar = addVar + '&changeTindakan='+true;
            }
            url = url + addVar;
        }
        $().docoForm("click",{
            data : $("#intra-item-operasi-form").serializeArray(),
            url : url,
            success: function(data) {
                $('#modal_backdrop').modal('toggle')
                _tableitemoperasi.draw();
                _tablepenggunaanbmhp.draw();
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
            let newTmpDaftartindakanId = data.id;
            $.ajax({
                url: baseUrl + 'bedah/informasi-pasien-operasi/check-cache-item-operasi?cacheName=' + _cacheitemoperasi +'&id=' + newTmpDaftartindakanId, 
                success: function(result){
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
                    $('#kegiatanoperasi_id').val(data.kegiatanoperasi_id);

                    if(isEdit){
                        tmpDataPegawai = JSON.parse(dataPegawaiOrigin);
                        tmpDataPegawai[newTmpDaftartindakanId] = tmpDataPegawai[tmpDaftartindakanId];
                        tmpDataPegawai[newTmpDaftartindakanId].forEach(function (value, i) {
                            tmpDataPegawai[newTmpDaftartindakanId][i]['daftartindakan_id'] = newTmpDaftartindakanId;
                            tmpDataPegawai[newTmpDaftartindakanId][i]['golonganoperasi_id'] = data.golonganoperasi_id;
                            tmpDataPegawai[newTmpDaftartindakanId][i]['golonganoperasi_nama'] = data.golonganoperasi_nama;
                            tmpDataPegawai[newTmpDaftartindakanId][i]['kegiatanoperasi_id'] = data.kegiatanoperasi_id;
                            tmpDataPegawai[newTmpDaftartindakanId][i]['kegiatanoperasi_nama'] = data.kegiatanoperasi_nama;
                        });

                        delete tmpDataPegawai[tmpDaftartindakanId];
                        dataPayload = [{
                            cacheName : _cacheNamePegawai,
                            cacheData : tmpDataPegawai
                        }];

                        $().docoForm('click', {
                            url: '/bedah/informasi-pasien-operasi/reset-cache',
                            type: "POST",
                            skipConfirm: true,
                            skipSuccessNotif: true,
                            data: {
                                payload : JSON.stringify(dataPayload)
                            },
                            success: function (data) {
                                ajaxUrlpegawaiOperasi = baseUrl + 'bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cacheoperasi +'&id=' + newTmpDaftartindakanId;
                                _tableoperasi.ajax.url(ajaxUrlpegawaiOperasi).load();
                                isChangeData()

                            }
                        })
                    }else{
                        tmpDaftartindakanId = newTmpDaftartindakanId;
                        ajaxUrlpegawaiOperasi = baseUrl + 'bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cacheoperasi +'&id=' + newTmpDaftartindakanId;
                        _tableoperasi.ajax.url(ajaxUrlpegawaiOperasi).load();
                    }
                },
                 error: function (data, status, error) {
                    docoHelper.listen = false;
                    var errMsg = 'Tindakan yang sudah diinput, tidak dapat dipilih kembali.';
                    var errTitle = 'Proses Gagal !';
                    docoNotification('error', errTitle, errMsg);
                    $('.select-tindakan').val(null).trigger('change');
                    let _parent = $('[name="IntraItemOperasiForm[daftartindakan_id]"]');
                    if(isEdit){
                        $(".select-tindakan").find('option').remove().end().append(new Option(dataTindakan.daftartindakan_nama, dataTindakan.daftartindakan_id));

                    }else{
                        _parent.parent('div').find('.help-block').remove();
                    }
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
        if(dataDokter[0] !== undefined){
            $("#pegawai-id").val(dataDokter[0].id).trigger('change')
        }
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
            data: null,
            render: (data) => {
                return isEdit ?
                    `<select class="select-pegawai-edit" id ="pegawai-${data.rowNum - 1}" data-key="${data.rowNum - 1}"><option value="${data.pegawai_id}">${data.pegawai_nama}</option></select>` 
                    : `${data.pegawai_nama}`
            }
        },
        {
            title: 'Posisi tim',
            data: null,
            render: (data) => {
                return isEdit ?
                    `<select class="select-posisi-edit" id ="posisi-${data.rowNum - 1}" data-key="${data.rowNum - 1}"></select>` 
                    : `${data.posisi_tim_nama}`
            }
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
            /**block pegawai operasi */
            $('.select-pegawai-edit').select2({
                placeholder: '',
                ajax: {
                    url: '/bedah/informasi-pasien-operasi/get-pegawai',
                    dataType: 'json',
                    quietMillis: 250,
                    data: function(term, page) {
                        return {
                            q: term,
                            ruangan_id: $('.ruangan-id').val(),
                            page: page
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: data.result
                        };
                    }
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function(m) {
                    return m;
                },
            });
            
            $('.select-pegawai-edit').on('change', function() {
                
                let key = $(this).data("key");
                let pegawai_id = $(this).find('option:selected').val()
                let pegawai_nama = $(this).find('option:selected').text()
                let isValid = validatePegawai(pegawai_id)
                let tmpPegawaiTindakan = dataPegawaiTindakan[key];
                if(isValid && typeof tmpPegawaiTindakan != 'undefined'){
                    let payload = {
                        pegawai_id : pegawai_id,
                        pegawai_nama : pegawai_nama,
                        posisi_tim : tmpPegawaiTindakan.posisi_tim,
                        prosentase : tmpPegawaiTindakan.prosentase,
                        posisi_tim_nama : tmpPegawaiTindakan.posisi_tim_nama,
                        slug_posisi : tmpPegawaiTindakan.slug_posisi,
                        inpostoperasi_id : tmpPegawaiTindakan.inpostoperasi_id,
                        pasienmasukpenunjang_id : tmpPegawaiTindakan.pasienmasukpenunjang_id,
                        daftartindakan_id : tmpPegawaiTindakan.daftartindakan_id,
                        kegiatanoperasi_id : (typeof $('#kegiatanoperasi_id').val() != 'undefined') ? $('#kegiatanoperasi_id').val() : ''  ,
                        kegiatanoperasi_nama : (typeof $('.kegiatanoperasi_nama').val() != 'undefined') ? $('.kegiatanoperasi_nama').val() : '' ,
                        golonganoperasi_id : (typeof  $('#golonganoperasi-id').val() != 'undefined') ?  $('#golonganoperasi-id').val() : '' ,
                        golonganoperasi_nama : (typeof $('#golonganoperasi-nama').val() != 'undefined') ? $('#golonganoperasi-nama').val() : ''  
                    }

                    $().docoForm('click', {
                        url: '/bedah/informasi-pasien-operasi/intra-update-pegawai',
                        type: "POST",
                        skipConfirm: true,
                        skipSuccessNotif: true,
                        data: {
                            payload : payload,
                            cacheName : _cacheNamePegawai,
                            key : key,
                            daftartindakan_id : tmpPegawaiTindakan.daftartindakan_id
                        },
                        success: function (data) {
                            isChangeData()
                            dataPegawaiTindakan[key] = payload;

                        }
                    })
                }else{

                    $('#pegawai-'+ key).find('option').remove().end().append(new Option(tmpPegawaiTindakan.pegawai_nama, tmpPegawaiTindakan.pegawai_id));
                    docoNotification('error', 'Perhatian!', 'Pegawai yang sama pada tindakan ini sudah diinputkan');
                }
            })
            /** end of block pegawai operasi */
            
            /** blok posisi operasi */
            $(".select-posisi-edit").select2()
            //Ambil posisi dari controller
            
            $('.select-posisi-edit').change(function() {
                let key = $(this).data("key");
                let posisi_tim = $(this).find('option:selected').val()
                let posisi_tim_nama = $(this).find('option:selected').text()
                let isValid = validatePosisi(posisi_tim)
                let tmpPegawaiTindakan = dataPegawaiTindakan[key];
                
                let prosentase = $(this).find('option:selected').data('prosentase');
                let slug_posisi = $(this).find('option:selected').data('slug_posisi');
                if(isValid && typeof tmpPegawaiTindakan != 'undefined'){
                    let payload = {
                        pegawai_id : tmpPegawaiTindakan.pegawai_id,
                        pegawai_nama : tmpPegawaiTindakan.pegawai_nama,
                        posisi_tim : posisi_tim,
                        posisi_tim_nama : posisi_tim_nama,
                        prosentase : prosentase,
                        slug_posisi : slug_posisi,
                        inpostoperasi_id : tmpPegawaiTindakan.inpostoperasi_id,
                        pasienmasukpenunjang_id : tmpPegawaiTindakan.pasienmasukpenunjang_id,
                        daftartindakan_id : tmpPegawaiTindakan.daftartindakan_id,
                        kegiatanoperasi_id : (typeof $('#kegiatanoperasi_id').val() != 'undefined') ? $('#kegiatanoperasi_id').val() : ''  ,
                        kegiatanoperasi_nama : (typeof $('.kegiatanoperasi_nama').val() != 'undefined') ? $('.kegiatanoperasi_nama').val() : '' ,
                        golonganoperasi_id : (typeof  $('#golonganoperasi-id').val() != 'undefined') ?  $('#golonganoperasi-id').val() : '' ,
                        golonganoperasi_nama : (typeof $('#golonganoperasi-nama').val() != 'undefined') ? $('#golonganoperasi-nama').val() : ''  
                    }

                    $().docoForm('click', {
                        url: '/bedah/informasi-pasien-operasi/intra-update-pegawai',
                        type: "POST",
                        skipConfirm: true,
                        skipSuccessNotif: true,
                        data: {
                            payload : payload,
                            cacheName : _cacheNamePegawai,
                            key : key,
                            daftartindakan_id : tmpPegawaiTindakan.daftartindakan_id
                        },
                        success: function (data) {
                            isChangeData()
                            dataPegawaiTindakan[key] = payload;

                        }
                    })
                }else{
                    $('#posisi-'+ key).find('option').remove().end().append(new Option(tmpPegawaiTindakan.posisi_tim_nama, tmpPegawaiTindakan.posisi_tim));
                    timOperasi.map((eachTim) => {
                        if(eachTim.id != tmpPegawaiTindakan.posisi_tim){
                            $("#posisi-"+key).append(`
                                <option value="${eachTim.id}" data-slug="${eachTim.slug}" data-prosentase="${eachTim.prosentase}">${eachTim.text}</option>
                            `)
                        }
                    })
                    docoNotification('error', 'Perhatian!', 'Posisi yang sama pada tindakan ini sudah diinputkan');
                }
            })

            /** end of posisi */
            dokterBedah = []
            const dataRes = settings.jqXHR.responseJSON
            dataRes.data.map((employee) => {
                if (employee.posisi_tim == idDokterBedah) {
                    dokterBedah.push(employee)
                }
                if(isEdit){
                    let key = employee.rowNum -1;
                    $("#posisi-"+key).append(new Option(employee.posisi_tim_nama, employee.posisi_tim))
                    
                    timOperasi.map((eachTim) => {
                        if(eachTim.id != employee.posisi_tim){
                            $("#posisi-"+key).append(`
                                <option value="${eachTim.id}" data-slug="${eachTim.slug}" data-prosentase="${eachTim.prosentase}">${eachTim.text}</option>
                            `)
                        }
                    })
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

    $('.btn-popup-kembali').on('click', function (e) {
        if(isEdit && isChange){
            var header = 'Perhatian !';
            var message = 'Anda yakin tidak akan menyimpan perubahan?';
            var label = {
                buttons: {
                    'Yes': 'button-yes',
                    'No': 'button-no'
                },
                hidden: true
            };

            $.showQuestionDialog(header, message, label, function (reaction) {
                if (reaction == 'Yes') {
                    var dataPayload = {};

                    dataPayload = [{
                        cacheName : _cacheNameTindakan,
                        cacheData : dataTindakanOrigin
                    },{
                        cacheName : _cacheNamePegawai,
                        cacheData : JSON.parse(dataPegawaiOrigin)
                    }];

                    $().docoForm('click', {
                        url: '/bedah/informasi-pasien-operasi/reset-cache',
                        type: "POST",
                        skipConfirm: true,
                        skipSuccessNotif: true,
                        data: {
                            payload : JSON.stringify(dataPayload)
                        },
                        success: function (data) {
                            var response = data.response;
                            $('#modal_backdrop').modal('hide')
                            _tableitemoperasi.draw();
                        }
                    })

                    isEdit = false;
                    isChange = false;
                }
                if (reaction == 'No') {
                    hideQuestionDialog();
                    $('[data-popup="tooltip"]').tooltip();
                }
            });
        }else{
            $('#modal_backdrop').modal('hide')
        }
    })

    function validatePegawai(pegawai_id){
        let isValid = true;
        dataPegawaiTindakan.map((eachPeg) => {
            if(pegawai_id == eachPeg.pegawai_id){
                isValid = false;
            }
        })
        return isValid;
    }

    function validatePosisi(posisi_tim){
        let isValid = true;
        dataPegawaiTindakan.map((eachPeg) => {
            if(posisi_tim == eachPeg.posisi_tim){
                isValid = false;
            }
        })
        return isValid;
    }

    $('.close').on('click', function (e) {
        $('.btn-popup-kembali').trigger('click')
    })

    function isChangeData(){
        isChange = true;
        $('.btn[data-dismiss="modal"]').removeAttr('data-dismiss');
        $('.close').removeAttr('data-dismiss');
    }

</script>