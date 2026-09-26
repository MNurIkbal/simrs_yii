// Global Var
var table;
var statusBatal = false

// Event click
$(document).on("click", ".data-reset", function() {
    // Reload table
    table.draw();

    // Disable edit and delete button
    $("#btn-approve").prop("disabled", true);
    $("#btn-edit-tanggal").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
});

// Event click
$(document).on("click", "#tb-inf-pasien-rujukan-lab tbody tr", function() {
    // Try catch
    try {
        // Get primary
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
        statusPenunjang = table.row(".selected").data().status_penunjang ? table.row(".selected").data().status_penunjang : null;
        statusBayar = table.row(".selected").data().is_bayar ? table.row(".selected").data().is_bayar : null;
        caraBayar = table.row(".selected").data().carabayar_id ? table.row(".selected").data().carabayar_id : null;
        statusPeriksa = table.row(".selected").data().status_periksa ? table.row(".selected").data().status_periksa : null;
        instalasi = table.row(".selected").data().instalasi_id ? table.row(".selected").data().instalasi_id : null
        penjamin = table.row(".selected").data().penjamin_id ? table.row(".selected").data().penjamin_id : null
        jml_pemeriksaan = table.row(".selected").data().jml_pemeriksaan ? table.row(".selected").data().jml_pemeriksaan : null
        jml_pemeriksaan_approve = table.row(".selected").data().jml_pemeriksaan_approve ? table.row(".selected").data().jml_pemeriksaan_approve : null

        jumlah_tagihan = table.row(".selected").data().jumlah_tagihan ? table.row(".selected").data().jumlah_tagihan : 0
        jumlah_bayar = table.row(".selected").data().jumlah_bayar ? table.row(".selected").data().jumlah_bayar : 0

    } catch (e) {
        // Make it false
        primaryKey = false;
        statusPenunjang = false;
        statusPeriksa = false;
    }
    

    if (statusPeriksa != constBelumPeriksa && statusPeriksa) {
        if (statusPeriksa == 476) {
            statusBatal = true
        }
        statusPeriksa = true;
    } else {
        statusPeriksa = false;
    }

    // Assign to ubah
    // $("#btn-edit").attr("action", updateUrl + primaryKey);
    $("#btn-approve").attr("data-target", approveUrl + primaryKey);
    // $("#btn-edit-tanggal").attr("data-target", approveUrl + primaryKey);
    $("#btn-batal").attr("data-target", batalUrl + primaryKey);

    // Check class selected
    if ($('#tb-inf-pasien-rujukan-lab tr.selected').length == 0) {
        // Disable edit button
        $("#btn-approve").prop("disabled", true);
        $("#btn-edit-tanggal").prop("disabled", true);
        $("#btn-batal").prop("disabled", true);
    }
    else {
        // Disable edit button
        $("#btn-approve").prop("disabled", false);
        $("#btn-edit-tanggal").prop("disabled", false);
        $("#btn-batal").prop("disabled", false);
        $("#btn-batal").attr("data-options", 'link');

        // pengecek bila status sudah di setujui maka tombol approve tidak akan aktif
        if (statusPenunjang == constDisetujui){
            $("#btn-approve").prop("disabled", true);
            $("#btn-edit-tanggal").prop("disabled", true);
            if (jml_pemeriksaan != jml_pemeriksaan_approve) {
                $("#btn-approve").prop("disabled", false);
            }
        }
        // pengecek bila status sudah di setujui maka tombol approve tidak akan aktif
        // pengecek bila status sudah dibatalkan maka tombol approve dan batal tidak akan aktif
        if (statusPenunjang == constBatal) {
            $("#btn-approve").prop("disabled", true);
            $("#btn-edit-tanggal").prop("disabled", true);
            $("#btn-batal").prop("disabled", true);
        }
        // pengecek bila status sudah dibatalkan maka tombol approve dan batal tidak akan aktif
        if (statusPenunjang == constAmbilSample || statusPeriksa) {
            $("#btn-approve").prop("disabled", true);
            $("#btn-edit-tanggal").prop("disabled", true);
            $("#btn-batal").attr("data-options", 'click');
            $("#btn-batal").attr("data-target", "#");
            $("#btn-batal").attr("data-pesan-error", "Tidak Bisa membatalkan karena sudah mengambil sampel");
        }
         
        //pasien instalasi rj, penjamin perseorangan dan sudah melakukan pembayaran tidak bisa dibatalkan
        var statusBayarUpdate = false;
        if ((jumlah_bayar > 0 && jumlah_tagihan == 0) || (jumlah_bayar > 0 && jumlah_tagihan > 0)) {
            statusBayarUpdate = true;
        }
        // console.log(statusBayarUpdate)
        if (statusBayarUpdate && instalasi == constInstalasiRJ && penjamin == constPenjaminPerseorangan) {
            if (jml_pemeriksaan != jml_pemeriksaan_approve) {
                $("#btn-approve").prop("disabled", false);
            }else{
                $("#btn-approve").prop("disabled", true);
                $("#btn-edit-tanggal").prop("disabled", true);
                $("#btn-batal").attr("data-options", 'click');
                $("#btn-batal").attr("data-target", "#");
                $("#btn-batal").attr("data-pesan-error", "Pasien dari instalasi Rawat Jalan dan Penjamin Perseorangan tidak bisa dibatalkan jika sudah melakukan pembayaran, harap melakukan pembatalan pembayaran.");
            }
        } else {
            if (statusPeriksa) {
                $("#btn-batal").attr("data-options", 'click');
                $("#btn-batal").attr("data-target", "#");
                if (statusBatal) {
                    $("#btn-batal").prop("disabled", true);
                }else{
                    $("#btn-batal").attr("data-pesan-error", "Tidak Bisa membatalkan karena sudah diperiksa");
                }
            }
        }
    }
});

