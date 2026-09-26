let table;

$(document).on("click", "#lihat", function () {
    const urlTarget = $(this).attr(`data-target`);
    window.open(urlTarget, "_blank");
});

let _type = `rajal`;
$(document).ready(() => {
    table = $("#example").docoTabel({
        select: {
            style: "single",
            selector: "tr",
        },
        filter: true,
        sorting: [2, "desc"],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: false,
        scrollY: true,
        ajax:   
            baseUrl + "fisioterapi/informasi-pasien-fisioterapi/get-data-rajal",
        columnDefs: [
            {
                orderable: false,
                className: "select-checkbox",
                targets: 0,
            },
        ],
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: "",
            },
            {
                title: "",
                data: "rowNum",
                searchable: false,
                orderable: false,
            },
            {
                title: "Tanggal Pendaftaran",
                data: "tgl_pendaftaran",
                searchable: true,
                orderable: true,
                visible: true,
            },
            {
                title: "Nama Pasien / No. RM / No Pendaftaran",
                data: "nama_pasien",
                render: (data, type, row, meta) => {
                    let namaPasien = `<b>` + data + `</b>`;
                    return (
                        namaPasien +
                        " " +
                        "(" +
                        row.jeniskelamin +
                        ")" +
                        `</br>` +
                        row.tgl_lahir +
                        `</br>` +
                        row.no_pendaftaran + " / " + row.no_rekam_medik
                    );
                },
            },
            {
                title: "Status Periksa",
                name: "status_periksa_id",
                data: "status_periksa_nama",
            },
            {
                title: "Jenis Terapi",
                data: "jenispemeriksaanfisio_nama",
                orderable: false,
                render: (data, type, row, meta) => (data ? data : "-"),
            },
            {
                title: "Terapi",
                data: "terapi_nama",
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) => (data ? data : "-"),
            },
            {
                title: "Terapis",
                data: "terapis_nama",
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) => (data ? data : "-"),
            },
            {
                title: "Dokter Penanggung Jawab",
                data: 'dpjp_id',
                render: (columnData, row, data) => {
                    var dpjpId = data.dpjp_id ? data.dpjp_id : ''
                    var dpjpNama = data.dokterdpjp_nama ? data.dokterdpjp_nama : ''
                    var statusPeriksaId = data.status_periksa_id ? data.status_periksa_id : ''
                    var _disabled = statusPeriksaId == dataFilter.statusPulang ? "disabled" : ""
                    var penunjangId = data.pasienmasukpenunjang_id
                    if(!penunjangId) {
                        _disabled = 'disabled'
                    }
                    return `<select 
                        name="dpjp_id" 
                        class="form-control input-sm selectDpjp"
                        data-id="${dpjpId}"
                        data-pendaftaran_id="${data.pendaftaran_id}"
                        ${_disabled}>
                        <option value="${dpjpId}">${dpjpNama}</option></select>`
                }
            },
            {
                title: "Cara Bayar / Penjamin",
                data: "carabayar_id",
                name: "carabayar_id",
                render: (data, type, row, meta) => {
                    return row.carabayar_nama + " / " + row.penjamin_nama;
                },
            },
            {
                title: "Status Bayar",
                data: "statusbayar_id",
                searchable: true,
                render: (data, type, row, meta) => {
                    if (row.terapis_nama == null) {
                        return "Belum Lunas";
                    } else {
                        return row.statusbayar_nama;
                    }
                },
            },
        ],
        drawCallback: () => {
            $("select[name='dpjp_id']").each(function () {
                const selectedId = $(this).data("id");
                const dokterList = dataFilter.listDokter.filter(item => item.id !== 'Semua');
                $(this).select2({
                    data: dokterList,
                    width: 'resolve'
                });
                if (selectedId) {
                    $(this).val(selectedId).trigger("change");
                }
            });
            $("select[name='dpjp_id']").on('change', ({ currentTarget }) => {
                const selectedId = $(this).data("id");
                if ($(currentTarget).val() != '') {
                    if($(currentTarget).val() != selectedId) {
                        const { pendaftaran_id } = $(currentTarget).data()
                        $.ajax({
                            url: '/fisioterapi/informasi-pasien-fisioterapi/update-dpjp',
                            method: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                pendaftaran_id,
                                pegawai_id: $(currentTarget).val()
                            }),
                            success:function(res) {
                                if(typeof res.meta.message != 'undefined') {
                                    docoNotification('success', 'Proses Berhasil', res.meta.message);
                                }
                                table.draw();
                            }
                        })
                    }
                }
            })
        },
    });

    document.getElementById("data-pasien").innerText = "Data Pasien";
    document.getElementById("status-bayar").innerText = "Status Bayar";
    $(".dataTables_filter").hide();

    $(".filter-form").datatableBootstrapFilter(
        table,
        [
            [
                2,
                '<div class="input-group"><input type="text" value=".date("d-M-Y", strtotime("-1 months"))." id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value=".date("d-M-Y")."  id="rangeDemoFinish" class="form-control endDate" readonly="readonly"/><input type="text" style="display:none" class="targetDate"></div>',
            ],
            [
                3,
                '<div class="form-group"><input type="text" class="form-control" id="namaPasien" placeholder="Cari Berdasarkan Nama / No Rekam Medik / No Pendaftaran"></div>',
            ],
            [
                5,
                '<div class="form-group"><select class="form-control" name="jenis_terapi" id="jenisTerapi"></select></div>',
            ],
            [
                8,
                '<div class="form-group"><select class="form-control" name="dokter_perujuk" id="dokterPerujuk"></select></div>',
            ],
            [
                4,
                '<div class="form-group"><select class="form-control" name="status" id="status"></select></div>',
            ],
            [
                9,
                '<div class="form-group"><select class="form-control" name="caraBayar" id="caraBayar"></select></div>',
            ],
            [
                10,
                '<div class="form-group"><select class="form-control" name="statusBayar" id="statusBayar"></select></div>',
            ],
            
        ],
        {
            2: 0,
            3: 1,
            5: 2,
            8: 3,
            4: 4,
            9: 5,
            10: 6,
            
        },
        true
    );

    dateRangeHelper(".startDate", ".endDate", ".targetDate");

    $("#dokterPerujuk").select2({
        placeholder: "Cari Berdasarkan Dokter Perujuk",
        data: dataFilter.listDokter,
    });

    $("#jenisTerapi").select2({
        placeholder: "Cari Berdasarkan Jenis Terapi",
        data: dataFilter.listjenisTerapi,
    });

    $("#status").select2({
        placeholder: "Cari Berdasarkan Status",
        data: dataFilter.listStatusRajal,
    });

    $("#statusBayar").select2({
        placeholder: "Cari Berdasarkan Status Bayar",
        data: dataFilter.listStatusBayar,
    });

    $("#caraBayar").select2({
        placeholder: "Cari Berdasarkan Cara Bayar",
        data: dataFilter.listCaraBayar,
    });

    $("#dokterPerujuk").val("Semua").trigger("change");
    $("#caraBayar").val("Semua").trigger("change");
    $("#statusBayar").val("Semua").trigger("change");
    $("#status").val(1).trigger("change");
    $("#jenisTerapi").val('').trigger("change");

    $(document).on("click", ".data-reset", function () {
        $("#dokterPerujuk").val("Semua").trigger("change");
        $("#caraBayar").val("Semua").trigger("change");
        $("#statusBayar").val("Semua").trigger("change");
        $("#status").val(1).trigger("change");
        $("#jenisTerapi").val('').trigger("change");
    });

    $("#example tbody").on("click", "tr", function () {
        const data = table.row(".selected").data();
        if (!data) {
            $("#periksa").attr("disabled", true);
            $("#lihat").attr("disabled", true);
            $("#history").attr("disabled", true);
            return false;
        }
        statusPeriksa = data.status_periksa_id;
        const pendaftaranId = data.pendaftaran_id_enc;
        const programFisioId = data.programterapi_id_enc;
        const urlPeriksa = `/fisioterapi/pemeriksaan?pendaftaran_id=${pendaftaranId}&program_terapi_id=${programFisioId}`;
        const urlLihat = `/fisioterapi/pemeriksaan?pendaftaran_id=${pendaftaranId}&program_terapi_id=${programFisioId}&readonly=true`;
        $(`#lihat`).attr(`data-target`, urlLihat);
        const localType = $(".nav-link.active").attr("data-type");
        const statusPeriksaSudahPeriksa = 3;
        const statusPeriksaSedangPeriksa = 2;
        const statusPeriksaPenunjang = data.status_periksa;
        if (localType == "ranap") {
            const urlPeriksaRanap = `/fisioterapi/pemeriksaan-ranap/index?pendaftaran_id=${pendaftaranId}&program_terapi_id=${programFisioId}`;
            const urlLihatRanap = `/fisioterapi/pemeriksaan-ranap/index?pendaftaran_id=${pendaftaranId}&program_terapi_id=${programFisioId}&readonly=true`;
            $(`#lihat`).attr(`data-target`, urlLihatRanap);
            const isDone = statusPeriksaPenunjang == statusPeriksaSudahPeriksa;
            const isProgress =
                statusPeriksaPenunjang == statusPeriksaSedangPeriksa;
            if (isDone || isProgress) {
                $("#lihat").attr("disabled", false);
                if (isDone) $("#periksa").attr("disabled", true);
                if (isProgress) $("#periksa").attr("disabled", false);
            } else {
                $("#lihat").attr("disabled", true);
                $("#periksa").attr("disabled", false);
            }
        } else {
            if (data.status_periksa_id == 4 || data.status_periksa_id == 487) {
                $("#periksa").attr("disabled", true);
            } else {
                $("#periksa").attr("disabled", false);
            }
            if (data.status_periksa_id == 4 || data.status_periksa_id == 487) {
                $("#lihat").attr("disabled", false);
            } else {
                $("#lihat").attr("disabled", true);
            }
        }

        $("#history").attr("disabled", false);
    });

    $("#periksa").on("click", function (e) {
        e.preventDefault();
        const data = table.row(".selected").data();
        primaryKey = data.primary;
        programterapi_id = data.programterapi_id;
        pasien_id = data.pasien_id;
        _type = $("a.active").attr("data-type");
        $(this).attr(
            "action",
            "/fisioterapi/informasi-pasien-fisioterapi/pilih-program?pasien_id=" +
                pasien_id +
                "&pendaftaran_id=" +
                primaryKey +
                "&jenis_pelayanan=" +
                _type
        );
        $(this).attr("data-target", "#modal_backdrop");
        $(this).attr("data-toggle", "modal");
        $(this).attr("data-width", "80%");
    });

    $("#history").on("click", function (e) {
        e.preventDefault();
        const data = table.row(".selected").data();
        primaryKey = data.primary;
        pasien_id = data.pasien_id;
        _type = $("a.active").attr("data-type");
        
        $(this).attr(
            "action",
            "/fisioterapi/informasi-pasien-fisioterapi/history-fisioterapi?pasien_id=" +
                pasien_id +
                "&pendaftaran_id=" +
                primaryKey +
                "&jenis_pelayanan=" +
                _type
        );
        $(this).attr("data-target", "#modal_backdrop");
        $(this).attr("data-toggle", "modal");
        $(this).attr("data-width", "50%");
    });

    $(".nav-link").on("click", function () {
        let _type = $(this).attr("data-type");
        const tableId = "example";
        const tableElement = $(`#${tableId}`).DataTable();
        tableElement.clear();
        if (_type == "ranap") {
            showLoader();
            window.location.href =
                "/fisioterapi/informasi-pasien-fisioterapi-ranap";
        } else {
            showLoader();
            window.location.href = "/fisioterapi/informasi-pasien-fisioterapi";
        }
    });
});
