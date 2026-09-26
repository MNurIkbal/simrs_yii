var totalTarif = 0;
var listTarif = [];
var arrayJenisIdentitas = [];
$("#pj_tanggal_lahir, #tanggal_lahir, #tgl_konfirmasi, #tanggal_rujukan").pickadate({
    selectMonths: true,
    selectYears: 100,
    monthsFull: [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'Nopember',
        'Desember'
    ],
    formatSubmit: 'dd-mm-yyyy',
    format: 'dd mmmm yyyy'
});

$(document).on('change', '#pj_tanggal_lahir', function () {
    var umur = '';

    if ($("input[name='KunjunganForm[pj_tanggal_lahir]_submit']").val() != ''){
        umur = generateUmur($("input[name='KunjunganForm[pj_tanggal_lahir]_submit']").val());
    }

    $('#pj_umur').val(umur);
});

$(document).on('click', '.tambah-jenis', function(event) {
    var next = true;
    var html = $('.identitas:last').clone();
    arrayJenisIdentitas = [];
    html.find('span').remove();
    html.find('select').select2();
    html.find('.no_identitas_pasien').val(null);
    html.find('.tambah-jenis').html('<i class="fa fa-close"></i>');
    html.find('.tambah-jenis').removeClass('btn-info');
    html.find('.tambah-jenis').addClass('btn-danger');
    html.find('.tambah-jenis').addClass('hapus-jenis');
    html.find('.tambah-jenis').removeClass('tambah-jenis');
    html.find('.tambah-jenis').prop('id', null);

    $('.no_identitas_pasien').each(function(key, obj) {
        if (!$(this).val()) {
            next = false;

            $(this).parent().addClass('has-error');
            $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;No Identitas Pasien cannot be blank.');
            $(this).parent().find('.fa').addClass('fa-exclamation-circle');

            docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
        }
    });

    $('.jenis_identitas').each(function(key, obj) {
        if (!$(this).val()) {
            next = false;

            $(this).parent().addClass('has-error');
            $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Jenis Identitas cannot be blank.');
            $(this).parent().find('.fa').addClass('fa-exclamation-circle');

            docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
        }

        arrayJenisIdentitas.push($(this).val());
    });

    if (next) {
        // Append in the last
        $('.identitas:last').after(html);
    }
});

$(document).on('click', '.hapus-jenis', function(event) {
    $(this).parent().parent().remove();
});