$("#btn-batal").on('click',function(){
    if ($("#btn-batal").attr("data-options") == "click"){
        docoNotification('error', "Gagal Melakukan Batal", $("#btn-batal").attr("data-pesan-error"));
    }
});

// Event Ready
$(document).ready(function() {
    $("#btn-approve").prop("disabled", true);
    $("#btn-edit-tanggal").prop("disabled", true);
    $("#btn-batal").prop("disabled", true);
    // Generate Table
    table = $("#tb-inf-pasien-rujukan-lab").docoTabel({
        columnDefs: [ {
            searchable: false,
            orderable: false,
            className: "select-checkbox",
            targets: 0
        }],
        select: {
            style: "os",
            selector: "tr"
        },
        filter: true,
        sorting: [[3, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        scrollCollapse: true,
        ajax: baseUrl + "laboratorium/inf-pasien-rujukan-lab/get-data",
        columns: [
            {
                title: "",
                data: null,
                defaultContent: "",
                searchable: false,
                orderable: false
            },
            {
                title: no,
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: no_pendaftaran,
                data: "no_pendaftaran",
                searchable: true
            },
            {
                title: tgl_rujukan,
                data: "tgl_rujukan",
                searchable: true
            },
            {
                title: no_rujukan,
                data: "no_rujukan",
                searchable: true
            },
            {
                title: nama_pasien,
                data: "no_rekam_medik",
                name: "nama_pasien",
                searchable: true
            },
            {
                title: tanggal_lahir,
                data: "tanggal_lahir",
                searchable: true
            },
            {
                title: ruangan_nama,
                data: "ruangan_nama",
                searchable: true
            },
            {
                title: dokter_perujuk,
                data: "dokter_perujuk",
                searchable: true
            },
            {
                title: carabayar_nama,
                data: "carabayar_nama",
                searchable: true
            },
            {
                title: penjamin_nama,
                data: "penjamin_nama",
                searchable: true
            },
            {
                title: status_penunjang,
                data: "status_periksa_btn",
                name:"stat_penunjang",
                searchable: true,
                visible: false
            },
            {
                title: is_bayar,
                data: "status_bayar",
                name: "stat_penunjang",
                searchable: false,
            },
        ],
        createdRow: function(row, data, dataIndex){
            if (data.status_penunjang == constBatal) {
                $(row).css("background-color", "#D24D57").css("color", "white");
            } else if(data.status_penunjang == 470) {
                $(row).css("background-color", "#FFFfff").css("color", "black");
            } else if (data.status_penunjang == constDisetujui ) {
                var statusBayarUpdate = false;
                if (data.jumlah_bayar > 0 && data.jumlah_tagihan == 0) {
                    statusBayarUpdate = true;
                }

                if (data.status_periksa == 476) {
                    $(row).css("background-color", "#D24D57").css("color", "white");
                }
                else{
                    if ( statusBayarUpdate == true ){
                        $(row).css("background-color", "#26A65B").css("color", "white");
                        if (data.jml_pemeriksaan != data.jml_pemeriksaan_approve) {
                            $(row).css("background-color", "#6A5ACD").css("color", "black");
                        }
                    } else if ((statusBayarUpdate == null || !statusBayarUpdate)){
                        $(row).css("background-color", "#ffff00").css("color", "black");
                        if (data.jml_pemeriksaan != data.jml_pemeriksaan_approve) {
                            $(row).css("background-color", "#FFA500").css("color", "black");
                        }
                    }
                }
            }
        },
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table , [
        [3, filterTanggalRujukan],
        [6, filterTanggalLahir],
        [7, dropdownRujukan],
        [8, autocompleteDokter],
        [9, dropdownCaraBayar],
        [10, dropdownPenjamin],
        [11, dropdownStatus],
    ],{
        2:0,
        3:1,
        4:2,
        5:3,
        6:4,
        7:5,
        8:6,
        9:7,
        10:8,
        11:9
    });

    dateRangeHelperFormat(".startDates",".endDates",".targetDates", true);
    dateRangeHelperFormat(".startDatex",".endDatex",".targetDatex", true);

    // dateRangeHelper(".rangeLahirStart", ".rangeLahirFinish", ".targetDateLahir");
    // dateRangeHelper(".startDate", ".endDate", ".targetDate");
    
    // $(".daterange-basic").daterangepicker({
    //     startDate: '<?=(date("01-M-Y"))?>', autoUpdateInput: true,
    //     endDate: '<?=(date("d-M-Y"))?>',
    //     applyClass: "bg-slate-600",
    //     cancelClass: "btn-default",
    //     locale: {
    //         format: "DD-MMMM-YYYY"
    //     }
    // });
});

/**
* Date Format
* Indra Tiola
*/
var dateRangeHelperFormat = function(startClass, endClass, targetClass, limit = true){
    //declare variable
    var start = $(startClass);
    var end = $(endClass);
    var target = $(targetClass);

    var current_datetime = new Date()
    var formatted_date = current_datetime.getDate() + "-" + current_datetime.getMonth() + "-" + current_datetime.getFullYear();

    start.change(function(e) {
        
        if (!start.val()) {
            $(startClass).val(end.val());
            docoNotification("warning", "Perhatian!", "Tanggal Mulai Tidak Boleh Kosong!");    
        }
        var startDate = new Date(start.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
        var endDate = new Date(end.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));

        if (startDate > endDate) {
            docoNotification("warning", "Perhatian!", "Tanggal Mulai Harus Lebih Kecil Dari Tanggal Akhir!");
            $(startClass).val(end.val());    
        } 
        dateValue = start.val()+' - '+end.val();
        target.val(dateValue);   
    });
    end.change(function(e) {
        
        if (!end.val()) {
            $(endClass).val(start.val());   
            docoNotification("warning", "Perhatian!", "Tanggal Akhir Tidak Boleh Kosong!"); 
        }

        var startDate = new Date(start.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
        var endDate = new Date(end.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));

        if (startDate > endDate) {
            docoNotification("warning", "Perhatian!", "Tanggal Akhir Harus Lebih Besar Dari Tanggal Mulai!");
            $(endClass).val(start.val());    
        } 
            dateValue = start.val()+' - '+end.val();
            target.val(dateValue); 
    });
    start.on('keydown', function(e) {
        if (e.which == 13) {
            if (!end.val()) {
                $(endClass).val(start.val());   
                docoNotification("warning", "Perhatian!", "Tanggal Akhir Tidak Boleh Kosong!"); 
            }

            var startDate = new Date(start.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
            var endDate = new Date(end.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));

            if (startDate > endDate) {
                docoNotification("warning", "Perhatian!", "Tanggal Akhir Harus Lebih Besar Dari Tanggal Mulai!");
                $(endClass).val(start.val());    
            } 
                dateValue = start.val()+' - '+end.val();
                target.val(dateValue); 
        }
    });
    end.on('keydown', function(e) {
        if (e.which == 13) {
            if (!end.val()) {
                $(endClass).val(start.val());   
                docoNotification("warning", "Perhatian!", "Tanggal Akhir Tidak Boleh Kosong!"); 
            }

            var startDate = new Date(start.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
            var endDate = new Date(end.val().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));

            if (startDate > endDate) {
                docoNotification("warning", "Perhatian!", "Tanggal Akhir Harus Lebih Besar Dari Tanggal Mulai!");
                $(endClass).val(start.val());    
            } 
                dateValue = start.val()+' - '+end.val();
                target.val(dateValue); 
        }
    });
}
/**
* Date filter
* Indra Tiola
* updated iqbal
*/
$(document).on("click", "#btn_add_start_date", function(event) {
    var $startDate = $('#startDate').pickadate({
        editable: true,
        format:'dd-mm-yyyy',
        formatSubmit:'dd-mm-yyyy',
        selectMonths: true,
        selectYears: 120,
        max: true,
        onClose: function() {
            $('.datepicker').focus();
        }
    });
    var picker_startDate = $startDate.pickadate('picker');

    if (picker_startDate.get('open')) {
        picker_startDate.close();
    } else {
        picker_startDate.open();
    }

    var _endDate = $('#endDate').pickadate('picker');
    try { 
        var checked = _endDate.get('open'); 
    } catch (e) {
        checked = null;
    }

    if (checked) {
        _endDate.close();
    }

    event.stopPropagation();
});
/**
* Date filter
* Indra Tiola
* updated iqbal
*/
$(document).on("click", "#btn_add_end_date", function(event) {
    var $endDate = $('#endDate').pickadate({
        editable: true,
        format:'dd-mm-yyyy',
        formatSubmit:'dd-mm-yyyy',
        selectMonths: true,
        selectYears: 120,
        max: true,
        onClose: function() {
            $('.datepicker').focus();
        }
    });
    var picker_endDate = $endDate.pickadate('picker');
    if (picker_endDate.get('open')) {
        picker_endDate.close();
    } else {
        picker_endDate.open();
    }

    var _startDate = $('#startDate').pickadate('picker');
    try { 
        var checked = _startDate.get('open'); 
    } catch (e) {
        checked = null;
    }

    if (checked) {
        _startDate.close();
    }
    event.stopPropagation();
});

/**
* Date filter
* Indra Tiola
* updated iqbal
*/
$(document).on("click", "#btn_add_start_date1", function(event) {
    var $startDate = $('#startDate1').pickadate({
        editable: true,
        format:'dd-mm-yyyy',
        formatSubmit:'dd-mm-yyyy',
        selectMonths: true,
        selectYears: 120,
        max: true,
        onClose: function() {
            $('.datepicker').focus();
        }
    });
    var picker_startDate = $startDate.pickadate('picker');

    if (picker_startDate.get('open')) {
        picker_startDate.close();
    } else {
        picker_startDate.open();
    }

    var _endDate = $('#endDate1').pickadate('picker');
    try { 
        var checked = _endDate.get('open'); 
    } catch (e) {
        checked = null;
    }

    if (checked) {
        _endDate.close();
    }

    event.stopPropagation();
});
/**
* Date filter
* Indra Tiola
* updated iqbal
*/
$(document).on("click", "#btn_add_end_date1", function(event) {
    var $endDate = $('#endDate1').pickadate({
        editable: true,
        format:'dd-mm-yyyy',
        formatSubmit:'dd-mm-yyyy',
        selectMonths: true,
        selectYears: 120,
        max: true,
        onClose: function() {
            $('.datepicker').focus();
        }
    });
    var picker_endDate = $endDate.pickadate('picker');
    if (picker_endDate.get('open')) {
        picker_endDate.close();
    } else {
        picker_endDate.open();
    }

    var _startDate = $('#startDate1').pickadate('picker');
    try { 
        var checked = _startDate.get('open'); 
    } catch (e) {
        checked = null;
    }

    if (checked) {
        _startDate.close();
    }
    event.stopPropagation();
});