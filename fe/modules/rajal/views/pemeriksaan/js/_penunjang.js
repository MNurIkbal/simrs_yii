/* 
    Author : Rizal Faidin
*/
var table_riwayatPenunjang;
var pemeriksaanlab = {};

$(document).ready(function () {
    $('#btn-penunjang-back').bind('click' , () => {
        $('#tab-cppt').trigger('click')
    })

    $('#save-terapi').attr('disabled', status_periksa);
    var date = new Date();
    var yesterday = new Date((new Date()).valueOf()-1000*60*60*24);
    $('.pickadate').pickadate({
        format: 'dd mmmm yyyy',
        disable: [
            { from: [0,0,0], to: yesterday }
        ]
    });

    var picker = $('#penunjang_tgl_kirimpasien').pickadate('picker');
    picker.set('select', date);


    var params = 'pendaftaran_id=' + pend_id;
    // params += '&instalasi_id=' + instalasi_id;
    var table_id = 'tabel-r';
    table_riwayatPenunjang = $('#'+table_id).docoTabel({
        filter: false,
        displayLength: 10,
        // columnDefs: [ {
        //     orderable: false,
        //     className: 'select-checkbox',
        //     targets:   0,
        //     checkboxes: {
        //         selectRow: true
        //     }
        // }],
        select: {
            // style: 'multi',
            selector: 'tr'
        },
        sorting: [[2, 'asc']], 
        processing: true,
        serverSide: true,
        ajax: baseUrl+'rajal/pemeriksaan/get-data-history-penunjang?'+params,
        columns:[
            // {
            //     data: null,
            //     searchable: false,
            //     orderable: false,
            //     defaultContent: '',
            // },
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {data: 'tgl_kirimpasien'},
            {data: 'instalasi_nama'},
            {data: 'ruangan_nama'},
            {data: 'no_orderkeunitlain'},
            {data: 'nama_pegawai'},
            {data: 'catatan_dokterpengirim'},
            {data: 'catatan'},
            {data: 'status'},
            {data: 'aksi'},
        ]
    });
});

$('.penunjang-bedah').hide();
$('#penunjang_instalasi_id').change(function() {
    $('#tgl_permintaan_info').html('-');
    $('#jam_mulai_info').html('-');
    $('#jam_selesai_info').html('-');
    $('#dr_operator_info').html('-');
    $('#dr_anestesi_info').html('-');
    $('#penunjang_has_jadwal').val('0');
    if ($(this).val() == '12') {
        $('.penunjang-bedah').show();
        $('.btn-buka-jadwal').attr('disabled', false);
    } else {
        $('.penunjang-bedah').hide();
        $('.btn-buka-jadwal').attr('disabled', true);
    }

    pemeriksaanlab = {};
    loadpemeriksaan(pemeriksaanlab);
});

$('#penunjang_ruangan_id').change(function(){
    if ($(this).val() == '') {
        $('.btn-pemeriksaan-tambah').prop('disabled', true);
    } else {
        $('.btn-pemeriksaan-tambah').prop('disabled', false);
    }
});


