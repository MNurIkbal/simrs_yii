$(document).ready(function() {
    $("#merge-po").on("click", function (e) {
        let tableDatas = table.rows(".selected").data();
        let message = "";
        let validate = true;
        if(typeof tableDatas !== "undefined" && tableDatas.length > 1){
            let tObj = [];
            let types = [];
            let poNumbers = [];
            let supplier = [];
            $.each(tableDatas, function(k,v){
                if(v.is_validasi){
                    message = "Gabung PO hanya untuk PO yang belum divalidasi";
                    validate = false;
                }
                types.push(v.type_po);
                poNumbers.push(v.no_transaksi);
                supplier.push(v.supplier_id);
                tObj.push({"primary": v.primary});
            });

            let is_same_type = types.every((val, i, arr) => val === arr[0]);
            if(!is_same_type) {
                message = "Tidak dapat melakukan gabung PO obat dan barang";
                validate = false;
            }

            let is_same_number = poNumbers.every((val, i, arr) => val === arr[0]);
            if(is_same_number) {
                message = "Tidak dapat melakukan gabung PO dengan nomor PO yang sama";
                validate = false;
            }

            let is_same_supplier = supplier.every((val, i, arr) => val === arr[0]);
            if(!is_same_supplier) {
                message = "Tidak dapat melakukan gabung PO dengan supplier berbeda";
                validate = false;
            }

            if(!validate) {
                docoNotification("warning", "Perhatian", message);
                return false;
            }

            $(this).docoForm("click", {
                url: "/pengadaan/info-purchase-order/merge-po",
                data: {
                    po: JSON.stringify(tObj),
                    type_po: tableDatas[0].type_po
                },
                skipErrorNotif: true,
                confirmMessage: "Apakah Anda yakin ingin menggabungkan PO?",
                title: "Sukses",
                method: "POST",
                type: "json",
                success: function(response){
                    table.draw();
                }
            });
        } else {
            message = "Belum ada data yang dipilih"
            if(tableDatas.length == 1) {
                message = "Silakan pilih minimal 2 data untuk gabung PO";
            }

            docoNotification("warning", "Perhatian", message);
        }
    });

    $(document).on("click", "#tbl-info-purchase-order tbody tr", function() {
        let selectedRow = table.rows(".selected").data();
        let selected = selectedRow.length;
        let disableMerge = disableLihat = disableDelete = true;
        if (selected > 1) {
            disableMerge = false;
            disableLihat = true;
            disableDelete = true;
            disablePrintKop = false;
            let status = table.row(".selected").data().status_penerimaan ? table.row(".selected").data().status_penerimaan : null;
            if(status == 575) {
                disableMerge = true;
            } else {
                disableMerge = false;
            }
        } else if(selected == 1) {
            disableMerge = true;
            disableLihat = false;
            disablePrintKop = false;
            let status = table.row(".selected").data().status_penerimaan ? table.row(".selected").data().status_penerimaan : null;
            if(status != 572) {
                disableDelete = true;
            } else {
                disableDelete = false;
            }
        } else if(selected < 1) {
            disableMerge = true;
            disableLihat = true;
            disableDelete = true;
            disablePrintKop = true;
        }

        $("#merge-po").attr("disabled", disableMerge);
        $("#btn-lihat").attr("disabled", disableLihat);
        $("#delete-po").prop("disabled", disableDelete);
        $(".btn-print-kop").prop("disabled", disablePrintKop);
    });

    $(document).on('click', '.btn-print-kop', function (e) {
        if ($(this).attr('data-toggle') == 'modal') return;
        var tableId = $(this).attr('data-table');
        var table = $(tableId).DataTable();
        var tableDatas = table.rows('.selected').data();
        let column = table.data().count();
        var conditions = $(this).attr('data-conditions') ? $(this).attr('data-conditions').split(',') : '';
        let url = $(this).attr('data-url') ? $(this).attr('data-url') : $(this).attr('data-href') ? $(this).attr('data-href') : null;
        
        if (tableDatas.length == 0) {
            docoNotification('warning', 'Terjadi Kesalahan', 'Tidak ada Data yang dipilih!');
        } else if (column === 0) {
            docoNotification('warning', 'Terjadi Kesalahan', 'Data Tidak Tersedia!');
        } else {
            var params = ''
            $.each(tableDatas, function (index, valueTable) {
                if(conditions.length > 0) {
                    $.each(conditions, function (index, value) {
                        params += value.trim() + '[]=' + valueTable[value] + '&';
                    });
                }
            })

            params = `${params == '' ? params : encodeURI(params)}${$.param(table.ajax.params())}`

            $(this).attr('action', url + params);

            $(this).attr('data-toggle', 'modal');

            $(this).trigger('click');

            $(this).removeAttr('action');
            $(this).removeAttr('data-toggle');
        }
    });
});
