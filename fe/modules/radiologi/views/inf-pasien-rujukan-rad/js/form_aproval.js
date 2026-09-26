// Global Var
var table;

// Event click
$(document).on("click", ".data-reset", function () {
    // Reload table
    table.draw();

    // Disable edit and delete button
    $("#btn-edit").prop("disabled", true);
    $("#btn-delete").prop("disabled", true);
});

// Event click
$(document).on("click", "#tb-inf-pasien-rujukan-rad tbody tr", function () {
    // Try catch
    try {
        // Get primary
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
    } catch (e) {
        // Make it false
        primaryKey = false;
    }

    // Assign to ubah
    $("#btn-edit").attr("action", updateUrl + primaryKey);

    // Check class selected
    if ($('#tb-inf-pasien-rujukan-rad tr.selected').length == 0) {
        // Disable edit button
        $("#btn-edit").prop("disabled", true);
        $("#btn-delete").prop("disabled", true);
    }
    else {
        // Disable edit button
        $("#btn-edit").prop("disabled", false);
        $("#btn-delete").prop("disabled", false);
    }
});

// Event Ready
$(document).ready(function () {
    $('#btn-kembali').on('click', function () {
        $(location).attr('href', redirectUrl);
    });

    table = $("#tableDetail").docoTabel({
        filter: false,
        sorting: [[1, "asc"]],
        paging: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "radiologi/inf-pasien-rujukan-rad/get-data-order-pemeriksaan?pasienkirimkeunitlain_id=" + pasienKirimKeUnitLainId,
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Jenis Pemeriksaan",
                data: "jenispemeriksaanrad_nama",
                searchable: false,
            },
            {
                title: "Nama Pemeriksaan",
                data: "daftartindakan_nama",
                searchable: false,
            },
            {
                title: "Qty",
                data: "qtypermintaan",
                searchable: false,
            },
            {
                title: "Cyto",
                data: "is_cyto",
                searchable: false,
                orderable: false
            },
            {
                title: "Dokter",
                data: "dokter",
                searchable: false,
                orderable: false
            },
            {
                title: "Approve",
                data: "approve",
                searchable: false,
                orderable: false
            },
        ],
        drawCallback: function(settings) {
            $("select[name=\"dokter\"]").select2InfinityScroll({
                url: '/radiologi/inf-pasien-rujukan-rad/dokter-list'
            })
            var dataDokter = {
                id: _dokterRujukId,
                text: _dokterRujukNama
            };
            var newOption = new Option(dataDokter.text, dataDokter.id, true, true);
            $('select[name=\"dokter\"]').append(newOption).trigger('change');
        }
    });

    $('#btn-simpan').on('click', function () {
        var _dataForm = $('#form').serializeArray();
        const detail = []
        let valid = 1
        var arrPemeriksaan = [];
        $(".approveCheck:checked").each(function () {
            var daftartindakanId = $(this).attr("data-value");
            arrPemeriksaan.push(daftartindakanId);
        })
        var countChecked = arrPemeriksaan.length;
        if (countChecked == 0) {
            valid = 0
            docoNotification('error', 'Terjadi kesalahan pada input.', 'Tidak ada Pemeriksaan yang akan di Approve!')
        }
        $('#tableDetail tbody tr').each((index, element) => {
            $(element).find('.approveCheck:checked').each(function () {
                var daftartindakan_id = $(this).data('value');
                var idpenunjang = $(this).data('idpenunjang');
                var idunitlain = $(this).data('idunitlain');
                var namatindakan = $(this).data('namatindakan');
                var id_dokter = $(element).find('select[name="dokter"]').val();
                
                detail.push({
                    permintaankepenunjang_id: idpenunjang,
                    pasienkirimkeunitlain_id: idunitlain,
                    dokter_id: id_dokter,
                    daftartindakan_id: daftartindakan_id,
                    nama_tindakan: namatindakan,
                    catatan_dokterpengirim: $('.catatan_dokter').val()
                })
                if (id_dokter == null || id_dokter == '') {
                    valid = 0
                    docoNotification('error', 'Terjadi kesalahan pada input.', 'Dokter untuk Pemeriksaan : </br><strong>' + namatindakan + '</strong> </br> Belum Dipilih')
                }
            });
        })
        _dataForm.push({
            name: 'detail',
            value: JSON.stringify(detail)
        })
        if (valid == 1) {
            $(this).docoForm('click', {
                url: "/radiologi/inf-pasien-rujukan-rad/approve?id=" + pasienKirimKeUnitLainId,
                method: "POST",
                type: "json",
                data: _dataForm,
                success: function (data) {
                    docoNotification('success', 'Sukses', 'Anda akan dialihkan ke halaman informasi pasien rujukan radiologi');
                    $('#btn-simpan').prop("disabled", true)
                    setTimeout(() => {
                        window.location.replace('/radiologi/inf-pasien-rujukan-rad')
                    }, 2000);
                }
            });
        }
    });

    // $("select[name=\"dokter\"]").select2InfinityScroll({
    //     url: '/radiologi/inf-pasien-rujukan-rad/dokter-list'
    // })
    
});