$(document).ready(function() {
    $("#tgl_pendaftaran").val(function() {
        var d = new Date();

        return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);;
    });

    $("#tanggal_sep").val(function() {
        var d = new Date();

        return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear();
    });

    $("input[name='KunjunganForm[is_pj]']").on('change', function() {
        var is_pj = $("input[name='KunjunganForm[is_pj]']:checked").val();

        if (is_pj) {
            $('#form-pj').show();
        } else {
            $('#form-pj').hide();
        }
    });

    $('#ruangan_id').on('depdrop:afterChange', function (event) {
        $(this).trigger('change').trigger('depdrop:change');
        getKarcis();
    });

    $('#kelaspelayanan_id').on('depdrop:afterChange', function (event) {
        $(this).trigger('change').trigger('depdrop:change');
        getKarcis();
    });

    $("#kelastanggungan_id").prepend("<option selected=></option>").select2({
        placeholder: "Kelas Tanggungan"
    });
    $("#carabayar_id").prepend("<option selected=></option>").select2({
        placeholder: "Cara Bayar"
    });
    $("#penjamin_id").prepend("<option selected=></option>").select2({
        placeholder: "Penjamin"
    });
    $("#asal_rujukan").prepend("<option selected=></option>").select2({
        placeholder: "Asal Rujukan"
    });
    $("#asalrujukan_id").prepend("<option selected=></option>").select2({
        placeholder: "Asal Rujukan"
    });
    $("#jenisidentitas").prepend("<option selected=></option>").select2({
        placeholder: "Jenis Identitas"
    });
    $("#ruangan_id").prepend("<option selected=></option>").select2({
        placeholder: "Ruangan"
    });
    $("#jeniskasuspenyakit_id").prepend("<option selected=></option>").select2({
        placeholder: "Jenis Kasus Penyakit"
    });
    $("#kelaspelayanan_id").prepend("<option selected=></option>").select2({
        placeholder: "Kelas Pelayanan"
    });
    $("#dokter_id").prepend("<option selected=></option>").select2({
        placeholder: "Dokter"
    });
    $("#keadaan_masuk").prepend("<option selected=></option>").select2({
        placeholder: "Keadaan Masuk"
    });
    $("#transportasi").prepend("<option selected=></option>").select2({
        placeholder: "Transportasi"
    });
    $("#pj_pengantar").prepend("<option selected=></option>").select2({
        placeholder: "Pengantar"
    });
    $("#pj_jk").prepend("<option selected=></option>").select2({
        placeholder: "Jenis Kelamin"
    });
    $("#pj_hubungan").prepend("<option selected=></option>").select2({
        placeholder: "Hubungan Keluarga"
    });
    $("#pj_jenis_identitas").prepend("<option selected=></option>").select2({
        placeholder: "Jenis Identitas"
    });
    $("#jenis_pelayanan").prepend("<option selected=></option>").select2({
        placeholder: "Pelayanan"
    });
    $("#jenis_kartu").prepend("<option selected=></option>").select2({
        placeholder: "Jenis Kartu"
    });
    $("#jenis_rujukan").prepend("<option selected=></option>").select2({
        placeholder: "Jenis Pencarian"
    });
    $("#jeniskelamin").prepend("<option selected=></option>").select2({
        placeholder: "Jenis Kelamin"
    });
    $("#rujukandari_id").prepend("<option selected=></option>").select2({
        placeholder: "Rujukan Dari"
    });
    $("#agama").prepend("<option selected=></option>").select2({
        placeholder: "Agama"
    });
    $("#pendidikan_id").prepend("<option selected=></option>").select2({
        placeholder: "Pendidikan"
    });
    $("#pekerjaan_id").prepend("<option selected=></option>").select2({
        placeholder: "Pekerjaan"
    });
    $("#suku_id").prepend("<option selected=></option>").select2({
        placeholder: "Suku"
    });
    $("#warga_negara").prepend("<option selected=></option>").select2({
        placeholder: "Kewarganegaraan"
    });

    $('#carabayar_id').on('change', function(){
        $('#jenis_rujukan').val(null).trigger('change');
        $('#asalrujukan_id').val(null).trigger('change');
        $('#jenis_pelayanan').val(null).trigger('change');
        $('#jenis_kartu').val(null).trigger('change');
        $('#no_asuransi').val(null);
        $('#no_kartu').val(null);
        $('#no_rujukan_f').val(null);
        $('#asal_rujukan').val(null).trigger('change');
        $('#namapemilikasuransi').val(null);
        $('#nomorpokokperusahaan').val(null);
        $('#namaperusahaan').val(null);
        $('#kelastanggungan_id').val(null).trigger('change');
        $("#status_konfirmasi").prop("checked", false);
        $('#no_rujukan').val(null);
        $('#rujukandari_id').val(null).trigger('change');
        $('#nama_perujuk').val(null);
    });

    $('#jenis_rujukan').on('change', function(){
        if (this.value == '1') {
            $('#form-asalrujukan_id').show();
            $('#form-norujukan').show();
            $('#form-rujukan').show();

            $('#form-jenislayanan').hide();
            $('#form-jeniskartu').hide();
            $('#form-nokartu').hide();

            $('#jenis_pelayanan').val(null).trigger('change');
            $('#jenis_kartu').val(null).trigger('change');
            $('#no_kartu').val(null);
        } else {
            $('#form-jenislayanan').show();
            $('#form-jeniskartu').show();
            $('#form-nokartu').show();

            $('#form-asalrujukan_id').hide();
            $('#form-norujukan').hide();
            $('#form-rujukan').hide();

            $('#no_rujukan_f').val(null);
            $('#asalrujukan_id').val(null).trigger('change');
            $('#no_rujukan').val(null);
            $('#rujukandari_id').val(null).trigger('change');
            $('#nama_perujuk').val(null);
            $('#tanggal_perujuk').data('value', null);
            $('#diagnosa_id').val(null).trigger('change');
        }
     });

     $('#asalrujukan_id').on('change', function(){
        if (this.value == '1') {
            $('#form-rujukan').hide();

            $('#no_rujukan').val(null);
            $('#rujukandari_id').val(null).trigger('change');
            $('#nama_perujuk').val(null);
            $('#tanggal_perujuk').data('value', null);
            $('#diagnosa_id').val(null).trigger('change');
        } else {
            $('#form-rujukan').show();
        }
     });

     $('#btn-advanced-option').on('click', function(){
         if ($("#advanced-option-icon").attr("class") == 'fa fa-chevron-down') {
            $("#advanced-option-icon").removeClass('fa fa-chevron-down');
            $("#advanced-option-icon").addClass('fa fa-chevron-up');
            
            $('#form-advanced-option').show();
         } else {
            $("#advanced-option-icon").removeClass('fa fa-chevron-up');
            $("#advanced-option-icon").addClass('fa fa-chevron-down');

            $('#form-advanced-option').hide();
         }
     });
});

