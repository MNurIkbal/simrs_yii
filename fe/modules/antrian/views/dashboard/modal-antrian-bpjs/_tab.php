<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-08 17:47:28
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-05 13:52:05
 */
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\web\View;
?>
<?php 
$prefix = '';
$state = 0;
$posisi = 1;
?>
<?=Html::hiddenInput('pos',$posisi, ['class'=>'posisi-tab'])?>
<?=Html::hiddenInput('state',$state, ['class'=>'state-tab'])?>
<div class="panel-body">
    <div class="row">
        <div id="tab-antrian-onsite-bpjs">
        <?php 
        $i = 0;
        foreach($activeTab as $index => $item){
            $i++
            ?>
            <fieldset title="<?=$i?>" class="stepy-step" onmouseover="this.title='';">
                <legend class="stepy-legend"><?=$item['title']?></legend>
                <div class="content">
                </div>
            </fieldset>
            <?php
        }
        ?>
        <?=Html::button("<b><i class='fa fa-floppy-o'></i></b> Simpan", ['class'=>'stepy-finish btn-save-post btn btn-xs btn-labeled btn-info'])?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#tab-antrian-onsite-bpjs').stepy({
        backLabel: 'Kembali <b><i class=\'fa fa-chevron-left\'></i></b>',
        nextLabel: 'Selanjutnya <b><i class=\'fa fa-chevron-right\'></i></b>',
        titleClick: false,
        legend: false,
        duration: 200,
        transition: 'fade',
        select: function (index) {
            loadContent(index)
        },
    })

    $(document).ready(function () {
        var _posisi = $('.posisi-tab').val()
        var _state = $('.state-tab').val()
        if (_state == 0) {
            $('.stepy-navigator').addClass('hidden')
            $('#tab-antrian-onsite-bpjs').stepy('step', _posisi)
        } else {
            $('#tab-antrian-onsite-bpjs').stepy('step', 1)
        }
        $('#frm-jam-praktek').prop('disabled', true)
        $('#frm-antrian-dokter_id').append($('<option>', {
            value: '',
            text : '- PILIH -'
        }));
        $('#frm-antrian-dokter_id').prop('disabled', true)
    })

    $('#back-step').click(() => {
        loadContent(1)
        clearContent()
    })

    $('#next-step').click(() => {
        if($('#antrianonsiteform-nomor_identitas').val() == ''){
            docoNotification("warning", "Perhatian!", "Nomor Identitas Belum Terisi!");
            return false
        }
        if($('#antrianonsiteform-nomor_rujukan').val() == ''){
            docoNotification("warning", "Perhatian!", "Nomor Rujukan Belum Terisi!");
            return false
        }
        if($('.select2dokter').val() == ''){
            docoNotification("warning", "Perhatian!", "Dokter Belum Dipilih!");
            return false
        }

        if($('#antrianonsiteform-nomor_surat_kontrol').val().length > 0 && $('.select2dokter option:selected').data('kodeDokter') != $('#suggest-kode-dokter').val()){
            let namaDokter = $('#suggest-nama-dokter').val();
            docoNotification("warning", "Perhatian!", "Dokter kontrol "+namaDokter+" tidak tersedia atau sedang cuti. <b>Silahkan hubungi bagian pendaftaran</b>");
            return false
        }

        $('.field-frm-jenis-kunjungan').hide()
        clearContent()
        if ($('#next-step').attr('disabled') != 'disabled') {
            setValueConfirm();
        } else {
            var _jenisKunjungan = $('#frm-jenis-kunjungan').val();
            $('#next-step').attr('disabled',false)
            if (_jenisKunjungan == '1098') { /** Kontrol */
                if (typeof __daftarKontrol[$('#antrianonsiteform-nomor_rujukan').val()] != 'undefined') {

                } else {
                    docoNotification("warning", "Perhatian!", "Pasien terindikasi melakukan kontrol namun belum memiliki surat kontrol, silahkan hubungi bagian pendaftaran");
                    return false
                }
            }
            getDataPasien()
        }
        // getJadwalDokter()
        // setValueConfirm()
    })

    var loadContent = function (index) {
        var _steps = index - 1;
        if (_steps == 0) {
            $('.second-step').addClass('hidden')
            $('.first-step').removeClass('hidden')
        }else{
            $('.first-step').addClass('hidden')
            $('.second-step').removeClass('hidden')
        }
    }

    var autoSuggestPoli = () => {
        if($('#suggest-kode-poli').val() !== undefined){
            $('#frm-antrian-poli-tujuan option').each(function(){
                if($(this).attr('kode-ruangan-bpjs') == $('#suggest-kode-poli').val()){
                    $(`#frm-antrian-poli-tujuan option[kode-ruangan-bpjs=${$('#suggest-kode-poli').val()}]`).prop("selected", true).change();
                }
            })
        }
        $('#frm-antrian-poli-tujuan').prop('disabled', true)
    }

    var autoSuggestJenisKunjungan = () => {
        $('#next-step').attr('disabled', true);
        let noRujukan = $('#antrianonsiteform-nomor_rujukan').val()
        let noSuratKontrol = $('#no-surat-kontrol').val()
        
        var asalRujukan = $('#asal-rujukan').val()
        var poliRujukan = $('#poli-tujuan-selected').val()
        var poliDipilih = $('#frm-antrian-poli-tujuan-all :selected').val()
        if(noSuratKontrol != undefined && noSuratKontrol != ''){
            $(`#frm-jenis-kunjungan option[value='1098']`).prop('selected','selected').change();
            $('#frm-jenis-kunjungan').prop('disabled', true)
            $('#next-step').trigger('click')
        } else {
            $.ajax({
            url: '/antrian/dashboard/jumlah-sep?jenis_rujukan='+asalRujukan+'&no_rujukan='+noRujukan,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                    var jumlahSep = data.response.jumlahSEP
                    if(jumlahSep == 0 && asalRujukan == 1){
                        $(`#frm-jenis-kunjungan option[value='1096']`).prop('selected','selected').change();
                    } else if (jumlahSep == 0 && asalRujukan == 2){
                        $(`#frm-jenis-kunjungan option[value='1099']`).prop('selected','selected').change();
                    } else if (jumlahSep >= 1 && poliDipilih == poliRujukan){
                        $(`#frm-jenis-kunjungan option[value='1098']`).prop('selected','selected').change();
                    } else if (jumlahSep >= 1 && poliDipilih != poliRujukan){
                        $(`#frm-jenis-kunjungan option[value='1097']`).prop('selected','selected').change();
                    }
                    $('#next-step').trigger('click')
                    $('#frm-jenis-kunjungan').prop('disabled', true)
                    $('#next-step').attr('disabled', false);
            },
            error : function() {
                $('#next-step').attr('disabled', true);
            }})
        }
        
        // if(noSuratKontrol != undefined && noSuratKontrol != ''){
        //     $(`#frm-jenis-kunjungan option[value='1098']`).prop('selected','selected').change();
        // }else if(noRujukan.includes("B", 12) || noRujukan.includes("K", 12)){
        //     $(`#frm-jenis-kunjungan option[value='1099']`).prop('selected','selected').change();
        // }else if(noRujukan.includes("Y", 12) || noRujukan.includes("P", 12)){
        //     $(`#frm-jenis-kunjungan option[value='1096']`).prop('selected','selected').change();
        // }else{
        //     $(`#frm-jenis-kunjungan option[value='1097']`).prop('selected','selected').change();
        // }
        // $('#frm-jenis-kunjungan').prop('disabled', true)
    }
    $('#frm-jenis-kunjungan').change( () => {
        $('#text-jenis-kunjungan').val($('#frm-jenis-kunjungan :selected').text())
    })

    var clearContent = () => {
        $('#tab-antrian-onsite-bpjs-step-0').addClass('stepy-active')
        $('#tab-antrian-onsite-bpjs-step-1').removeClass('stepy-active')
        $('#tab-antrian-onsite-bpjs').stepy('step', 1)

        $('#frm-antrian-poli-tujuan').find('option').remove().end()
        $('#frm-antrian-poli-tujuan').append($('<option>', {
            value: '',
            text : '- PILIH -'
        }));

        $('#frm-jam-praktek').prop('disabled', true)
        $('#frm-jam-praktek').find('option').remove().end()
        $('#frm-jam-praktek').append($('<option>', {
            value: '',
            text : '- PILIH -'
        }));
    }

    $('#frm-antrian-poli-tujuan').change((e) => {
        e.preventDefault()
        $('#frm-jam-praktek').find('option').remove().end()
        $('#frm-jam-praktek').append($('<option>', {
            value: '',
            text : '- PILIH -'
        }));
        kodeRuanganBpjs = $('#suggest-kode-poli').val()
        ruanganNama = $('#frm-antrian-poli-tujuan :selected').text()
        kodeDokter = $('#frm-antrian-dokter_id :selected').data('kodeDokter')
        $.ajax({
            url: '/antrian/dashboard/get-jam-praktek-bpjs?kode_ruangan_bpjs='+kodeRuanganBpjs+'&kode_dokter_bpjs='+kodeDokter,
            type: 'POST',
            dataType: 'json',
            beforeSend: function (data) {
            },
            success: function (data) {
                if (data.result?.length > 0) {
                    let suggestJadwal = data?.suggest_jadwal
                    $.each(data.result, function (i, item) {
                        $('#frm-jam-praktek').append($('<option>', {
                            value: i,
                            text : item
                        }));
                        if(suggestJadwal != undefined && suggestJadwal == item){
                            $(`#frm-jam-praktek option[value='${i}']`).attr('selected','selected');
                        }else{
                            $(`#frm-jam-praktek option[value='${i}']`).attr('selected','selected');
                        }
                    });
                    $('#frm-jam-praktek').prop('disabled', false)
                } else {
                    if(data?.response){
                        let errorMessage =  data?.response?.metadata?.message
                        docoNotification('warning','Perhatian!', errorMessage);
                    }else{
                        docoNotification('warning','Perhatian!',`Dokter tidak memiliki jadwal di ${ruanganNama} hari ini.`);
                    }
                }
            }
        });
    })

    $('#frm-antrian-poli-tujuan-all').change((e) => {
        $.ajax({
            url: '/antrian/dashboard/get-dokter-by-poli?kode_poli='+$('#frm-antrian-poli-tujuan-all :selected').val(),
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.result) {
                    $('#frm-antrian-dokter_id').find('option').remove().end()
                    $('#frm-antrian-dokter_id').prop('disabled', false)
                    $.each(data.result, function (i, item) {
                        $('#frm-antrian-dokter_id').append($('<option>', {
                            value: item.id,
                            text : item.nama_dokter + ' (' + item.jam_praktek + ')'
                        }).data({
                            "kode-dokter": item.kode_dokter_bpjs,
                            "kode-poli": item.kode_ruangan_bpjs,
                            "nama-dokter": item.nama_dokter,
                            "jam-praktek": item.jam_praktek
                        }));
                    });
                    let is_found = false;
                    
                    $("#frm-antrian-dokter_id option").each(function(){
                        if($(this).data('kodePoli') == $('#poli-tujuan-selected').val()){
                            $('#suggest-kode-poli').val($('#poli-tujuan-selected').val())
                        }

                        if (($('#suggest-kode-dokter').val() == $(this).data('kode-dokter'))) {
                            $(this).prop('selected', true);
                        }

                        if (data.result != null) {
                            is_found = true
                        } else {
                            is_found = false
                        }
                    });
                    if(is_found == false){
                        docoNotification('warning','Perhatian!', 'Dokter tidak ditemukan.');
                    }
                }
            },
            error: function(err){
                console.log('err',err)
            }
        })
    })

    $('input[name="AntrianOnsiteForm[jenis_identitas]"]').change(() => {
        if($('input[name="AntrianOnsiteForm[jenis_identitas]"]:checked').val() == 0){
            $('.field-antrianonsiteform-nomor_identitas .control-label').text('Nomor Identitas')
            $('#antrianonsiteform-nomor_identitas').attr('placeholder', 'Masukan Nomor Identitas')
        }else{
            $('.field-antrianonsiteform-nomor_identitas .control-label').text('Nomor Kartu')
            $('#antrianonsiteform-nomor_identitas').attr('placeholder', 'Masukan Nomor Kartu')
        }
    })

    var setValueConfirm = () => {
        $('#frm-nama-dokter').val($('#frm-antrian-dokter_id :selected').data('nama-dokter'))
        $('#frm-jam-praktek').val($('#frm-antrian-dokter_id :selected').data('jam-praktek'))
        $('#frm-antrian-poli-tujuan').val($('#frm-antrian-poli-tujuan-all :selected').text())
        // $('#frm-antrian-poli-tujuan').data('kode-poli-tujuan') = $('#frm-antrian-poli-tujuan-all :selected').val()
        autoSuggestJenisKunjungan()
    }

    var getJadwalDokter = () => {
        var dokterId = $('#frm-antrian-dokter_id').val()
        $.ajax({
            url: '/antrian/dashboard/get-poli-bpjs?dokter_id='+dokterId,
            type: 'GET',
            dataType: 'json',
            beforeSend: function (data) {
            },
            success: function (data) {
                if (data.result) {
                    $.each(data.result, function (i, item) {
                        $('#frm-antrian-poli-tujuan').append($('<option>', {
                            value: item.id,
                            text : item.ruangan_nama
                        }).attr('kode-ruangan-bpjs', item.kode_ruangan_bpjs));
                    });
                    autoSuggestPoli()
                }
            }
        });
    }

    var getDataPasien = () => {
        var nomorIdentitas = $('#antrianonsiteform-nomor_identitas').val();
        var jenisIdentitas = $('input[name="AntrianOnsiteForm[jenis_identitas]"]:checked').val();

        $.ajax({
            url: '/antrian/dashboard/get-data-pasien-bpjs?nomor_identitas='+nomorIdentitas+'&jenis_identitas='+jenisIdentitas,
            type: 'GET',
            dataType: 'json',
            beforeSend: function (data) {
            },
            success: function (data) {
                if (data.result) {
                    let patientName = data.result?.nama ? data.result?.nama : ''
                    // let noMr = data.result?.noMr ? data.result?.noMr + ' - ' : ''
                    let noKartu = data.result?.noKartu ? data.result?.noKartu : '-'
                    let nik = data.result?.nik ? data.result?.nik : '-'
                    let noTelepon = data.result?.noTelepon ? data.result?.noTelepon : '-'
                    let noRujukan = $('#antrianonsiteform-nomor_rujukan').val()
                    let noSuratKontrol = $('#antrianonsiteform-nomor_surat_kontrol').val()
                    let today = moment().format('D MMMM, YYYY');
                    let dataPasien = data.result?.data_pasien
                    let noMr = dataPasien?.no_rekam_medik ? dataPasien?.no_rekam_medik : '';
                    $('.label-no-kartu').text(noKartu)
                    $('.label-nik').text(nik)
                    $('.label-no-hp').text(noTelepon)
                    $('.label-tgl-periksa').text(today)
                    $('#pasien-id').val(dataPasien?.pasien_id)
                    // $('#no-rekam-medik').val(data.result?.noMr)
                    $('#no-rekam-medik').val(dataPasien?.no_rekam_medik)
                    $('.confirm-title-patient__name').text(patientName)
                    $('.confirm-title-patient__no-rm').text(noMr)
                    $('.label-no-referensi').text(noRujukan)
                    $('.label-no-surat-kontrol').text((noSuratKontrol ? noSuratKontrol : '-'))

                    loadContent(2)
                    $('#tab-antrian-onsite-bpjs').stepy('step', 2)
                }
            },
            error: function(data){
                if(data?.responseJSON){
                    let errorMessage = data?.responseJSON?.metaData?.message
                    $('#frm-antrian-poli-tujuan').find('option').remove().end()
                    $('#frm-antrian-poli-tujuan').append($('<option>', {
                        value: '',
                        text : '- PILIH -'
                    }));
                    docoNotification('warning','Perhatian!', errorMessage);
                }
            }
        });
    }

    $('#antrianonsiteform-nomor_identitas').keypress(function (e) {
        var key = e.which;
        if(key === 13){
            if($('#antrianonsiteform-nomor_identitas').val() == ''){
                docoNotification("warning", "Perhatian!", "Nomor Identitas Belum Terisi!");
                return false
            }
            getDaftarRujukanRencanaKontrol()
            $('#antrianonsiteform-nomor_identitas').blur()
            return false
        }
    })

    //dari button cari
    $('#nomor-identitas-btn-cari').click(function(e) {
        let noIdentitas = $('#antrianonsiteform-nomor_identitas').val();
        let jenisIdentitas = $('input[name="AntrianOnsiteForm[jenis_identitas]"]:checked').val();
        var length = $('#antrianonsiteform-nomor_identitas').val().length;
        var params;
        if($('#antrianonsiteform-nomor_identitas').val() == ''){
            docoNotification("warning", "Perhatian!", "Nomor Identitas Belum Terisi!");
            return false
        } else {
            if(jenisIdentitas == 0){
                if(length != 16){
                    docoNotification("warning", "Perhatian!", "NIK Harus 16 digit.")
                    hideLoader()
                    return false
                }
                params = 'no_identitas='+noIdentitas
            } else if (jenisIdentitas == 1){
                if(length != 13){
                    docoNotification("warning", "Perhatian!", "Nomor Kartu BPJS Harus 13 digit.")
                    hideLoader()
                    return false
                }
                params = 'no_kartu='+noIdentitas
            }
        }
        showLoader('Memuat Halaman...')
        $(this).attr('disabled', true);
        $('.field-frm-antrian-dokter_id').show();
        $.ajax({
            url: '/antrian/dashboard/cek-pasien-baru?'+params,
            type: 'GET',
            dataType: 'json',
            beforeSend: function (data) {
            },
            success: function (data) {
                // hideLoader()
                $('#nomor-identitas-btn-cari').attr('disabled', false);
                if(data.is_validated == false){
                    docoNotification("warning", "Perhatian!", data.text);
                } else {
                    getDaftarRujukanRencanaKontrol()
                    getHistoryPoli(data.pasien_id)
                    $('#antrianonsiteform-nomor_identitas').blur()
                    return false
                }
                    
            },
            error : function() {
                $('#nomor-identitas-btn-cari').attr('disabled', false);
            }
        });
    })

    var getHistoryPoli = (pasien_id) => {
        var dokterId = $('#frm-antrian-dokter_id').val()
        $.ajax({
            url: '/antrian/dashboard/get-history-poli?pasien_id='+pasien_id,
            type: 'GET',
            dataType: 'json',
            beforeSend: function (data) {
            },
            success: function (response) {
                $('#frm-antrian-poli-tujuan-all').find('option').remove().end()
                $('#frm-antrian-poli-tujuan-all').append($('<option>', {
                    value: '',
                    text : '- PILIH -'
                }));
                if (response.data) {
                    $.each(response.data, function (i, item) {
                        $('#frm-antrian-poli-tujuan-all').append($('<option>', {
                            value: item.kode_ruangan_bpjs,
                            text : item.ruangan_nama
                        }).attr('ruangan-id', item.ruangan_id));
                    });
                }
            }
        });
    }

    var getDaftarRujukanRencanaKontrol = () => {
        let wrapperParents = $('#modal-rujukan-rencana')
        showLoader('Memuat Halaman...')
        $('#modal-rujukan-rencana .modal-content').docoLoad({
            url: '/antrian/dashboard/modal-rujukan-rencana-kontrol',
            dataType: 'html',
            success: function (data) {
                hideLoader()
                $('#modal-rujukan-rencana .modal-content').parents('.modal').modal('show')
                $('#modal-rujukan-rencana').css('z-index', '1950')
                $('#modal-rujukan-rencana').find('.modal-dialog').css('width', '1280px')
                $('#nomor-identitas-btn-cari').attr('disabled',false)
            },
            error: function () {
                hideLoader()
                $('#nomor-identitas-btn-cari').attr('disabled',false)
            }
        })
    }

    function antrianOnsiteBpjsAutoDaftar(payload)
    {
        $.ajax({
            url: '/antrian/dashboard/create-jkn-onsite',
            type: 'POST',
            dataType: 'json',
            data: payload,
            global: false,
            beforeSend: function(xhr, note, request) {
                showLoaderCustom('Memproses data', '(20%)', 'Data Anda sedang kami proses untuk membuat antrian.');
            },
            complete: function(event, xhr, optionApi) {
            },
            success: function (res) {
                if(res?.response){
                    if(res?.response?.data?.length == 0){
                        let errTitle = res.response.title
                        let errMessage = res.response.message
                        if(typeof res.response.text != 'undefined'){
                            errMessage = res.response.text;
                        }
                        hideLoaderCustom();
                        docoNotification('warning', errTitle, errMessage);
                    }else{
                        updateLoaderCustom('Memproses data', '(40%)', 'Nomor antrian Anda sudah dibuat, sistem sedang mendaftarkan kunjungan Anda...')

                        let randString = res?.response?.randString;
                        let responsePasien = res?.response?.pendaftaranol;
                        let payload = setPayloadAutoDaftar(responsePasien);
                        let dataPrint = {
                            "antrian_id": res?.response?.payload?.antrian_id,
                            "no_antrian": res?.response?.payload?.nomorantrean,
                            "jenisantrian_id": 2121,
                            "jenis_antrian_id": 312,
                        }

                        listenStatusReservationUpdate(payload, randString, dataPrint);

                         $.ajax({
                            url: '/antrian/dashboard/auto-register-process',
                            type: 'POST',
                            dataType: 'json',
                            global: false,
                            data: {
                                randString: randString,
                                reservationItems: JSON.stringify(payload)
                            },
                            success: function () {
                                updateLoaderCustom('Memproses data', '(70%)', 'Nomor antrian Anda sudah dibuat, sistem sedang mendaftarkan kunjungan Anda...');
                            },
                            error: function () {
                                hideLoaderCustom();
                                docoNotification('warning', 'Perhatian!', 'Proses pembuatan Kunjungan Anda gagal. <b>Silakan hubungi petugas pendaftaran untuk melakukan pendaftaran secara manual.</b>', false);
                            }
                         });
                    }
                } else {
                    hideLoaderCustom();
                }
            },
            error: function(data){
                $('#frm-antrian-poli-tujuan').prop('disabled', true)
                hideLoaderCustom();
                if(data?.responseJSON){
                    let response = data?.responseJSON?.response
                    if(response?.data?.is_finger){
                        window.open(response?.data?.url);
                    }
                    let errorMessage = response.message
                    if(typeof response.text != 'undefined'){
                        errorMessage = response.text;
                    }
                    docoNotification('warning','Perhatian!', errorMessage);
                }
            }
        });
    }

    function antrianOnsiteBpjsManual(payload)
    {
        $.ajax({
            url: '/antrian/dashboard/create-jkn-onsite',
            type: 'POST',
            dataType: 'json',
            data: payload,
            beforeSend: function (data) {
            },
            success: function (res) {
                if(res?.response){
                    if(res?.response?.data?.length == 0){
                        let errTitle = res.response.title
                        let errMessage = res.response.message
                        if(typeof res.response.text != 'undefined'){
                            errMessage = res.response.text;
                        }
                        docoNotification('warning', errTitle, errMessage);
                    }else{
                        docoNotification('success', 'Proses Berhasil!', 'Antrian Dicetak');
                        let antrian_id = res?.response?.payload?.antrian_id
                        let no_antrian = res?.response?.payload?.nomorantrean

                        $('#modal_backdrop').modal('hide');
                        let data = {
                            "antrian_id": antrian_id,
                            "no_antrian": no_antrian,
                            "jenisantrian_id": 2121,
                        }

                        $.ajax({
                            type: 'POST',
                            url: '/antrian/dashboard/cetak-antrian-dashboard',
                            timeout: (60 * 1000),
                            data: data,
                            success: function (res){
                                location.reload();
                                $.redirect("/antrian/dashboard/cetak-antrian-dashboard",{
                                    antrian_id: antrian_id,
                                    no_antrian: no_antrian,
                                    jenisantrian_id: 2121,
                                });
                            },
                            error: function (error) {
                            }
                        });
                    }
                }
            },
            error: function(data){
                $('#frm-antrian-poli-tujuan').prop('disabled', true)
                if(data?.responseJSON){
                    let response = data?.responseJSON?.response
                    if(response?.data?.is_finger){
                        window.open(response?.data?.url);
                    }
                    let errorMessage = response.message
                    if(typeof response.text != 'undefined'){
                        errorMessage = response.text;
                    }
                    docoNotification('warning','Perhatian!', errorMessage);
                }
            }
        });
    }


    $('#submit-button').click((e) => {
        e.preventDefault()
        /** remove prop disabled from selection, to get its value */
        $('#frm-antrian-poli-tujuan').prop('disabled', false)

        let jenisIdentitas = $('input[name="AntrianOnsiteForm[jenis_identitas]"]:checked').val();
        let nomorIdentitas = $('#antrianonsiteform-nomor_identitas').val();
        let nomorRujukan = $('#antrianonsiteform-nomor_rujukan').val();
        let nomorReferensi = ''
        if ($('#antrianonsiteform-nomor_surat_kontrol').val() != '' && $('#antrianonsiteform-nomor_surat_kontrol').val() != null) {
            nomorReferensi = $('#antrianonsiteform-nomor_surat_kontrol').val();
        } else {
            nomorReferensi = $('#antrianonsiteform-nomor_rujukan').val();
        }
        // let nomorReferensi = $('#no-surat-kontrol').val() != undefined && $('#no-surat-kontrol').val() != '' ? $('#no-surat-kontrol').val() : $('#antrianonsiteform-nomor_rujukan').val()
        let namaDokter = $('#frm-antrian-dokter_id :selected').text()
        let nik = $('.label-nik').text()
        let noHp = $('.label-no-hp').text()
        let kodeDokter = $('#frm-antrian-dokter_id :selected').data('kodeDokter')
        let dokterId = $('#frm-antrian-dokter_id').val()
        let kodeRuanganBpjs = $('#suggest-kode-poli').val()
        let poliTujuan = $('#frm-antrian-poli-tujuan :selected').val()
        let poliTujuanAll = $('#frm-antrian-poli-tujuan-all :selected').val()
        let jamPraktek = $('#frm-antrian-dokter_id :selected').data('jam-praktek')
        let jenisKunjungan = $('#frm-jenis-kunjungan :selected').val()
        let noKartu = $('.label-no-kartu').text()
        let pasien_id = $('#pasien-id').val()
        let noRekamMedik = $('#no-rekam-medik').val()
        let payload = {
            'kodepoli': poliTujuanAll,
            'kodedokter': kodeDokter,
            'nomorkartu': noKartu,
            'jenisidentitas': jenisIdentitas,
            'no_identitas_pasien': nik,
            'no_telepon_pasien': noHp,
            'no_rekam_medik': noRekamMedik,
            'jampraktek': jamPraktek,
            'jeniskunjungan': jenisKunjungan,
            'nomorreferensi': nomorReferensi,
            'no_rujukan': nomorRujukan,
        }

        if (konfigAutoDaftar) {
            antrianOnsiteBpjsAutoDaftar(payload)
        } else {
            antrianOnsiteBpjsManual(payload);
        }
    })
</script>