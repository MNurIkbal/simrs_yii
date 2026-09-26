/*
    Author : Randy Vianda Putra (aweutist)
*/

var sorted_obat = [];
var arr_id = [];
$(document).ready(function () {
    appendObat(transRetur);
    setValidator();
    $('.pickadate').pickadate({
        format: 'dd mmmm yyyy',
        onStart: function () {
            var date = new Date()
            this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
        }
    });

    function alertApotek(title, message, type, element) {
        new PNotify({
            title: i18next.t(title),
            text: i18next.t(message),
            addclass: 'alert alert-' + element + ' alert-arrow-right alert-styled-right',
            type: type
        });
    }

    function ajaxLoading(element) {
        $(element).attr("disabled", true);
        $(element).html("<i class=\"fa fa-spinner fa-pulse fa-1x fa-fw\"></i>");
    }

    function ajaxAfterLoading(element, text) {
        $(element).attr('disabled', false);
        $(element).html(text)
    }

    function appendObat(object) {
        var _no = 0;
        var _html = "";
        $(".default-value").attr("style", "display:none");
        if (typeof object !== 'undefined') {
            $.each(object, function (x, y) {
                if (typeof object[x] !== "undefined") {
                    var total_retur = 0;
                    var total_pembelian;
                    var disable = (parseInt(y.qty_oa) == 0) ? 'disabled=disabled' : '';
                    var r_disable = y.racikan_id == 2 ? "" : "disabled";
                    var _idParent = y.obatalkespasien_id;
                    var nilai_konversi = parseFloat(y.nilai_konversi);
                    var qty_besar = 0;
                    if(y.det != null) {
                        qty_besar = parseFloat(y.det);
                    } else {
                        qty_besar = parseFloat(y.qty_oa);
                    }
                    total_pembelian = parseFloat(y.hargasatuan_oa) * parseFloat(qty_besar);

                    _no++;
                    _html += "<tr class=\"resep\" id='"+ x +"'>";
                    _html += "<td style='display:none;'><input name='penjualan_id' type='hidden' class='penjualan_id' data-id='" + y.penjualanresep_id + "' value='" + y.penjualanresep_id + "'></td>";
                    _html += "<td class=\"numbering\">" + _no + "</td>";
                    _html += "<td>" + (y.racikan_id == 2 ? "-" : y.rke) + "</td>";
                    _html += "<td>" + y.obatalkes_nama + "</td>";
                    _html += "<td class='text-right'>" + docoHelper.convertToRupiah(y.hargasatuan_oa) + "</td>";
                    _html += "<td class=\"text-left\"  id=\"qty_jual" + x + "\" class=\"qty_jual text-right\">" + qty_besar + " " + y.satuan_input + "</td>";
                    _html += "<td class=\"harga text-right\">" + docoHelper.convertToRupiah(total_pembelian) + "</td>";
                    _html += `<td class="text-right" data-sub="` + total_pembelian + `">
                        <input type="text" name="qty_retur[` + y.stokobatalkesasal_id + `]" id="qty` + x + `"
                                data-konversi="`+ nilai_konversi +`" data-qty="` + qty_besar + `" data-id="` + x + `"
                                data-error="1" data-harga="` + y.hargasatuan_oa + `" class="form-control qty_retur text-right"
                                value="0"`+ r_disable +`>
                                </td>`;
                    _html += "<td id=\"total-retur" + x + "\" class=\"total-sum text-right\">" + total_retur + "</td>";
                    _html += "</tr>";
                    if (parseInt(y.qty_oa) == 0) {
                        $('#retur' + x).hide();
                    }

                }
                object[x] = y;

            });
        }

        if (_html === "") {
            _html += "<tr>";
            _html += "<td colspan=\"10\" id=\"data-null\" class=\"text-center\">Data Tidak Ditemukan</td>";
            _html += "</tr>";
        }
        $("#list-obat").html("");
        $("#list-obat").prepend(_html);
        var sum_subtotalItem = 0;
        $.each($(".total-sum"), function () {
            var value = parseInt($(this).attr("data-sub"));
            if (isNaN(value)) {
                value = 0;
            }
            sum_subtotalItem += value;
        });
        $("#subtotalItem").html("" + docoHelper.convertToRupiah(sum_subtotalItem));
        $("#subtotalItem").attr('data-total', sum_subtotalItem);
        $(".styled, .multiselect-container input").uniform({
            radioClass: 'choice'
        });
    }

    function setValidator() {
        $(".is_retur").change(function () {
            var _parent = $(this).closest('tr');
            var _idParent = _parent.attr('id');
            var id = $(this).data('id');
            var is_retur = $('#retur' + id).is(":checked");
            if (is_retur) {
                $('#qty' + id).prop('disabled', false);
            } else {
                $('#qty' + id).prop('disabled', true);
                var total_retur = 0;
                $('#qty' + id).val(total_retur);
                $('#total-retur' + id).html('' + docoHelper.convertToRupiah(total_retur));
                $('#total-retur' + id).attr('data-sub', total_retur);
            }
        });

        $(".qty_retur").on("input", function () {
            match        = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
            this.value   = match[1] + match[2];

            var _parent = $(this).closest('tr');
            var _idParent = _parent.attr('id');
            var id = $(this).data('id');
            var harga = $(this).data('harga');
            var konversi = parseFloat($(this).data('konversi'));
            var qty_jual = parseFloat($(this).data('qty'));
            var qty_retur = parseFloat($(this).val());

            if (qty_retur == "") {
                qty_retur = 0;
                this.value = qty_retur;
            }

            if(qty_retur > qty_jual) {
                qty_retur = qty_jual;
                this.value = qty_retur;
            }

            if(isFloat(qty_retur)){
                this.value = Math.ceil(qty_retur);
            }

            if (typeof transRetur[_idParent] != "undefined") {
                transRetur[_idParent].qty_retur = Math.ceil(qty_retur);
            }

            var total_retur = Math.ceil(qty_retur) * parseFloat(harga);
            if (isNaN(total_retur)) {
                total_retur = 0;
            }

            $('#total-retur' + id).html('' + docoHelper.convertToRupiah(total_retur));
            $('#total-retur' + id).attr('data-sub', total_retur);
            sumRetur();
        });
    }

    function isFloat(n){
        return Number(n) === n && n % 1 !== 0;
    }

    function sumRetur() {
        var sum_subtotalItem = 0;
        $.each($(".total-sum"), function () {
            $("#subtotalItem").html("0");
            var value = parseFloat($(this).attr('data-sub'));
            if (isNaN(value)) {
                value = 0;
            }
            sum_subtotalItem += value;
        });
        $("#subtotalItem").html(docoHelper.convertToRupiah(sum_subtotalItem));
        $("#subtotalItem").attr('data-total', sum_subtotalItem);
    }

    $("#save-retur").on('click',function (event) {
        var url = '/apotek/transaksi-retur/save?no_resep=' + no_resep;
        $(this).docoForm("click", {
            url: url,
            data: {
                data_retur : transRetur
            },
            success: function (data) {
                var no_returresep = data.response.no_returresep;
                var id_retur = data.response.id_retur;
                var data_update = data.response.data_retur;
                appendObat(data_update);
                (new PNotify({
                    title: "Berhasil",
                    text: "Retur berhasil disimpan dengan No."+ no_returresep +", apakah Anda ingin melakukan cetak?",
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: "success",
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    hide: false,
                    confirm: {
                        confirm: true,
                        buttons: [
                            {
                                text: 'Ya',
                                addClass: 'btn btn-xs btn-success',
                            },
                            {
                                text: 'Tidak',
                                addClass: 'btn btn-xs btn-danger',
                            }
                        ]
                    },
                    history: {
                        history: false
                    }
                })).get().on('pnotify.confirm', function() {
                    window.open("/apotek/transaksi-retur/cetak-pdf?id=" + id_retur);
                    location.reload();
                }).on('pnotify.cancel', function() {
                    location.reload();
                });

                setTimeout(function(){
                    window.location.href = "/apotek/informasi-reseptur/#";
                }, 3000);
            },
            error: function(res) {
                var _res = res.responseJSON.response

                if (_res.data != undefined) {
                    var messages = _res.data
                    $.each(messages, function (key, val) {
                        var resep = $(`.noresep-${val}`)

                        $.each(resep, function (key2, val2) {
                            $(val2).attr('style','background: #F1948A')
                        })
                    })
                } 
                
                if (_res.text != undefined) {
                    alertApotek("Gagal", _res.text, "error", "danger");
                    setTimeout(function(){
                        location.reload()
                    },5000);
                }
            }
        });
    });

    $(document).on("click", ".ulang", function (e) {
        e.preventDefault();
        location.reload();
        return false;
    });

    $('#btn-print').prop('disabled', true);
    $('#btn-print').on('click', function () {
        var link = $(this).attr('data-target');

        if (typeof link !== 'undefined') {
            window.location.href = link;
        } else {
            $('#btn-print').prop('disabled', true);
        }
    });

})