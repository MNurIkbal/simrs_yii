/*
* @Author: rizqi_fitrianto
* @Date:   2018-08-10 10:48:49
* @Last Modified by:   rizqi_fitrianto
* @Last Modified time: 2018-09-07 11:23:12
*/
$(document).ready(function(){
    if(!$('.stepy-navigator').hasClass('hidden')){
        $('.stepy-navigator').addClass('hidden')
    }
    $('.select2').select2();
    $('.dokterbedah-id').val( $('.dr-operator-id').val() )
    $('.dokteranastesi-id').val( $('.dr-anastesi-id').val() )
    $('.dokterbedah-nama').text( $('.dok-operator').val() )
    $('.dokteranastesi-nama').text( $('.dok-anastesi').val() )
    if($('#intraoperasiform-masuk_kamar').val() == ''){
        $('#intraoperasiform-masuk_kamar').val( $('.jam-rencana-mulai').val() )
    }
    if($('#intraoperasiform-mulai_anastesi').val() == ''){
        $('#intraoperasiform-mulai_anastesi').val( $('.jam-rencana-mulai').val() )
    }
    if($('#intraoperasiform-mulai_operasi').val() == ''){
        $('#intraoperasiform-mulai_operasi').val( $('.jam-rencana-mulai').val() )
    }
    if($('#intraoperasiform-selesai_anastesi').val() == ''){
        $('#intraoperasiform-selesai_anastesi').val( $('.jam-rencana-selesai').val() )
    }
    if($('#intraoperasiform-selesai_operasi').val() == ''){
        $('#intraoperasiform-selesai_operasi').val( $('.jam-rencana-selesai').val() )
    }

    $('.txt-timepicker').timepicker({
        showMeridian: false,
        minuteStep: 5,
        defaultTime: false
    });
    _tableoperasi = $('#table-tim-operasi').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl+'igd/riwayat-pasien/get-cache?cacheName='+_cacheoperasi,
        columns: [
            {
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
                title: 'Aksi',
                data: 'aksi',
            },
        ],
    });
    _tableitemoperasi = $('#table-item-operasi').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl+'igd/riwayat-pasien/get-cache?cacheName='+_cacheitemoperasi,
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Nama Operasi',
                data: 'daftartindakan_nama',
            },
            {
                title: 'Cyto',
                data: 'cyto',
            },
            {
                title: 'Jenis Luka',
                data: 'jenis_luka_nama',
            },
            {
                title: 'Jenis Operasi',
                data: 'golonganoperasi_nama',
            },
            {
                title: 'Jenis Anastesi',
                data: 'jenisanastesi_nama',
            },
            {
                title: 'Aksi',
                data: 'aksi',
            },
        ],
    });
    _tablepenggunaancairan = $('#table-penggunaan-cairan').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl+'igd/riwayat-pasien/get-cache?cacheName='+_cachepenggunaancairan,
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Kegiatan',
                data: 'kegiatan',
            },
            {
                title: 'Cairan Masuk',
                data: 'cairan_masuk',
            },
            {
                title: 'Cairan Keluar',
                data: 'cairan_keluar',
            },
            {
                title: 'Keterangan',
                data: 'keterangan',
            },
            {
                title: 'Aksi',
                data: 'aksi',
            },
        ],
    });
    _tablealatditubuh = $('#table-alat-ditubuh').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl+'igd/riwayat-pasien/get-cache?cacheName='+_cachealatditubuh,
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Jenis Alat',
                data: 'jenis_alat_nama',
            },
            {
                title: 'Jumlah',
                data: 'jumlah',
            },
            {
                title: 'Lokasi',
                data: 'lokasi',
            },
            {
                title: 'Aksi',
                data: 'aksi',
            },
        ],
    });
    _tablepemeriksaanpelengkap = $('#table-pemeriksaan-pelengkap').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl+'igd/riwayat-pasien/get-cache?cacheName='+_cachepemeriksaanpelengkap,
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Nama Pemeriksaan',
                data: 'daftartindakan_nama',
            },
            {
                title: 'Nama Jaringan',
                data: 'nama_jaringan',
            },
            {
                title: 'Ukuran',
                data: 'qty',
            },
            {
                title: 'Aksi',
                data: 'aksi',
            },
        ],
    });
    _tablekonsultindakan = $('#table-konsul-tindakan').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl+'igd/riwayat-pasien/get-cache?cacheName='+_cachekonsultindakan,
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Nama Tindakan',
                data: 'daftartindakan_nama',
            },
            {
                title: 'Nama Dokter',
                data: 'dokter_nama',
            },
            {
                title: 'Bagian Tubuh',
                data: 'bagian_tubuh',
            },
            {
                title: 'Alasan',
                data: 'alasan',
            },
            {
                title: 'Aksi',
                data: 'aksi',
            }
        ],
    });
    _tablepenggunaanbmhp = $('#table-penggunaan-bmhp').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        createdRow: function (row, data, index) {
            if (data.is_available == 0) {
                $(row).addClass('blured-row')
            }
        },
        ajax: baseUrl+'igd/riwayat-pasien/get-cache?cacheName='+_cachepenggunaanbmhp,
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'Jenis Alat',
                data: 'obatalkes_nama',
            },
            {
                title: 'Persediaan',
                data: 'persediaan',
            },
            {
                title: 'Tambahan',
                data: 'tambahan',
            },
            {
                title: 'Terpakai',
                data: 'terpakai',
            },
            {
                title: 'Sisa',
                data: 'sisa',
            },
            {
                title: 'Ditagihkan',
                data: 'ditagihkan',
            },
            {
                title: 'Aksi',
                data: 'aksi',
            }
        ],
    });
    $('.select-instrumen').select2({
        placeholder: '',
        minimumInputLength: 3,
        ajax: {
            url: '/igd/riwayat-pasien/get-jenis-alat',
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
    $('.select-penunjangkhusus').select2({
        placeholder: '',
        minimumInputLength: 3,
        ajax: {
            url: '/igd/riwayat-pasien/get-jenis-alat',
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
    $('.select-pegawai-pemberi').select2({
        placeholder: '',
        minimumInputLength: 3,
        ajax: {
            url: '/igd/riwayat-pasien/get-pegawai',
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
    $('input[name="IntraOperasiForm[is_diserahkan]"]').change(function(){
        if( $(this).val() == 1 ){
            if($('.penerima-pemberi').hasClass('hidden')){
                $('.penerima-pemberi').removeClass('hidden')
            }
        } else{
            if(!$('.penerima-pemberi').hasClass('hidden')){
                $('.penerima-pemberi').addClass('hidden')
                $('.penerima-txt').val('')
                $('.select-pegawai-pemberi').val('').trigger('change')
            }
        }
    })
    $('input[name="IntraOperasiForm[is_jaringantubuh]"]').change(function(){
        if( $(this).val() == 1 ){
            if($('.pa-jaringan-tubuh').hasClass('hidden')){
                $('.pa-jaringan-tubuh').removeClass('hidden')
            }
        } else{
            if(!$('.pa-jaringan-tubuh').hasClass('hidden')){
                $('.pa-jaringan-tubuh').addClass('hidden')
                $('#intraoperasiform-jenis_jaringan').val('')
                $('input[name="IntraOperasiForm[is_diserahkan]"]').prop('checked',false).trigger('change')
            }
        }
    })
    populateOptions(_opsipenunjang, '.select-penunjangkhusus')
    populateOptions(_opsiinstrumen, '.select-instrumen')
    populateOptions(_opsipenerima, '.select-pegawai-pemberi')


    $('.collapse-click').on('click', function(){
        var _id = $(this).attr('id');
        var _obj = $('#'+_id);
        if(!_obj.hasClass('rotate-180')){
            _obj.addClass('rotate-180')
        }else{
            _obj.removeClass('rotate-180')
        }
    })
})
$('.submit-intra-operasi').on('click', function(){
    $().docoForm('click',{
        data: $('#form-intra-operasi').serializeArray(),
        url : $('#form-intra-operasi').attr('action'),
        success : function(data) {
            $('#tab-operasi').stepy('step', '2')
            $('.stepy-navigator').removeClass('hidden')
        }
    });
})

$(document).on('click', '.delete-item', function(e){
    e.preventDefault()
    var _key = $(this).attr('data-key');
    var _cachename = $(this).attr('data-cache');
    var _arrstring = _cachename.split('-')
    $(this).docoForm('click',{
        confirmMessage: 'Anda yakin akan menghapus item ini?',
        url: '/igd/riwayat-pasien/unset-cache?key='+_key+'&cacheName='+_cachename,
        success : function (response) {
            if(_arrstring[0] == 'pegawaioperasi'){
                _tableoperasi.draw()
            }else if(_arrstring[0] == 'itemoperasi'){
                _tableitemoperasi.draw();
                _tablepenggunaanbmhp.draw();
            }else if(_arrstring[0] == 'penggunaancairan'){
                _tablepenggunaancairan.draw()
            }else if(_arrstring[0] == 'alatditubuh'){
                _tablealatditubuh.draw()
            }else if(_arrstring[0] == 'pemeriksaanpelengkap'){
                _tablepemeriksaanpelengkap.draw()
            }else if(_arrstring[0] == 'konsultindakan'){
                _tablekonsultindakan.draw()
            }else if(_arrstring[0] == 'penggunaanbmhp'){
                _tablepenggunaanbmhp.draw()
            }else if(_arrstring[0] == 'pemasanganinfus'){
                _tablepemasanganinfus.draw()
            }
        }
    });
})

var populateOptions = function(_obj, _target){
    var _options = new Option(_obj.name, _obj.id, false, false)
    $(_target).append(_options).val(_obj.id).trigger('change')
}

