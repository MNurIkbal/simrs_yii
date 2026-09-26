var table;

// Event Ready
$(document).ready(function() {
    moment.locale("en");
    var _rujukanRs = 1
    var _rujukanMasuk = 2
    var _aps = 3
    var _rujukanKeluar = 4
    var _jenis_rujukan = [
        {
           id: _aps,
           text: 'APS'
        },
        {
           id: _rujukanMasuk,
           text: 'Rujukan Masuk'
        },
        {
           id: _rujukanKeluar,
           text: 'Rujukan Keluar'
        },
        {
           id: _rujukanRs,
           text: 'Rujukan RS'
        },
    ]
  
    table = $("#example").docoTabel({
        select: {
            style: "os",
            selector: "tr"
        },
        filter: false,
        sorting: [[1, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: false,
        scrollY: true,
        ajax: {
            url: baseUrl + "radiologi/lap-waktu-tunggu-pasien-rad/get-data",
        },
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                   var tableInfo = table.page.info()
                   return tableInfo.start + rowAdditionalData.row + 1
                }
            },
            {
                title: "Tanggal Rujukan",
                data: "tglmasukpenunjang",
                render: (data) => {
                    return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY hh:mm:ss")
                }
            },
            {
                title: "No Pendaftaran",
                data: "no_pendaftaran",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Pasien",
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData) => {
                    return `
                       <p style="margin-bottom: 2px"> ${rowData.nama_pasien != null ? rowData.nama_pasien : ''} (${rowData.jeniskelamin != null ? rowData.jeniskelamin : ''}) </p>
                       <p style="margin-bottom: 2px"> ${rowData.no_rekam_medik != null ? rowData.no_rekam_medik : ''} </p>
                       <p style="margin-bottom: 2px"> ${rowData.tanggal_lahir != null ? moment(rowData.tanggal_lahir).format("DD MMM YYYY") : ''} </p>
                    `
                }
            },
            {
                title: "Dokter Radiologi",
                data: "dokter",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Jenis Pemeriksaan",
                data: "jenispemeriksaanrad_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Nama Pemeriksaan",
                data: "daftartindakan_nama",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Jenis Rujukan",
                data: "jenis_rujukan",
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Asal Rujukan / Nama RS",
                data: "rujukan",
                orderable: false,
                render: (data, rowElement, rowData) => {
                    let _jenisRujukanId = rowData.jenis_rujukan_id;
                    if(_jenisRujukanId == _rujukanKeluar) {
                        return `${rowData.asalrujukan_nama != null ? rowData.asalrujukan_nama : "-"} / ${rowData.rujukandari_nama != null ? rowData.rujukandari_nama : "-"}`
                    } else if(_jenisRujukanId == _rujukanRs) {
                        return `${rowData.asalrujukan_nama != null ? rowData.asalrujukan_nama : "-"}`
                    } else if(_jenisRujukanId == _rujukanMasuk) {
                        return `${rowData.asalrujukan_nama != null ? rowData.asalrujukan_nama : "-"} / ${rowData.rujukandari_nama != null ? rowData.rujukandari_nama : "-"}`
                    } else {
                        return ``
                    }
                }
            },
            {
                title: "Tanggal Pendaftaran",
                data: "tglmasukpenunjang",
                render: (data) => {
                    return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY hh:mm:ss")
                }
            },
            {
                title: "Tanggal Persetujuan",
                data: "tglpersetujuan",
                render: (data) => {
                    return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY hh:mm:ss")
                }
            },
            {
                title: "Tanggal Ambil Foto",
                data: "tgl_ambilfoto",
                render: (data) => {
                    return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY hh:mm:ss")
                }
            },
            {
                title: "Tanggal Expertise",
                data: "tgl_hasilrad",
                render: (data) => {
                    return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY hh:mm:ss")
                }
            },
            {
                title: "Waktu Tunggu Pendaftaran Expertise",
                data: "waktu_tunggu_tanggal_expertise",
                searchable: false,
                orderable: false,
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Waktu Tunggu Pemeriksaan Radiologi",
                data: "waktu_tunggu_ambil_foto_expertise",
                searchable: false,
                orderable: false,
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
        ],
        formFilters: [
            {
                fieldName: 'tglmasukpenunjang',
                label: 'Tanggal Rujukan',
                type: {
                   name: 'rangeDate',
                }
            },
            'no_pendaftaran',
            {
                fieldName: 'nama_pasien',
                label: 'No.RM / Nama Pasien'
            },
            {
                fieldName: 'daftartindakan_id',
                label: 'Nama Pemeriksaan',
                type: {
                   name: 'dropdownScroll',
                   url: "/radiologi/lap-waktu-tunggu-pasien-rad/filters",
                   additionalPayload: {
                      type: 'pemeriksaan',
                   }
                }
            },
            {
                fieldName: 'tglpersetujuan',
                label: 'Tanggal Persetujuan',
                type: {
                    name: 'rangeDate',
                    payload: {
                        allDate: true,
                    }
                }
            },
            {
                fieldName: 'jenis_rujukan_id',
                label: 'Jenis Rujukan',
                type: {
                   name: 'select',
                   payload: _jenis_rujukan,
                }
            },
            {
                fieldName: 'asalrujukan_nama',
                label: 'Asal Rujukan',
                type: {
                   name: 'dropdownScroll',
                   url: "/radiologi/lap-waktu-tunggu-pasien-rad/filters",
                   additionalPayload: {
                      type: 'asal_rujukan',
                   }
                }
            },
            {
                fieldName: 'rujukandari_nama',
                label: 'Nama RS Rujukan',
                type: {
                   name: 'dropdownScroll',
                   url: "/radiologi/lap-waktu-tunggu-pasien-rad/filters",
                   additionalPayload: {
                      type: 'rs_rujukan',
                   }
                }
            },
        ],
    });
    $(document).on("click", ".btn-reset", function (e) {
        const tableId = "example";
        const element = $(`#filter-section__${tableId}`)
        const formWrapper = $(`#form-filter__${tableId}`)
        element.find('input').val('')
        element.find('select').val(null).trigger('change')
        element.find('#tglmasukpenunjang-startDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
        element.find('#tglmasukpenunjang-endDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
        const tableElement = $(`#${tableId}`).DataTable()
        showLoader()
        tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
        tableElement.ajax.url("/radiologi/lap-waktu-tunggu-pasien-rad/get-data").load()
    });
  
    $('#btn-search__example').css('display', 'none');
    $('#btn-reset__example').css('display', 'none');
});