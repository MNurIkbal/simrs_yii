$(document).ready(function() {
	var list_data = [];
    var qty_sisa = max_qty;

    $(`select[name="nama_supplier"]`).select2();

    $(`select[name="nama_supplier"]`).on('select2:select', function(e) {
        var harga_obat = list_supplier[e.params.data.id]['supplier_harga'];

        if(harga_obat != null) {
            var supplier_satuan = list_supplier[e.params.data.id]['supplier_satuan'];
            var nilai_konversi_supplier = list_konversi[supplier_satuan]['nilai_konversi'];
            var nilai_konversi_po = list_konversi[satuan_id]['nilai_konversi'];
            var harga = harga_obat / (nilai_konversi_supplier / nilai_konversi_po);

            document.getElementById("harga").innerHTML = formatMoney(harga);    
        }
    });

	$(`input[name="qty"]`).on("keyup", function() {
		match = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
		var qty_input = match[1]

		if (qty_input > qty_sisa) {
			this.value = qty_sisa;
		}
	});

	$("#btn-tambah").on('click', function() {
        var select_supplier = $(`select[name="nama_supplier"]`).select2('data')[0];
        var qty             = $(`[name="qty"]`).val();
        var catatan         = $(`[name="catatan"]`).val();
        var harga           = document.getElementById("harga").innerHTML.replace(',','');
        var _isDuplicate    = false;

        if(select_supplier['id'] == "") {
            docoNotification('warning', "Silahkan cek inputan", "Nama Supplier belum di input.");
            return false;
        }

        match = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(qty.replace(/[^\d,]/g, ""));
        qty = match[1];

        if(qty < 1) {
            docoNotification('warning', "Silhakan cek inputan", "Qty kurang dari 1");
            return false;
        }

        $.each(list_data, function(index, row) {
            if (index == select_supplier['id'] && row !== undefined) {
                _isDuplicate = true;
            }
        });

        if(_isDuplicate) {
            docoNotification('warning', "Silhakan cek inputan", "Tidak dapat memasukan supplier yang sama.");
            return false;
        }

        if(harga == ' - ') {
            harga = null;
        }

        var newData = {
        	supplier_id	   : select_supplier['id'],
        	supplier_nama  : select_supplier['text'],
        	qty            : qty,
        	satuan		   : satuan,
            satuan_id      : satuan_id,
            harga          : harga,
            catatan        : catatan
        };

        list_data[newData.supplier_id] = newData;
        appendData(list_data);
        resetForm();
        hitungQty(qty);
    });

    function resetForm() {
        $(`[name="nama_supplier"], [name="qty"], [name="catatan"]`).val(null).trigger('change');
        document.getElementById("harga").innerHTML = "-";
    }

    function appendData(data) {
        $("#table-po tbody").html("");
        var no = 1;
        $.each(data, function(index, row) {
            if (row == undefined) {
                return;
            }

            var str_tr = "";
            str_tr += "<tr>";
            str_tr += "<td class='text-center' width='1'>"+ no +"</td>";
            str_tr += "<td style='width: 40%'>"+ row.supplier_nama +"</td>";
            str_tr += "<td style='width: 15%'>"+ row.qty +"</td>";
            str_tr += "<td style='width: 10%'>"+ row.satuan +"</td>";

            if(row.harga != null && row.harga != '-') {
                str_tr += "<td style='width: 10%'>"+ formatMoney(row.harga) +"</td>";
            } else {
                str_tr += "<td style='width: 10%'> - </td>";
            }
            
            // str_tr += `<td style='width: 10%'><input name='harga_alih' data-index="`+index+`" type='text' class='form-control doco-number' style='width: 100%' value="`+ row.harga +`"></td>`;
            str_tr += "<td style='width: 25%'>"+ row.catatan +"</td>";
            str_tr += `<td class='text-center'>
            			<button class="btn btn-sm btn-danger btn-hapus" data-index="`+ index +`">
            			<i class="fa fa-minus"></i></button>
            			</td>`;
            str_tr += "</tr>";
            no++;
            $("#table-po tbody").append(str_tr);
        });
    }

    $(document).on('click',".btn-hapus", function() {
        var button = $(this);
        var index = button.attr('data-index');

        if(typeof list_data[index].qty !== undefined) {
            var return_qty = list_data[index].qty * -1;    
            hitungQty(return_qty);
        }

        delete list_data[index];
        appendData(list_data);
    });

    $("#btn-submit-split-po").on('click', function() {
        var url = "/pengadaan/info-purchase-order/split-po-save";
        var detail = [];

        $.each(list_data, function(index, row) {
            if (row == undefined) {
                return;
            }

            if(row.harga != null && row.harga != '-') {
                var harga = parseFloat(row.harga);
            } else {
                var harga = null;
            }

            // remove separator
            var qty_input = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(row.qty.replace(/[^\d,]/g, ""));

            var _detail = {
                "supplier_id"   : row.supplier_id,
                "qty"           : qty_input[1],
                "satuan_id"     : row.satuan_id, 
                "harga"         : harga
            };

            detail.push(_detail);
        });

        if ($.isEmptyObject(detail)) {
            docoNotification('warning', "Data Tidak Lengkap", "List PO kosong.");
            return false;
        }

        $(this).docoForm("click", {
            url: url,
            skipErrorNotif: true,
            data: {
                item_id: obat_barang_id,
                po_detail: po_detail,
                pr_nomor: pr_nomor,
                detail: detail,
                type_po: type_po
            },
            success: function(data) {
                setTimeout(function() {
                    window.location.reload();
                }, 850);
            }
        });
    });

    function hitungQty(qty) {
        qty_sisa = qty_sisa - qty;
    }

    function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
        try {
            decimalCount = Math.abs(decimalCount);
            decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

            const negativeSign = amount < 0 ? "-" : "";

            let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
            let j = (i.length > 3) ? i.length % 3 : 0;

            return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
        } catch (e) {
            console.log(e)
        }
    };
});
