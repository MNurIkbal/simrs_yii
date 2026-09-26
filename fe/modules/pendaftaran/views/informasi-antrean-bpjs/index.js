// Event Ready
$(document).ready(function() {
    // Generate Table
    table = $("#table-antreanbpjs").docoTabel({
        filter: true,
        columnDefs: [{
            orderable: false,
            className: "select-checkbox",
            targets: 0
        }],
        select: {
            style: "multi",
            selector: "tr"
        },
        ordering: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        /*stateSave: true,*/
        scrollX: true,
        ajax: baseUrl+"pendaftaran/informasi-antrean-bpjs/get-data",
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: "",
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Tanggal Pendaftaran",
                data: "tgl_pendaftaran"
            },
            {
                title: "No Pendaftaran",
                data: "no_pendaftaran"
            }, //2
            {
                title: "Kode Booking",
                data: "kodebooking"
            }, //3
            {
                title: "Nama Pasien",
                data: "nama_pasien"
            }, //4
            {
                title: "Jenis Pasien",
                data: "jenis_pasien"
            }, //5
            {
                title: "No BPJS",
                data: "nopeserta_bpjs"
            }, //6
            {
                data: "nomorreferensi",
                searchable: false
            },
            {
                data: "noantrean",
                searchable: false
            },
            {
                data: "task1",
                searchable: false
            },
            {
                data: "task2",
                searchable: false
            },
            {
                data: "task3",
                searchable: false
            },
            {
                data: "task4",
                searchable: false
            },
            {
                data: "task5",
                searchable: false
            },
            {
                data: "task6",
                searchable: false
            },
            {
                data: "task7",
                searchable: false
            },
            {
                data: "task99",
                searchable: false
            },
            {
                data: "is_success_antrean",
                title : "Create Antrean",
                "render": function ( data, type, row, meta ) {
                    return data?'Antrean Berhasil':'Antrean Gagal';
                }
            },
            {
                data: "status",
                title : "Status Antrean",
                visible: true,
            },
            {
                data: "pendaftaran_id",
                title : "Status Lengkap",
                visible: false,
            },
        ],
        drawCallback: function(e) {
            var api = this.api();
            for (var i = 0; api.rows().count() > i; i++) {
                var rowData = api.row(i).data();
                var rowNode = api.row(i).node();
                if (rowData.tgl_order_resep && (rowData.task5 == null || rowData.task5 == undefined)) {
                    $(rowNode).addClass("row__not-completed-resep");
                } else if ((rowData.task7 == null || rowData.task7 == undefined) && (rowData.tgl_order_resep == null || rowData.tgl_order_resep == '')) {
                    $(rowNode).addClass("row__not-completed-non-resep");
                }
            }
        },
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            2,
            "<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>"
        ],
        [
            6,
            "<select id='jenispasien' class='form-control select2' name='jenis_pasien' col-index='4' tabindex='-1' aria-hidden='true'><option value='' selected='selected'>--Pilih Jenis Pasien--</option><option value='JKN'>JKN</option><option value='NON JKN'>NON JKN</option></select>"
        ],
        [
            18,
            "<select id='create_antrean' class='form-control select2' name='create_antrean' col-index='4' tabindex='-1' aria-hidden='true'><option value='' selected='selected'>--Pilih Create Antrean--</option><option value='true'>Antrian Berhasil</option><option value='false'>Antrean Gagal</option></select>"
        ],
        [
            19,
            "<select id='status_antrean' class='form-control select2' name='status_antrean' col-index='4' tabindex='-1' aria-hidden='true'><option value='' selected='selected'>--Pilih Status Antrean--</option><option value='selesai'>Selesai Dilayani</option><option value='belum'>Belum Dilayani</option><option value='kosong'>Kosong</option></select>"
        ],
        [
            20,
            "<select id='status_lengkap' class='form-control select2' name='status_lengkap' col-index='4' tabindex='-1' aria-hidden='true'><option value='' selected='selected'>--Pilih Status Lengkap--</option><option value='resep-belum-lengkap'>Pasien Resep Belum Lengkap</option><option value='non-resep-belum-lengkap'>Pasien Non Resep Belum Lengkap</option></select>"
        ],
    ],{
        2:1,
        3:2,
        4:3,
        5:4,
        6:5,
        7:6,
        18:7,
        19:8,
        20:9,
    }, true);
    dateRangeHelper(".startDate",".endDate",".targetDate");

    $("#btn-resend-task").on('click',function(e){
        let data = table.rows('.selected').data().toArray();
        let idsParam =table.rows('.selected').data().pluck('primary').toArray();
        if(data.length > 0){
            let cannotProcessResend = false
            data.forEach((item, i) => {
               if (!item.is_success_antrean) {
                   docoNotification('warning','Proses Tidak Dapat Dilanjutkan!','Ada data yang tidak memiliki data antrian');
                   cannotProcessResend = true
               }
            });

            if (!cannotProcessResend) {
                target = $(this).attr("action");
                $(this).docoForm("click", {
                  url: target+idsParam.toString(),
                  success: function(res){
                    table.draw();
                  }
                })
            }
        }
    });

    $("#reset-table-antreanbpjs").on('click',function(e){
        $(this).docoForm("click", {
            url: "/pendaftaran/informasi-antrean-bpjs/sync",
            skipConfirm:true,
            skipSuccessNotif:true,
            success: function(res){
                table.draw();
            }
        })
    });

    $(".pickMe").on( "click", function(e) {
        if ($(this).is( ":checked" )) {
            table.rows(  ).select();
        } else {
            table.rows(  ).deselect();
        }
    });

    $("#table-antreanbpjs").DataTable().on('draw', function () {
        $('.pickMe').prop('checked', false);
    });
});
