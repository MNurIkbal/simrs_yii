var listDetailTindakanPaket = phpVars.listDetailTindakanPaket;
var listTindakanFormData = [];
var tabelTindakan;

$('#jumlah-pilihan').on('change', function() {
    $('#jumpil').remove();
    var jumlahPilihan = $(this).val();
    if(jumlahPilihan > 0 && jumlahPilihan < 11){
        jumlahPilihan = $(this).val();
    }else if(jumlahPilihan > 10){
        $(this).val(10);
        jumlahPilihan = $(this).val();
        $('#jumlah-pilihan').after('<p class="text-error" id="jumpil" style="color: red">Maksimal Jumlah Pilihan Adalah 10</p>');
    }else{
        $(this).val(1);
        jumlahPilihan = $(this).val();
        $('#jumlah-pilihan').after('<p class="text-error" id="jumpil" style="color: red">Minimal Jumlah Pilihan Adalah 1</p>');
    }
});

$('#jumlah-frekuensi').on('change', function() {
    $('#jumsi').remove();
    var frekuensi = $(this).val();
    if(frekuensi > 0 && frekuensi < 11){
        frekuensi = $(this).val();
    }else if(frekuensi > 10){
        $(this).val(10);
        frekuensi = $(this).val();
        $('#jumlah-frekuensi').parent('.input-group').after('<p class="text-error" id="jumsi" style="color: red">Maksimal Jumlah Frekuensi Adalah 10</p>');
    }else{
        $(this).val(1);
        frekuensi = $(this).val();
        $('#jumlah-frekuensi').parent('.input-group').after('<p class="text-error" id="jumsi" style="color: red">Minimal Jumlah Frekuensi Adalah 1</p>');
    }
});

$('#input-kode-paket-fis').on('change', function() {
    $('#kopet').remove();
    var kodepaket = $(this).val().length;
    if(kodepaket > 0 && kodepaket < 11){
        kodepaket = $(this).val().length;
    }else{
        kodepaket = $(this).val().length;
        selisih = kodepaket - 10;
        this.value = this.value.slice(0, -selisih);
        kodepaket = $(this).val().length;
        $('#input-kode-paket-fis').after('<p class="text-error" id="kopet" style="color: red">Maksimal Panjang Kode Paket Adalah 10 Huruf</p>');
    }
});

$.each(listDetailTindakanPaket, (k, v) => {
    var data = {
        id: v.daftartindakan_id,
        instalasi_id: null,
        instalasi_nama: null,
        kelompoktindakan_nama: v.kelompoktindakan_nama,
        ruangan_id: null,
        ruangan_nama: null,
        selected: true,
        text: v.daftartindakan_nama
    }
    appendListTindakanData(data);
});

function initSelect2Tindakan() {
    $('#auto-tindakan').select2({
        // minimumInputLength: 3,
        placeholder: 'Tambah Tindakan',
        allowClear: true,
        ajax: {
            url: `tindakan/get-tindakan`,
            data: function (params) {
                var query = {
                    is_fisioterapi : 1,
                    search: params.term,
                    type: 'public'
                }
                return query;
            }
        }
    });
    $('#auto-tindakan').on('select2:select', function (e) {
        const data = e.params.data;
        appendListTindakanData(data);
    });
}

function generateRemoveButton(id) {
    return `<button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeListTindakanData(${id})"><i class="fa fa-trash"></i></button>`;
}

function drawTableTindakan() {
    if (tabelTindakan) {
        tabelTindakan.clear();
        tabelTindakan.destroy();
    }
    tabelTindakan = $("#tabel-tampung-tindakan").docoTabel({
        destroy: true,
        filter: false,
        paging: false,
        serverSide: false,
        scrollCollapse: false,
        data: listTindakanFormData,
        columns: [
            {
                title: "No",
                searchable: false,
                orderable: false,
                render: function (data, type, full, meta) {
                    return meta.row + 1;
                }
            },
            { title: "Tindakan", data: "text", searchable: false },
            { title: "Kelompok", data: "kelompoktindakan_nama", searchable: false },
            {
                title: "Aksi",
                data: "aksi",
                render: function (data, type, row) {
                    return generateRemoveButton(row.id);
                }
            },
        ],
        drawCallback: function () { },
    });
}

function appendListTindakanData(data) {
    const collection = collect(listTindakanFormData);
    const findOne = collection.where('id', data.id);
    if (!findOne.isEmpty()) {
        docoNotification('error', 'Tindakan sudah ada !', `Tindakan ${data.text} sudah didaftarkan !`);
        return false;
    }
    listTindakanFormData.push(data);
    const listTindakanStringify = JSON.stringify(listTindakanFormData);
    $(`#list_tindakan`).val(listTindakanStringify);
    drawTableTindakan();
}

function removeListTindakanData(id) {
    const collection = collect(listTindakanFormData);
    const filtered = collection.whereNotIn('id', [id]).all();
    listTindakanFormData = [];
    listTindakanFormData = filtered;
    const listTindakanStringify = JSON.stringify(listTindakanFormData);
    $(`#list_tindakan`).val(listTindakanStringify);
    drawTableTindakan();
}

$(function () {
    initSelect2Tindakan();
    drawTableTindakan();
});