function getKarcis() {
    var _params = {
        ruangan_id: $('#ruangan_id').val(),
        kp_id: $('#kelaspelayanan_id').val(),
        status: 1,
        penjamin_id: $('#penjamin_id').val()
    };
    var tbl;
    _params = $.param(_params);

    tbl = $('#tbl-karcis').docoTabel({
        filter: true,
        destroy: true,
        paging: false,
        sorting: [[0, 'asc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl + 'pendaftaran/daftar-rajal/get-karcis?' + _params,
        initComplete: function (row, data) {
            var api = this.api();

            $.each(api.rows().data(), function (key, val) {
                listTarif.push(val);
            });

            $('.check-aksi').on('click', function (event) {
                var _this = $(this);
                var isChecked = this.checked;
                var rowData = api.rows($(this).closest("tr"));
                var data = rowData.data()[0];
                var allData = api.rows().data();
                var cancelChecked = false;

                if (data.is_konsultasi) {
                    if (isChecked) {
                        allData.each(function(value, index) {
                            if (value.is_konsultasi == true && value.checked == true && value.daftartindakan_id != data.daftartindakan_id) {
                                cancelChecked = true;
                            }
                        });

                        if (cancelChecked) {
                            _this.parent().removeClass("checked");
                            _this.prop("checked", false);
                            rowData.data()[0].checked = false;
                            docoNotification("warning", "Peringatan!", "Tidak boleh memilih karcis konsultasi lebih dari satu.");
                        } else {
                            rowData.data()[0].checked = true;
                        }
                    } else {
                        rowData.data()[0].checked = false;
                    }
                }

                if (cancelChecked === false) {
                    var key = _this.attr('data-key');
                    if (_this.is(':checked')) {
                        total_tarif = parseInt(totalTarif) + parseInt(listTarif[key].harga_tariftindakan);
                    } else {
                        total_tarif = totalTarif - (listTarif[key].harga_tariftindakan);
                    }

                    totalTarif = total_tarif;
                    $('.kolom-total-tarif').html('Rp. ' + docoHelper.convertToRupiah(total_tarif));
                }
            });
        },
        drawCallback: () => {
            $('#tbl-karcis').find('.styled, .check-aksi input').uniform({
                radioClass: 'choice'
            });
            bindCheckboxWithSpace($('#tbl-karcis'))
        },
        fnFooterCallback: function (row, data, start, end, display) {
            var api = this.api();
            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            totalTarif = api
            .column(5)
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

            $(api.column(2).footer()).addClass('kolom-total-tarif').html('Rp. ' + docoHelper.convertToRupiah(totalTarif));
        },
        columns: [
            {
                title: 'No',
                data: 'number',
                searchable: false,
                orderable: false
            },
            {
                title: 'Karcis',
                data: 'daftartindakan_nama',
                searchable: false,
                orderable: false
            },
            {
                title: 'Konsultasi',
                data: 'konsultasi',
                searchable: false,
                orderable: false
            },
            {
                title: 'Harga',
                data: 'tmp_view',
                searchable: false,
                orderable: false
            },
            {
                title: '',
                data: 'aksi',
                searchable: false,
                orderable: false
            },
            {
                data: 'tmp_total',
                visible: false,
                searchable: false,
                orderable: false
            }
        ],

    });
    $('.dataTables_filter').hide();
}