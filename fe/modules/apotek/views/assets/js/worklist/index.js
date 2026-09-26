$(document).ready(function() {

    dateRangeHelper(".startDate",".endDate",".targetDate");

    var list_data = [];
    var enc_noresep = "";
    var enc_id = "";
    var search_noresep = $("#search_noresep");
    var search_tgl_transaksi = $(".targetDate");
    var execute = {
        rj: true,
        ranap: true,
        igd: true,
        penunjang: true
    };

    var sync = true;
    $(document).on('change', '#search_noresep, .startDate, .endDate, .targetDate', function() {
        if (search_noresep.val() == "") {
            sync = true;
        } else {
            sync = false;
        }
    })

    $(document).on('click', '.data-reset', function() {
        $("#search_noresep").val("");
        $(".targetDate").val("");

        var start_default = $('.startDate').attr('data-default');
        var end_default = $('.endDate').attr('data-default');
        $('.startDate').val(start_default);
        $('.endDate').val(end_default);
        $('.data-filter').trigger('click');
    });

    var xhr = {
        rj : null,
        ranap: null,
        igd: null
    };

    getAllList();

    var modalDetail = $("#modal-detail-worklist");

    $("#modal-detail-worklist").on('hidden.bs.modal', function() {
        modalButtonInit();
    });

    setInterval(function(){
        if (!sync) {
            return true;
        }
        getAllList();
    }, 5000);

    $(".data-filter").on('click', function(){
        getAllList();
    });

    function getAllList() {
        getListRj();
        getListRi();
        getListRd();
        getListPenunjang();
    }

    function abortXhr(instalasi = null) {
        if (instalasi != null) {
            xhr[instalasi].abort();
        }
        if (instalasi == null) {
            $.each(xhr, function(index, value){
                value.abort();
            });
        }
    }

    function getListRj() {
        var url = '/apotek/worklist/get-list-rj';
        var instalasi = 'rj';
        if (execute.rj) {
            getCardResep(url, instalasi);
        }
    }

    function getListRi() {
        var url = '/apotek/worklist/get-list-ri';
        var instalasi = 'ranap';
        if (execute.ranap) {
            getCardResep(url, instalasi);
        }
    }

    function getListRd() {
        var url = '/apotek/worklist/get-list-rd';
        var instalasi = 'igd';
        if (execute.igd) {
            getCardResep(url, instalasi);
        }
    }

    function getListPenunjang(){
        var url = '/apotek/worklist/get-list-penunjang';
        var instalasi = 'penunjang';
        if (execute.penunjang) {
            getCardResep(url, instalasi);
        }
    }

    function getCardResep(url, instalasi) {
        var appendTo = '.panel-'+ instalasi + ' .panel-body-'+ instalasi;

        execute[instalasi] = false;

        url += "?" + "no_resep=" + search_noresep.val() + "&tanggal=" + search_tgl_transaksi.val();

        xhr[instalasi] =  $.get(url, function(data, status) {
            if (status == 'success') {
                var resp = data.response;
                list_data[instalasi] = resp;

                $(appendTo).html("");
                $.each(resp, function(index, value){
                    var template = $('#resep-card-example').clone();
                    template.removeAttr('style');
                    template.removeAttr('id');
                    template.attr('data-reseptur', value.no_reseptur);
                    template.attr('data-resep', value.no_resep);
                    template.attr('data-instalasi', instalasi);

                    var status = "";
                    if (value.status_worklist_id == 674) {
                        status = "<span class='badge badge-danger'>" + value.status_worklist + "</span>";
                    } else if (value.status_worklist_id == 675) {
                        status = "<span class='badge badge-default'>" + value.status_worklist + "</span>";
                    } else if (value.status_worklist_id == 676) {
                        status = "<span class='badge badge-warning'>" + value.status_worklist + "</span>";
                    } else if (value.status_worklist_id == 677) {
                        status = "<span class='badge badge-info'>" + value.status_worklist + "</span>";
                    } else if (value.status_worklist_id == 678) {
                        status = "<span class='badge badge-success'>" + value.status_worklist + "</span>";
                    } else {
                        status = "";
                    }

                    var status_bayar = "";
                    if (value.status_bayar_id == 349) {
                        status_bayar = "<span class='badge badge-danger'>" + value.status_bayar + "</span>";
                    } else {
                        status_bayar = "";
                    }

                    var tipe_resep = "";
                    if(value.kategori_resep_nama != null && value.kategori_resep_nama != '-') {
                        tipe_resep = "<span class='badge badge-custom'>" + value.kategori_resep_nama + "</span>";
                    }

                    template.find('.worklist-no-resep').html(value.no_reseptur + ' / ' + value.no_resep);
                    template.find('.worklist-info-pasien').html(value.nama_pasien + ' / ' + value.tanggal_lahir + '<br>' + value.no_pendaftaran);
                    template.find('.worklist-badges').html(status);
                    template.find('.worklist-payment-status').html(status_bayar);
                    template.find('.worklist-tipe-resep').html(tipe_resep);
                    template.appendTo(appendTo);
                });
            }
        }).done(function(){
            execute[instalasi] = true;
        });
    }

    $(document).on('click', '.card-worklist', function(e){
        modalButtonInit();
        showLoader();
        var noReseptur = $(this).attr('data-reseptur');
        var noResep    = $(this).attr('data-resep');
        var instalasi  = $(this).attr('data-instalasi');
        var identifier = noReseptur == "-" ? noResep : noReseptur;
        var dataDetail = list_data[instalasi][identifier];
        if (dataDetail === undefined) { docoNotification('error', 'Data tidak ditemukan', 'Data yang anda pilih tidak dapat ditemukan'); return false;}
        var additional_data = dataDetail.add_penjualaanresep == null ? JSON.parse(dataDetail.add_reseptur) : JSON.parse(dataDetail.add_penjualaanresep);
        if (additional_data != null) {
            var log_status = additional_data.log_status;
            appendLog(log_status);
        }

        enc_noresep = dataDetail.enc_noresep;
        enc_id = dataDetail.enc_id;
        var status_worklist = dataDetail.status_worklist_id;
        $.get('/apotek/worklist/get-detail?identifier=' + identifier, function(data, status){
            if (status == 'success') {
                var resp = data.response;
                list_data[instalasi][identifier]["detail"] = resp;
                var is_oral = $.map(resp, function(val) {
                    if(val.is_oral == true) {
                        return true;
                    } else {
                        return false;
                    }
                });

                var disable_oral, disable_nonoral = false;
                if(is_oral.includes(true) && is_oral.includes(false)) {
                    disable_oral = false;
                    disable_nonoral = false;
                } else if(is_oral.includes(true) && !is_oral.includes(false)) {
                    disable_oral = false;
                    disable_nonoral = true;
                } else if(!is_oral.includes(true) && is_oral.includes(false)) {
                    disable_oral = true;
                    disable_nonoral = false;
                }

                $('#btn-oral').attr('disabled', disable_oral);
                $('#btn-non-oral').attr('disabled', disable_nonoral);

                modalDetail.find('#detail-reseptur').html(dataDetail.no_reseptur);
                modalDetail.find('#detail-noresep').html(dataDetail.no_resep);
                modalDetail.find('#detail-status').html(dataDetail.status_worklist);
                modalDetail.find('#detail-namaPasien').html(dataDetail.nama_pasien);
                modalDetail.find('#detail-nopendaftaran').html(dataDetail.no_pendaftaran);
                modalDetail.find('#detail-alergi').html(dataDetail.alergi);
                modalDetail.find('#detail-tgllahir').html(dataDetail.tanggal_lahir);
                modalDetail.find('#detail-dokter').html(dataDetail.dokter);
                modalDetail.find('#detail-tb').html(dataDetail.tinggi_badan);
                modalDetail.find('#detail-bb').html(dataDetail.berat_badan);
                modalDetail.attr('data-identifier', identifier);
                modalDetail.attr('data-instalasi', instalasi);

                appendObatDetail(resp);

                if (instalasi == 'rj' && dataDetail.status_bayar_id == 349) {
                    modalButtonInit();
                } else {
                    modalButtonStatus(status_worklist);
                }

                modalDetail.modal('show');
            }
        });
    });

    function modalButtonInit(){
        $("#worklist-log ul li").remove();
        $("#modal-detail-worklist #btn-disiapkan").attr('disabled', 'disabled');
        $("#modal-detail-worklist #btn-ditelaah").attr('disabled', 'disabled');
        $("#modal-detail-worklist #btn-qc").attr('disabled', 'disabled');
        $("#modal-detail-worklist #btn-siapSerahkan").attr('disabled', 'disabled');

        $("#modal-detail-worklist #btn-disiapkan").removeClass('btn-info');
        $("#modal-detail-worklist #btn-ditelaah").removeClass('btn-info');
        $("#modal-detail-worklist #btn-qc").removeClass('btn-info');
        $("#modal-detail-worklist #btn-siapSerahkan").removeClass('btn-info');
    }

    function modalButtonStatus(status_worklist = null) {
        if (status_worklist == 674) {
            $("#modal-detail-worklist #btn-ditelaah").addClass('btn-info');
            $("#modal-detail-worklist #btn-ditelaah").removeAttr('disabled');
        }

        if (status_worklist == 676) {
            $("#modal-detail-worklist #btn-ditelaah").addClass('btn-info');
            $("#modal-detail-worklist #btn-disiapkan").addClass('btn-info');
            $("#modal-detail-worklist #btn-disiapkan").removeAttr('disabled');
        }

        if (status_worklist == 675) {
            $("#modal-detail-worklist #btn-ditelaah").addClass('btn-info');
            $("#modal-detail-worklist #btn-disiapkan").addClass('btn-info');
            $("#modal-detail-worklist #btn-qc").addClass('btn-info');
            $("#modal-detail-worklist #btn-qc").removeAttr('disabled');
        }

        if (status_worklist == 677) {
            $("#modal-detail-worklist #btn-ditelaah").addClass('btn-info');
            $("#modal-detail-worklist #btn-disiapkan").addClass('btn-info');
            $("#modal-detail-worklist #btn-qc").addClass('btn-info');
            $("#modal-detail-worklist #btn-siapSerahkan").addClass('btn-info');
            $("#modal-detail-worklist #btn-siapSerahkan").removeAttr('disabled');
        }

        if (status_worklist == 678) {
            $("#modal-detail-worklist #btn-ditelaah").addClass('btn-info');
            $("#modal-detail-worklist #btn-disiapkan").addClass('btn-info');
            $("#modal-detail-worklist #btn-qc").addClass('btn-info');
            $("#modal-detail-worklist #btn-siapSerahkan").addClass('btn-info');
        }
    }

    function appendObatDetail(data){
        var tbody = $("#table-detail-worklist tbody");
        tbody.html("");
        $.each(data, function(index, value){
            var label_is_racikan = value.is_racikan ? "<i class='fa fa-check'></i>" : "-";
            var row = "";
            row += "<tr>";
            row += "<td>" + value.rowNum + "</td>";
            row += "<td>" + label_is_racikan + "</td>";
            row += "<td>" + value.rke + "</td>";
            row += "<td>" + value.nama_obat + "</td>";
            row += "<td>" + value.signa + "</td>";
            // row += "<td>" + value.qty_obat + "</td>";
            row += "<td>" + value.det + "</td>";
            row += "<td>" + value.satuan_input + "</td>";
            row += "<td>" + value.det + "</td>";
            row += "<td>" + value.etiket + "</td>";
            row += "</tr>";
            $(row).appendTo(tbody);
        });
    }

    function appendLog(data){
        $("#worklist-log ul li").remove();
        var log_ul = $("#worklist-log ul");
        var str_li = "";
        $.each(data, function(index, value){
            str_li = "";
            str_li += "<li>";
            str_li += "["+ value.tanggal +"], " + value.nama_pegawai +", " + value.status_worklist;
            str_li += "</li>";
            $(str_li).appendTo(log_ul);
        });
    }

    $(document).on('click', '.btn-status-worklist', function(){
        $(this).attr('disabled', true);
        var id         = modalDetail.attr('data-identifier');
        var instalasi  = modalDetail.attr('data-instalasi');
        var status     = $(this).attr('data-work-stat');

        $.post('/apotek/worklist/update-status', { identifier: id, status_worklist: status }, function(data, status) {
            if (status == 'success') {
                modalDetail.modal('hide');
                docoNotification('success', 'Proses Berhasil', data.response.message);
                execute[instalasi] = true;
                modalButtonStatus(status);
                if (instalasi == 'rj') {
                    getListRj();
                } else if ('ranap') {
                    getListRi();
                } else if ('igd') {
                    getListRd();
                } else if('penunjang')  {
                    getListPenunjang();
                } else {
                    return true;
                }
            }
        });
    });

    $(document).on('click', '.btn-cetak-etiket', function(event) {
        event.preventDefault();
        var jenis = $(this).data('jenis');
        var noReseptur = $('#detail-reseptur').text();
        var noResep = $('#detail-noresep').text();
        var identifier = noReseptur == "-" ? noResep : noReseptur;
        var is_oral = jenis == 'oral' ? true : false;
        window.open('/apotek/worklist/print-etiket?identifier='+identifier+'&is_oral='+is_oral);
    });

    $(document).on('click', '.btn-cetak-resep', function(event) {
        event.preventDefault();
        window.open('/apotek/informasi-reseptur/print-resep-detail?id=' + enc_id +'&nomor=' + enc_noresep);
    });

    modalDetail.on('shown.bs.modal', function() {
        hideLoader();
    });
});