$('#save-terapi').click(function(e) {
// $('#terapi-penunjang-form').submit(function(e) {
    e.preventDefault();
    var data = $('#terapi-penunjang-form').serializeArray();

    if ($.isEmptyObject(pemeriksaanlab)) {
        new PNotify({
            title: 'Proses Gagal !',
            text: 'Pemeriksaan tidak boleh kosong',
            addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
            type: 'error'
        });
    } else {
        data.push({name:'periksalab', value:JSON.stringify(pemeriksaanlab)});
        // data.push({name:'catatan_terapi', value:$('#instruksiform-catatan_instruksi').val()});
        // data.push({name:'pegawai_id', value:$('#penunjang_pegawai_id').val()});
        // data.push({name:'instruksi_id', value:$('#instruksiform-instruksi_id').val()});

        // console.log(data);
        $().docoForm('click',{
            data:data,
            url: $('#terapi-penunjang-form').attr('action'),
            success : function (data) {
                var pasienkirimkeunitlain_id = data.response.pasienkirimkeunitlain_id;
                list_pemeriksaanlab = []
                pemeriksaanlab = {}
                loadpemeriksaan(pemeriksaanlab)

                // Pnotify
                PNotify.prototype.options.styling = "bootstrap3";
                (new PNotify({
                    title: "Berhasil",
                    text: "Data berhasil disimpan, apakah Anda ingin melakukan cetak?",
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: "success",
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    hide: false,
                    confirm: {
                        confirm: true
                    },
                    history: {
                        history: false
                    }
                })).get().on('pnotify.confirm', function() {
                    // Print
                    window.open("/rajal/pemeriksaan/cetak-penunjang?id="+pendaftaran_id+"&pasienkirimkeunitlain_id="+pasienkirimkeunitlain_id);
                }).on('pnotify.cancel', function() {

                });
                
                const monthNames = ["January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];

                var d = new Date();
                var day = d.getDate();
                var thn = d.getFullYear();
                var tgl = ((''+day).length<2 ? '0' : '') + day;
                var bulan = monthNames[d.getMonth()];

                $('.btn-buka-jadwal').prop("disabled", true);
                $("#penunjang_instalasi_id").val('');
                $('#penunjang_ruangan_id').val('');
                $('#penunjang_tgl_kirimpasien').val(tgl + " " + bulan + " " + thn);
                $("#tgl_permintaan_info").text('-');
                $("#jam_mulai_info").text('-');
                $("#dr_anestesi_info").text('-');
                $("#dr_operator_info").text('-');
                $("#jam_selesai_info").text('-');

                $('#penunjang_instalasi_id2').val('');
                $('#penunjang_ruangan_id2').val('');
                $('#instruksipenunjangform-catatan_dokterpengirim').val('');
                $('#penunjang_instalasi_id').attr('disabled', false).val('').trigger('change.select2').trigger('depdrop:change');
                $('#penunjang_ruangan_id').attr('disabled', false);
                $('#instruksipenunjangform-catatan_dokterpengirim').val('');

                table_riwayatPenunjang.draw();
            },
            error : function (data) {
            }
        });
    }

});

$('.btn-muat-ulang').click(function(e) {
    e.preventDefault();

    var date = new Date();
    var picker = $('.pickadate').pickadate('picker');

    if (!$.isEmptyObject(pemeriksaanlab)) {
        var header = "Perhatian !";
        var message = "Apakah Anda yakin untuk muat ulang? Jika yakin maka order terakhir akan terhapus dan tidak tersimpan.";
        var label = { 
            buttons: {
                'No': 'btn btn-danger',
                'Yes': 'btn btn-success btn-yes'
            }
        };
        $.showQuestionDialog(header, message, label, function(reaction) {
            if (reaction == 'Yes') {
                $('#penunjang_instalasi_id2').val('');
                $('#penunjang_ruangan_id2').val('');
                $('#instruksipenunjangform-catatan_dokterpengirim').val('');
                $('#penunjang_instalasi_id').attr('disabled', false).val('').trigger('change.select2').trigger('depdrop:change');
                $('#penunjang_ruangan_id').attr('disabled', false);
                picker.set('select', date);
                $('.btn-pemeriksaan-clear').click();
            } else {

            }
        });
    } else {
        $('#penunjang_instalasi_id2').val('');
        $('#penunjang_ruangan_id2').val('');
        $('#instruksipenunjangform-catatan_dokterpengirim').val('');
        $('#penunjang_instalasi_id').attr('disabled', false).val('').trigger('change.select2').trigger('depdrop:change');
        $('#penunjang_ruangan_id').attr('disabled', false);
        picker.set('select', date);
        $('.btn-pemeriksaan-clear').click();
    }

});
