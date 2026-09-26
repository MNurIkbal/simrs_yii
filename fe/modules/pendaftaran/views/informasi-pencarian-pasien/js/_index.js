var { columnsLabels, formFilters, options } = pageVars;
var table = null;
var JENIS_IDENTITAS_NIK = 94;
var JENIS_IDENTITAS_BPJS = 601;

$(document).ready(function() {
    $(function(){
        $(".daterange").daterangepicker({
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD MMM YYYY"
            }
        });
    })

    table = $("#table-informasi-pasien").docoTabel({
        filter: true,
        columnDefs: [ {
            orderable: false,
            className: "select-checkbox",
            targets:   0
        }],
        select: {
            style:    "os",
            selector: "tr"
        },
        order: [[ 3, "desc" ]],
        sorting: [[3, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        scrollY: false,
        ajax: baseUrl+"pendaftaran/informasi-pencarian-pasien/get-data",
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: "",
            },
            {
                title: columnsLabels.number,
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: columnsLabels.tglRekamMedik,
                data: "tgl_rekam_medik",
                searchable: false
            },
            {
                title: columnsLabels.noRekamMedik,
                data: "no_rekam_medik"
            },
            {
                title: "Nama Pasien / No. Handphone",
                data: "nama_pasien",
                visible: false
            },
            {
                title: "Nama Pasien",
                data: "display_name",
                searchable: false,
                orderable: false
            },
            {
                title: columnsLabels.tglLahir,
                data: "tanggal_lahir"
            },
            {
                title: columnsLabels.jenisKelamin,
                data: "jenis_kelamin",
                name: "jeniskelamin"
            },
            {
                title: columnsLabels.nikPasien,
                data: "no_identitas_pasien",
                orderable: false,
                render: function (data, type, row, meta) {
                    if (data == null || data == undefined || data == '') {
                        let additionalData = row.additional_pasien ? JSON.parse(row.additional_pasien) : [];
                        let objNIK = additionalData.find((el) => el.jenisidentitas == JENIS_IDENTITAS_NIK);
                        if (objNIK) {
                            return objNIK?.no_identitas_pasien
                        }
                    }
                    return data;
                }
            },
            {
                title: columnsLabels.alamatPasien,
                data: "alamat_pasien"
            },
            {
                title: columnsLabels.provinsi,
                data: "propinsi_nama",
                name: "propinsi_id"
            },
            {
                title: columnsLabels.kabupaten,
                data: "kabupaten_nama",
                name: "kabupaten_id"
            },
            {
                title: columnsLabels.kecamatan,
                data: "kecamatan_nama",
                name: "kecamatan_id"
            },
            {
                title: columnsLabels.namaPetugas,
                data: "petugas",
                name: "pembuat_nama",
                orderable: false
            },
            {
                title: columnsLabels.alasanPerubahan,
                data: "alasan_ubahdata",
                searchable: false
            },
            {
                title: columnsLabels.noMobilePasien,
                data: "nomor_pasien",
                searchable: false,
                orderable: false
            },
            {
                title: "No. BPJS",
                data: "nopeserta_bpjs",
                orderable: false,
                render: function (data, type, row, meta) {
                    if (data == null || data == undefined || data == '') {
                        let additionalData = row.additional_pasien ? JSON.parse(row.additional_pasien) : [];
                        let objBPJS = additionalData.find((el) => el.jenisidentitas == JENIS_IDENTITAS_BPJS);
                        if (objBPJS) {
                            return objBPJS?.no_identitas_pasien
                        }
                    }
                    return data;
                }
            },
        ],
        infoCallback: function( settings, start, end, max, total, pre ) {
            return pre + " (Filtered) from total " + settings.json.totalData;
        },
        rowCallback: (rowElement, data) => {
            if (data.is_catatanpenting) {
                $($(rowElement).find('td')[4]).css('background-color', '#d64541')
                $($(rowElement).find('td')[4]).css('color', 'white')
            }
        },
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        // [2, formFilters.tglRekamMedikFilter],
        // [4, formFilters.namaPasienFilter],
        [7, formFilters.jenisKelaminFilter],
        [10,formFilters.provinsiFilter],
        [11,formFilters.kabupatenFilter],
        [12,formFilters.kecamatanFilter],
        [6,formFilters.tglLahirFilter],
    ],
    {
        3:0,
        4:1,
        6:2,
        9:3,
        10:4,
        11:5,
        12:6,
        7:7,
        8:8,
        13:9,
        16:10

    });
});


$(document).on('click',table,function(){
    var tableData = table.row(".selected").data();

    if(typeof tableData !== "undefined" && isAksesUpdate)
    {
       $("#btn-update").prop('disabled',false)
    } else {
        $("#btn-update").prop('disabled',true)
    }

    if(typeof tableData !== "undefined") {
        $('#btn-upload-dokumen').prop('disabled', false);
    } else {
        $('#btn-upload-dokumen').prop('disabled', true);
    }
 })

$(".btn-cetak-kartu").click(function(e){
    e.preventDefault()
    var tableData = table.row(".selected").data();
    if(typeof tableData !== "undefined"){
        var primary = tableData.primary;
        var url = window.location.origin;
        var target = $(this).attr("data-target")+"id=";
        window.open(url+target+primary);
    }else{
        docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
    }

})
$(".btn-cetak-data-pasien").click(function(e){
    e.preventDefault()
    var tableData = table.row(".selected").data();
    if(typeof tableData !== "undefined"){
        var primary = tableData.primary;
        var url = window.location.origin;
        var target = $(this).attr("data-target");
        window.open(url+target+primary);
    }else{
        docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
    }

})
// Options
var oneDay = 24*60*60*1000;
var rangeDemoFormat = "%e-%b-%Y";
var rangeDemoConv = new AnyTime.Converter({format:rangeDemoFormat});

$(document).on("click", "#btn-riwayat-pasien", function() {
    if (typeof table.row(".selected").data() !== "undefined") {
        let norm = table.row(".selected").data().no_rekam_medik;
        if (norm) {
                $("#btn-hidden-riwayat-pasien").attr("action", "/igd/riwayat-pasien/history-patient?norm=" + norm + "&instalasi=" + options.instalasiId + "&modal=is_modal");
                $("#btn-hidden-riwayat-pasien").trigger("click");
            } else {
                $("#btn-hidden-riwayat-pasien").attr("action", "");
            }
    } else {
        docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        return true;
    }
});

$(document).on("click", "#btn-history-terra-medik", function() {
    if (typeof table.row(".selected").data() !== "undefined") {
        let pasien = table.row(".selected").data().id_pasien;
        if (pasien) {
                $("#btn-hidden-history-terra-medik").attr("action", "/ranap/riwayat-pasien/modal-history-terra-medik?pasien_id=" + pasien,true,);
                $("#btn-hidden-history-terra-medik").trigger("click");
            } else {
                $("#btn-hidden-history-terra-medik").attr("action", "");
            }
    } else {
        docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        return true;
    }
});

$('#export-excel').on('click', function (e) {
    e.preventDefault()
    let _data = table.ajax.params()
    $(this).attr({
        'action': baseUrl + 'pendaftaran/informasi-pencarian-pasien/excel-popup?' + $.param(_data),
        'data-target': '#modal_backdrop',
        'data-width': '75%',
        'data-toggle': 'modal'
    })
})
