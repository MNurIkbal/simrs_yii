/*
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-10 10:48:49
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-07 11:23:12
 */
var _tabletindakanluarbedah
$(document).ready(function () {
    if ($('.btn-save-post').length) {
        $('.btn-save-post').remove()
    }
    idDokterBedah = $('#idDokterBedah').val()
    if (!$('.stepy-navigator').hasClass('hidden')) {
        $('.stepy-navigator').addClass('hidden')
    }
    $('.select2').select2();
    $('.dokterbedah-id').val($('.dr-operator-id').val())
    $('.dokteranastesi-id').val($('.dr-anastesi-id').val())
    $('.dokterbedah-nama').text($('.dok-operator').val())
    $('.dokteranastesi-nama').text($('.dok-anastesi').val())
    if ($('#intraoperasiform-masuk_kamar').val() == '') {
        $('#intraoperasiform-masuk_kamar').val($('.jam-rencana-mulai').val())
    }
    if ($('#intraoperasiform-mulai_anastesi').val() == '') {
        $('#intraoperasiform-mulai_anastesi').val($('.jam-rencana-mulai').val())
    }
    if ($('#intraoperasiform-mulai_operasi').val() == '') {
        $('#intraoperasiform-mulai_operasi').val($('.jam-rencana-mulai').val())
    }
    if ($('#intraoperasiform-selesai_anastesi').val() == '') {
        $('#intraoperasiform-selesai_anastesi').val($('.jam-rencana-selesai').val())
    }
    if ($('#intraoperasiform-selesai_operasi').val() == '') {
        $('#intraoperasiform-selesai_operasi').val($('.jam-rencana-selesai').val())
    }

    $('.txt-timepicker').timepicker({
        showMeridian: false,
        minuteStep: 5,
        defaultTime: false
    });
    _tableitemoperasi = $('#table-item-operasi').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cacheitemoperasi,
        columns: [{
            title: 'No',
            data: 'rowNum',
            searchable: false,
            orderable: false
        },
        {
            title: 'Tindakan Operasi',
            data: 'daftartindakan_nama',
            visible: false
        },
        {
            title: 'Kegiatan Operasi',
            data: 'kegiatan_operasi_header',
            className: 'kegiatan-operasi',
        },
        {
            title: 'Golongan Operasi',
            data: 'str_null',
            className: 'golongan-operasi',
        },
        {
            title: 'Nama Pegawai',
            data: 'str_null',
            className: 'nama-pegawai',
        },
        {
            title: 'Posisi Tim',
            data: 'str_null',
            className: 'posisi-tim',
        },
        {
            title: 'Penyulit',
            data: 'penyulit',
            visible: false,
            render: (col) => {
                return col == '1' ? '✓' : ''
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
        rowCallback: (row, data) => {
            $(row).addClass(`pegawai-${data.pegawai_id} dt-${data.daftartindakan_id}`)
            $(row).attr('style', 'background:#d7f7f0')

            if(show_kegiatan_golongan_operasi == 1){
                $('td.kegiatan-operasi', row).attr( 'colspan', '4');
                $('td.golongan-operasi', row).css( 'display','none');
                $('td.nama-pegawai', row).css( 'display','none' );
                $('td.posisi-tim', row).css( 'display','none' );
            }else{
                $('td.nama-pegawai', row).html(data.kegiatan_operasi_header);
                $('td.nama-pegawai', row).attr( 'colspan', '2');
                $('td.posisi-tim', row).css( 'display','none');
            }
        },
        drawCallback: (data) => {
            if(show_kegiatan_golongan_operasi != 1){
                _tableitemoperasi.column(2).visible(false);
                _tableitemoperasi.column(3).visible(false);
            }else{
                _tableitemoperasi.column(2).visible(true);
                _tableitemoperasi.column(3).visible(true);
            }
            $('th[data-key="hapus"]').attr('style','width:150px');
            let datas = data.json.data;
            $.each(datas, function (n, row) {
                _tr = $(`.dt-${row.daftartindakan_id}`);
                _timOperasi = JSON.parse(row.tim_operasi);
                _html = '';
                
                $.each(_timOperasi, function (key, val) {
                    _html += `<tr>`;
                    _html += `<td> ${key+1} </td>`;
                    if(show_kegiatan_golongan_operasi ==1){
                        _html += `<td> ${row.kegiatanoperasi_nama} </td>`;
                        _html += `<td> ${row.golonganoperasi_nama} </td>`;
                    }
                    _html += `<td> ${val.pegawai_nama} </td>`;
                    _html += `<td> ${val.posisi_tim_nama} </td>`;
                    _html += `<td> </td>`;
                    _html += `</tr>`;
                });
                
                $(_html).insertAfter(_tr);
             })
        }
    });

    _tablepenggunaancairan = $('#table-penggunaan-cairan').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cachepenggunaancairan,
        columns: [{
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
            title: 'Pegawai Input',
            data: 'pegawai_input',
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
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cachealatditubuh,
        columns: [{
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
            title: 'Pegawai Input',
            data: 'pegawai_input',
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
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cachepemeriksaanpelengkap,
        columns: [{
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
            title: 'Pegawai Input',
            data: 'pegawai_input',
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
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cachekonsultindakan,
        columns: [{
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
            title: 'Pegawai Input',
            data: 'pegawai_input',
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
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cachepenggunaanbmhp + '&ruangan_id=' + ruangan_id,
        columns: [{
            title: 'No',
            data: 'rowNum',
            searchable: false,
            orderable: false
        },
        {
            title: 'BMHP',
            data: 'obatalkes_nama',
        },
        {
            title: 'Persediaan',
            data: 'persediaan',
        },
        {
            title: 'Tambahan',
            data: 'tambahan',
            visible:false
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
            title: 'Pegawai Input',
            data: 'pegawai_input',
        },
        {
            title: 'Aksi',
            data: 'aksi',
        }
        ],
        drawCallback: function (e) {
            var api = this.api();
            for (var i = 0; api.rows().count() > i; i++) {
                var rowData = api.row(i).data();
                var rowNode = api.row(i).node();
                var cellNode = api.cell(i, 1).node();
                if (rowData.sisa < 0) {
                    $(rowNode).addClass('blured-row')
                }
            }
        },
    });

    _tableinstrumen = $('#table-set-instrumen').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: baseUrl + 'bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cacheinstrumen,
        columns: [{
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
            title: 'Pegawai Input',
            data: 'pegawai_input',
        },
        {
            title: 'Aksi',
            data: 'aksi',
        }
        ],
    });

    $('#instrumen-select_instrumen').select2InfinityScroll({
        url: '/bedah/informasi-pasien-operasi/get-instrumen',
    })
    $('#instrumen-select_instrumen').on('change', () => {
        let data = $('#instrumen-select_instrumen').select2('data')[0];
        let qty_stok = typeof data.qty_stok !== 'undefined' ? data.qty_stok : 0
        $('#instrumen-persediaan_instrumen').val(qty_stok)
    })
    $('#add-instrumen').on('click', (e) => {
        e.preventDefault()
        let data = $('#instrumen-select_instrumen').select2('data')[0]
        tambahan = parseInt($('#instrumen-tambahan_instrumen').val())
        terpakai = parseInt($('#instrumen-terpakai_instrumen').val())
        if ($('#instrumen-select_instrumen').val() == '') {
            $('#instrumen-select_instrumen').closest('.col-md-3').append('<p class="text-error-message" style="color: red">Instrumen Tidak Boleh Kosong!</p>')
            setTimeout(() => {
                $('.text-error-message').remove()
            }, 3000);
            return false;
        }
        const _form = {
            inpostoperasi_id: parseInt($('.inpostoperasi-id').val()),
            pasienmasukpenunjang_id: parseInt($('.pasienmasukpenunjang-id').val()),
            obatalkes_id: $('#instrumen-select_instrumen').val(),
            obatalkes_nama: data.text,
            satuan_id: data.satuankecil_id,
            persediaan: data.qty_stok,
            tambahan: tambahan,
            terpakai: terpakai,
            sisa: (parseInt(data.qty_stok) + tambahan) - terpakai,
        }
        $.ajax({
            url: '/bedah/informasi-pasien-operasi/add-instrumen',
            method: 'POST',
            data: _form,
            beforeSend: () => {
                $('#add-instrumen').attr('disabled', true)
            },
            success: (result) => {
                $('#instrumen-select_instrumen').val(null).trigger('change')
                $('#instrumen-tambahan_instrumen').val(0)
                $('#instrumen-terpakai_instrumen').val(0)
                $('#add-instrumen').attr('disabled', false)

                _tableinstrumen.draw()
            },
            error: (xhr) => {
                const error = typeof xhr.responseJSON.error != 'undefined' ? xhr.responseJSON.error : null;
                if (error) {
                    $.each(error, (k, v) => {
                        $(`#instrumen-${k}_instrumen`).closest('.col-md-3').append(`<p class="text-error-message" style="color: red">${v[0]}</p>`)
                    })
                    setTimeout(() => {
                        $('.text-error-message').remove()
                    }, 3000);
                }
                $('#add-instrumen').attr('disabled', false)
            }
        })
    })
    $('.select-penunjangkhusus').select2({
        placeholder: '',
        minimumInputLength: 3,
        ajax: {
            url: '/bedah/informasi-pasien-operasi/get-jenis-alat',
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
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
            url: '/bedah/informasi-pasien-operasi/get-pegawai',
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
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
    $('input[name="IntraOperasiForm[is_diserahkan]"]').change(function () {
        if ($(this).val() == 1) {
            if ($('.penerima-pemberi').hasClass('hidden')) {
                $('.penerima-pemberi').removeClass('hidden')
            }
        } else {
            if (!$('.penerima-pemberi').hasClass('hidden')) {
                $('.penerima-pemberi').addClass('hidden')
                $('.penerima-txt').val('')
                $('.select-pegawai-pemberi').val('').trigger('change')
            }
        }
    })
    $('input[name="IntraOperasiForm[is_jaringantubuh]"]').change(function () {
        if ($(this).val() == 1) {
            if ($('.pa-jaringan-tubuh').hasClass('hidden')) {
                $('.pa-jaringan-tubuh').removeClass('hidden')
            }
        } else {
            if (!$('.pa-jaringan-tubuh').hasClass('hidden')) {
                $('.pa-jaringan-tubuh').addClass('hidden')
                $('#intraoperasiform-jenis_jaringan').val('')
                $('input[name="IntraOperasiForm[is_diserahkan]"]').prop('checked', false).trigger('change')
            }
        }
    })
    populateOptions(_opsipenunjang, '.select-penunjangkhusus')
    populateOptions(_opsipenerima, '.select-pegawai-pemberi')


    $('.collapse-click').on('click', function () {
        var _id = $(this).attr('id');
        var _obj = $('#' + _id);
        if (!_obj.hasClass('rotate-180')) {
            _obj.addClass('rotate-180')
        } else {
            _obj.removeClass('rotate-180')
        }
    })

    _tabletindakanluarbedah = $('#tindakan-luar-bedah-table').docoTabel({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        info: false,
        ajax: '/bedah/informasi-pasien-operasi/get-cache?cacheName=' + _cachetindakanluarbedah,
        columns: [{
            title: 'No',
            data: 'rowNum',
            searchable: false,
            orderable: false
        },
        {
            title: 'Nama Alat',
            data: 'tindakanluarbedah_nama',
        },
        {
            title: 'Qty',
            data: 'qtytindakan'
        },
        {
            title: 'Pegawai Input',
            data: 'pegawai_input',
        },
        {
            title: 'Aksi',
            data: 'aksi',
        },
        ]
    });

    $("#action-name-form").select2InfinityScroll({
        url: '/bedah/informasi-pasien-operasi/tindakan',
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                    kelaspelayanan_id: kelasPelayanan,
                    penjamin_id: penjaminId,
                    type: "pelayanan",
                }
            }
        }
    })

    $("#btn-add-tindakan").bind('click', () => {
        $.ajax({
            url: '/bedah/informasi-pasien-operasi/tambah-tindakan-luar-bedah',
            data: [{
                name: 'tindakanluarbedah_id',
                value: $("#action-name-form").val()
            },
            {
                name: 'qtytindakan',
                value: $("[name='qtytindakan']").val()
            },
            {
                name: 'tindakanluarbedah_nama',
                value: $("#action-name-form").select2('data').length > 0 ? $("#action-name-form").select2('data')[0].text : ''
            },
            {
                name: 'keyUnique',
                value: keyUnique
            }
            ],
            withoutScroll: true,
            method: 'POST',
            success: () => {
                _tabletindakanluarbedah.draw()
                $("[name='qtytindakan']").val('')
                $("#action-name-form").val(null).trigger('change')
            }
        })
    })

    $('.submit-intra-operasi-new').click(function(e) {
        e.preventDefault()
        var formData = $('#form-intra-operasi').serializeArray()
        var pasienPenunjnag = parseInt($('.pasienmasukpenunjang-id').val()) 
        $().docoForm("click", {
            url: `/bedah/informasi-pasien-operasi/intra-operasi?id=${pasienPenunjnag}`,
            data: formData,
            method: 'post',
            success: function(data) {
                var status = data.status;
                if(status == 200) {
                    $('#tab-operasi-head-1').trigger('click')
                }
            },
            error: function(error) {
                if(error != undefined) {
                    if(error.responseJSON.response?.is_nulldokter == true) {
                        $('#panel-data-list-detail-operasi').addClass('collapse in')
                        $('#red-corner').removeClass('hide')
                        $('#table-red-corner').addClass('red-corner-row')
                    }
                }
            }
        });
    })
})

$(document).on('keyup', 'input[name="dynamicProsentase"]', ({ currentTarget }) => {
    if ($(currentTarget).val() != '') {
        const valueInput = $(currentTarget).val().replace(/[^0-9.]/g, "") != '' ? parseInt($(currentTarget).val().replace(/[^0-9.]/g, "")) : 0
        $(currentTarget).val(valueInput)
    }
})
$(document).on('change', 'input[name="dynamicProsentase"]', ({ currentTarget }) => {
    if ($(currentTarget).val() != '') {
        const valueInput = parseInt($(currentTarget).val().replace(/[^0-9.]/g, ""))
        if (valueInput < 35) {
            $(currentTarget).val(35)
        } else if (valueInput > 50) {
            $(currentTarget).val(50)
        }
        $.ajax({
            url: `/bedah/informasi-pasien-operasi/update-prosentase-anestesi?inpostoperasi_id=${parseInt($('.inpostoperasi-id').val())}&pasienmasukpenunjang_id=${parseInt($('.pasienmasukpenunjang-id').val())}`,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                pegawai_id: $(currentTarget).data('pegawai'),
                value: $(currentTarget).val()
            })
        })
    }
})
$(document).on('keypress', 'input[name="dynamicProsentase"]', function (e) {
    if (e.keyCode == 13) {
        e.preventDefault();
        return false;
    }
});