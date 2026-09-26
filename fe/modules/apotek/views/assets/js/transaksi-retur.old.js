/*
    Author : Randy Vianda Putra (aweutist)
*/

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
                    var total_pembelian = parseInt(y.hargasatuan_oa) * parseInt(y.qty_oa);
                    var disable = (parseInt(y.qty_oa) == 0) ? 'disabled=disabled' : '';
                    var _idParent = y.obatalkespasien_id;
                    _no++;
                    _html += "<tr class=\"resep\" id='"+ _idParent +"'>";
                    _html += "<td style='display:none;'><input name='penjualan_id' type='hidden' class='penjualan_id' data-id='" + y.penjualanresep_id + "' value='" + y.penjualanresep_id + "'></td>";
                    _html += "<td class=\"numbering\">" + _no + "</td>";
                    _html += "<td>" + y.obatalkes_namalain + "</td>";
                    _html += "<td>" + y.tglkadaluarsa + "</td>";
                    _html += "<td class='text-right'>" + docoHelper.convertToRupiah(y.hargasatuan_oa) + "</td>";
                    _html += "<td width=\"5%\" id=\"qty_jual" + x + "\" class=\"qty_jual text-right\">" + y.qty_oa + "</td>";
                    _html += "<td class=\"harga text-right\">" + docoHelper.convertToRupiah(total_pembelian) + "</td>";
                    _html += "<td width=\"5%\" class=\"\"><input " + disable + " type=\"checkbox\" id=\"retur" + x + "\" data-id=\"" + x + "\" class=\"styled is_retur\"></td>";
                    _html += "<td width=\"10%\" class=\"text-right\" data-sub=\"" + total_pembelian + "\"><input type=\"text\" name=\"qty_retur[" + y.stokobatalkesasal_id + "]\" id=\"qty" + x + "\" data-qty=\"" + y.qty_oa + "\" data-id=\"" + x + "\" data-error=\"1\" data-harga=\"" + y.hargasatuan_oa + "\" disabled class=\"form-control qty_retur text-right\"><span class=\"error error" + x + "\"></span></td>";
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
        $('.qty_retur').prop('disabled', true);
        $(".is_retur").change(function () {
            // console.log('check')
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
                $(".qty_retur").trigger('keyup');
                if (typeof transRetur[_idParent] != "undefined") {
                    transRetur[_idParent].qty_retur = 0;
                }
            }
        });

        $(".qty_retur").on("keyup", function () {
            var _parent = $(this).closest('tr');
            var _idParent = _parent.attr('id');
            var id = $(this).data('id');
            var harga = $(this).data('harga');
            var qty_jual = $(this).data('qty');
            var qty_retur = $(this).val();

            if (parseInt(qty_retur) > parseInt(qty_jual)) {
                qty_retur = parseInt(qty_jual);
            } else if (parseInt(qty_retur) == 0) {
                $('#qty' + id).attr('data-error', 1);
                $('.error' + id).text('');
                $('#qty' + id).removeAttr('style');
            } else {
                $('#qty' + id).removeAttr('style');
                $('#qty' + id).attr('data-error', 0);
                $('.error' + id).text('');
            }
            qty_retur = parseInt(docoHelper.convertToAngka(qty_retur));
            if (isNaN(qty_retur)) {
                qty_retur = 0;
                $('#qty' + id).attr('data-error', 1);
            }
            $(this).val(qty_retur);
            if (typeof transRetur[_idParent] != "undefined") {
                transRetur[_idParent].qty_retur = parseInt(qty_retur);
            }
            var total_retur = parseInt(qty_retur) * parseInt(harga);
            if (isNaN(total_retur)) {
                total_retur = 0;
            }
            $('#total-retur' + id).html('' + docoHelper.convertToRupiah(total_retur));
            $('#total-retur' + id).attr('data-sub', total_retur);
            sumRetur();
        });
    }

    function sumRetur() {
        var sum_subtotalItem = 0;
        $.each($(".total-sum"), function () {
            $("#subtotalItem").html("0");
            var value = parseInt($(this).attr('data-sub'));
            if (isNaN(value)) {
                value = 0;
            }
            sum_subtotalItem += value;
        });
        $("#subtotalItem").html(docoHelper.convertToRupiah(sum_subtotalItem));
        $("#subtotalItem").attr('data-total', sum_subtotalItem);
    }

    $("#save-retur").on('click',function (event) {
        var url = '/apotek/transaksi-retur/save?id=' + penjualan_id;
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