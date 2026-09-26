/**
 * @author : Asri
 * Powered by Sirs
 */

var _group = {};
var isEditReseptur = isEditResep = false;
var type;
var pathEditReseptur = '/apotek/informasi-reseptur/view';
var sum_subtotalNetto = 0;
var mapping_det = [];
var det_arr = [];
var det_kronis = [];
var count_list = [];
var catatan = [];
var qty = [];
var signa = [];
var _editSigna = [];
var apotek = { list_stok: {} };
var apotek_nr = { list_stok: {} };
if(window.location.pathname == pathEditReseptur){
    isEditReseptur = true;
    type = 5;
}

$(document).ready(function () {
    if(isEditReseptur){
        appendObat(transObat);
    }

    sumHarga();
    function appendObat(object, list_error = []) {
        var _no = 0;
        var _html = "";
        count_list = object;
        $(".default-value").attr("style", "display:none");
        $.each(object, function (x, y) {
            if (typeof object[x] !== "undefined" && object !== '') {
                catatan[y.posisi] = (typeof catatan[y.posisi] == 'undefined') ? y.etiket : catatan[y.posisi];
                qty[y.posisi] = (typeof qty[y.posisi] == 'undefined') ? y.qty : qty[y.posisi];
                signa[y.posisi] = (typeof signa[y.posisi] == 'undefined') ? y.signa : signa[y.posisi];
                var out_of_stock = list_error.indexOf(parseInt(y.obatalkes_id)) > -1 ? "out-stock" : "";
                var is_deleted_row = y.is_deleted ? " strikeout" : "";
                det_arr[y.posisi] = (typeof det_arr[y.posisi] == 'undefined') ? (typeof y.det != 'undefined' ? y.det : y.qty) : det_arr[y.posisi];
                var qty_hitung = Math.ceil(det_arr[y.posisi]);
                var subtotal_obat = y.harga * qty_hitung;
                var val_catatan = catatan[y.posisi] !== 'undefined' ? catatan[y.posisi] : y.etiket;
                var val_qty = qty[y.posisi] !== 'undefined' ? qty[y.posisi] : y.qty;
                var val_signa = signa[y.posisi] !== 'undefined' ? signa[y.posisi] : y.signa;
                var val_hari = y.hari;
                var totalharga_netto = docoHelper.convertToAngka(y.harganetto) * qty_hitung;
                var nama_racikan = (y.nama_racikan == null || y.nama_racikan == "" || typeof y.nama_racikan == "undefined" ? "" : " - " + y.nama_racikan);
                var satuan_racikan_nama = (y.satuan_racikan_nama == null || y.satuan_racikan_nama == "" || typeof y.satuan_racikan_nama == "undefined" ? "" : " - " + y.satuan_racikan_nama);
                var qty_racikan = (y.qty_racikan == null || y.qty_racikan == "" || typeof y.qty_racikan == "undefined" ? "" : " - " + y.qty_racikan + " " + satuan_racikan_nama);
                var identifier = y.racikan_id == 1 ? y.racikan_id + "-" + y.r_ke : "nonracikan";

                _no++;
                _html += "<tr class='resep "+ out_of_stock + is_deleted_row + " " + identifier +"'>";
                    _html += "<td style='display:none;'><input type='hidden' class='list_barang' data-id='" + y.r_ke + "-" + y.obatalkes_id + "-" + y.racikan_id + "' value='" + y.obatalkes_id + "'></td>";
                    _html += "<td class=\"numbering\">" + _no + "</td>";
                    _html += '<td colspan="2">' + ((y.r_ke == null) ? '-' : y.r_ke + nama_racikan + qty_racikan) + "</td>";
                    _html += "<td>" + y.obatalkes_nama + "</td>";
                    _html += "<td align=\"right\">" + docoHelper.convertToRupiah(y.harga) + "</td>";
                    _html += "<td class='hari' align='right' style='min-width: 90px;'><select data-hari-"+y.posisi+"=\""+val_hari+"\" data-position=\"" + y.posisi + "\" class=\"selectHari-" + y.posisi + " changeHari form-control select2\" name=\"hari\" ></select></td>";
                    _html += "<td class=\"signa\"><select data-signa-"+y.posisi+"=\""+val_signa+"\" data-position=\"" + y.posisi + "\" class=\"selectSigna-" + y.posisi + " changeSigna form-control select2\" name=\"signa\" ></select></td>";

                    _html += `<td class="qty" align="right">
                        <input
                            type="text"
                            class="form-control input-qty"
                            size="1"
                            value="`+ val_qty +`"
                            data-qty="`+ y.posisi +`"
                            style="text-align: right; margin-bottom:5%"
                        />
                        </td>`;
                    _html += "<td class=\"satuan\" align=\"left\">" + y.satuan_input + "</td>";
                    _html += `<td class="catatan" align="right">
                        <input
                            type="text"
                            class="form-control catatan"
                            size="8"
                            value="`+ val_catatan +`"
                            data-catatan="`+ y.posisi +`"
                            style="text-align: right; margin-bottom:5%"
                        />
                        </td>`;
                    
                    _html += "<td align=\"right\" class=\"total_harga\" data-subindex=\""+y.posisi+"\" data-netto=\""+totalharga_netto+"\" data-sub=\"" + subtotal_obat + "\">" + docoHelper.convertToRupiah(subtotal_obat) + "</td>";
                    if(isEditReseptur){
                        _html += "<td><a class=\"btn btn-danger deleted\" data-rdId=\"" + y.resepturdetail_id + "\" data-id=\"" + y.posisi + "\"><i class=\"fa fa-trash\"></i></a></td>";
                    } else if(isEditResep) {
                        _html += "<td><a class=\"btn btn-danger deleted\" data-rdId=\"" + y.obatalkespasien_id + "\" data-id=\"" + y.posisi + "\"><i class=\"fa fa-trash\"></i></a></td>";
                    }
                _html += "</tr>";
            }

            $(document).ready(function () {
                var signaList = [];
                $(".selectSigna-" + y.posisi).select2InfinityScroll({
                    url: "/apotek/transaksi-resep/source-data",
                    callbackData: (params) => {
                        // auto-select selected value
                        if(typeof(params.term) == "undefined" && params.term == null) {
                            let term = $(".selectSigna-" + y.posisi).attr("data-signa-"+y.posisi);
                            $(".select2-search__field").val(null)
                            $(".select2-search__field").val(term)
                            params.term = term;
                        }
                        return {
                            payload: {
                               ...params,
                            }
                        }
                    },
                    callbackProccess: (data) => {
                        let resultProccess = {
                            pagination: data.pagination,
                            results: []
                        }
                        data.results.map((itemData) => {
                            resultProccess.results.push({
                                id: itemData.signa_id,
                                text: itemData.signa_kode +" "+ itemData.signa_nama,
                                ...itemData,
                                // disable: itemData.status == 1
                            })
                        })
                        signaList = resultProccess;
                        return resultProccess
                    }
                })

                var _signaName = (y.signa_id != null) ? y.signa_id : y.signa;
                var _options = new Option(y.signa, _signaName, false, false);
                $('.selectSigna-' + y.posisi).append(_options).val(_signaName).trigger('change')

                $(".selectSigna-" + y.posisi).on("select2:selecting", function(e){
                    var _args = e.params.args;
                    _editSigna[y.posisi] = _args.data;
                })
                $(".selectSigna-" + y.posisi).on("change", function(){
                    $.each(signaList.results, function(index, value) {
                        if(value.id == $(".selectSigna-" + y.posisi).val()) {
                            signa[y.posisi] = value.signa_nama;
                            $(".selectSigna-" + y.posisi).attr("data-signa-"+y.posisi, value.signa_nama)
                        }
                    });
                    recalculateQty(y, $(this), identifier);
                });

                $(".selectHari-" + y.posisi).select2({
                    placeholder: 'Hari'
                });

                var dataHariOptions = [];
                for (var i = 1; i <= 31; i++) {
                    let dataOptions = {
                        'id': i,
                        'text': i
                    };
                    dataHariOptions.push(new Option(dataOptions.text, dataOptions.id, false, false));
                }
            
                $(".selectHari-" + y.posisi).append(dataHariOptions).change();
                $(".selectHari-" + y.posisi).val(y.hari).change();
                $(".selectHari-" + y.posisi).bind('select2:select', function({delegateTarget}){
                    if(y.racikan_id == 1) { // obat racikan
                        let bundleRacikan = $("." + identifier);
                        $.each(bundleRacikan, function(key, val) {
                            let valHari = $(".selectHari-" + y.posisi).val();
                            bundleRacikan.eq(key).find(".changeHari").val(valHari).change();
                        })
                    }
                    recalculateQty(y, $(this), identifier);
                })
            })

            object[x] = y;
            $(document).on("input", "[data-qty=" + y.posisi + "]", function (e) {
                match = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
                this.value = match[1] + match[2];

                qty[y.posisi] = parseFloat($(this).val());
                var qty_value = parseFloat($(this).val());
                if (qty_value == 0) {
                    docoNotification("warning", i18next.t("Perhatian"), i18next.t("Qty tidak boleh 0"));
                    return false;
                }
            })

            $(document).on("click", ".hasil_kronis" + y.posisi, function (e) {
                $(".racikan" + y.r_ke).each(function(index, val){
                    det_kronis[$(this).attr("data-kronis")] = $(".hasil_kronis" + y.posisi).is(':checked');
                })
                e.preventDefault();
                $.showQuestionDialog(header, message, label, function(reaction) {
                    if (reaction == 'Yes') {
                        if(y.r_ke == "-"){
                            if($(".hasil_kronis" + y.posisi).is(':checked') == false){
                                $(".hasil_kronis" + y.posisi).prop("checked", true);
                            }else{
                                $(".hasil_kronis" + y.posisi).prop("checked", false);
                            }
                        }else{
                            if($(".racikan" + y.r_ke).is(':checked') == false){
                                $(".racikan" + y.r_ke).prop("checked", true);
                            }else{
                                $(".racikan" + y.r_ke).prop("checked", false);
                            }
                        }
                    }
                });
            })

            $(document).on("input", "[data-catatan=" + y.posisi + "]", function (e) {
                catatan[y.posisi] = $(this).val();
            })
            
            $(document).on("change", "[data-qty="+ y.posisi +"]", function(e){
                // validasi jumlah objek dengan jumlah item det pada array
                    match        = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
                    this.value   = match[1] + match[2];

                    var det_value = parseFloat($(this).val());
                    var qty_value = parseFloat(y.qty);

                    // validasi field DET
                    if(det_value == ""){
                        this.value = parseFloat(0);
                    }


                    // update det value
                    det_arr[y.posisi] = det_value.toString();
                    det_kronis[y.posisi] = $(".hasil_kronis").is(':checked');
                    qty[y.posisi] = parseFloat($(this).val());

                    // kalkulasi ulang subtotal
                    var subtotal_hargajual = parseFloat(y.harga) * Math.ceil(det_value);
                    var subtotal_harganetto = parseFloat(y.harganetto) * Math.ceil(det_value);
                    subtotal_hargajual = parseFloat(subtotal_hargajual).toFixed(2);
                    subtotal_harganetto = parseFloat(subtotal_harganetto).toFixed(2);
                    if(subtotal_hargajual == NaN || Number.isNaN(subtotal_hargajual)){
                        subtotal_hargajual = 0;
                    }
                    if(subtotal_harganetto == NaN || Number.isNaN(subtotal_harganetto)){
                        subtotal_harganetto = 0;
                    }
                    $("[data-subindex="+ y.posisi +"]").text(docoHelper.convertToRupiah(subtotal_hargajual));
                    $("[data-subindex="+ y.posisi +"]").attr("data-sub", subtotal_hargajual);
                    $("[data-subindex="+ y.posisi +"]").attr("data-netto", subtotal_harganetto);
                    y.subtotal = subtotal_hargajual;
                    sumHarga();
            });
        });

        if (_html === "") {
            _html += "<tr>";
            _html += "<td colspan=\"9\" id=\"data-null\" class=\"text-center\">Data Tidak Ditemukan</td>";
            _html += "</tr>";
        }
        
        $("#list-obat-kronis").html("");
        $("#list-obat-kronis").prepend(_html);
        sumHarga();
    }

    function ajaxLoading(element) {
        $(element).attr("disabled", true);
        $(element).html("<i class=\"fa fa-spinner fa-pulse fa-1x fa-fw\"></i>");
    }

    function ajaxAfterLoading(element, text) {
        $(element).attr('disabled', false);
        $(element).html(text);
    }

    function recalculateQty(y, el, identifier) {
        let index = el.parents('tr').index();
        let hari = parseFloat($(".selectHari-" + y.posisi).val());
        var signaId = $(".selectSigna-" + y.posisi).val() ? $(".selectSigna-" + y.posisi).val() : null;
        if (masterSigna[signaId] != undefined && _editSigna[y.posisi] == undefined) {
            _editSigna[y.posisi] = masterSigna[signaId];
        }
        let qtyObat = _editSigna[y.posisi] != undefined ? parseFloat(_editSigna[y.posisi].qty_obat) : 0;
        let iterasi = _editSigna[y.posisi] != undefined ? parseFloat(_editSigna[y.posisi].iterasi) : 0;
        let kebutuhan = y.racikan_id != 2 ? 1 : 1;
        let result = parseFloat(qtyObat * iterasi * hari * kebutuhan).toFixed(2);
        result = !isNaN(result) ? result : 0;
        
        if(y.racikan_id == 1) { // obat racikan
            let bundleRacikan = $("." + identifier);
            $.each(bundleRacikan, function(key, val) {
                bundleRacikan.eq(key).find('.input-qty').val(result).change();
            })
        } else {
            $('.qty').eq(index).find('.input-qty').val(result).change();
        }
    }

    function sumHarga(){
        var sum_subtotalItem = 0;

        $.each($(".total_harga").not(".removed_row"), function () {
            var value = parseFloat($(this).attr("data-sub"));
            var netto = parseFloat($(this).attr("data-netto"));
            sum_subtotalItem += value;
            sum_subtotalNetto += netto;
        });
        $(".subTotalItem").val(sum_subtotalItem);
        $(".totalharga_netto").val(sum_subtotalNetto);
        $(".subtotal").html(docoHelper.convertToRupiah(sum_subtotalItem));
        $(".total").html(docoHelper.convertToRupiah(sum_subtotalItem));
    }

    // Simpan
    $("#generate-resep-kronis").on("click", function (e) {
        e.preventDefault();

        var _url = "/apotek/informasi-reseptur/generate-kronis?id="+id;
        $(this).docoForm("click", {
            url: _url,
            data: {
                nama_pembeli: pasien,
                dokter: dokter,
                reseptur_id: reseptur_id,
                penjualanresep_id: penjualanresep_id,
                total_obat: $(".subTotalItem").val(),
                biayaadministrasi: biayaAdmin,
                sep: sep,
                val_catatan: catatan,
                val_qty: qty,
                val_signa: signa,
                totalharga_netto: $(".totalharga_netto").val(),
                penjamin_id: $(".penjaminId").val(),
                carabayar_id: $(".carabayarId").val(),
                antrian_id: antrian,
                pasien_id: _data_resep.pasien_id,
                no_pendaftaran: _data_resep.no_pendaftaran
            },
            success: function(data) {
                onUpdateSuccess();
            },
            error: function(response) {
                var respon = response.responseJSON;
                if(typeof respon != undefined) {
                    docoNotification('error', 'Proses Gagal', respon.response.message);
                } else {
                    docoNotification('error', 'Proses Gagal', 'Terjadi Kesalahan');
                }
            }
        })
    });

    function onUpdateSuccess() {
        (new PNotify({
            title: "Berhasil",
            text: "Resep baru berhasil di tambahkan!",
            addclass: "alert alert-success alert-arrow-right alert-styled-right",
            type: "success",
            buttons: {
                closer: false,
                sticker: false
            },
            hide: false,
            confirm: {
                confirm: false
            },
            history: {
                history: false
            }
        }));

        setTimeout(function(){
            window.location.href = "/apotek/informasi-reseptur/#";
        }, 3000);
    }
    $(document).on("change", ".changeSigna", function (event) {
        event.preventDefault();
        var _signaTNama = $(this).find('option:selected').text();
        var _signaId = $(this).val();
        var _position = $(this).data('position');
        $.ajax({
            url: "/apotek/transaksi-resep/update-cache-edit",
            type: "post",
            data:  {
                signa_id: _signaId,
                signa_nama: _signaTNama,
                position: _position
            },

            success: function(response) {
                return true;
            },
            error: function (response) {
                return false;
            },
        });

    });

    // Hapus Obat
    $(document).on("click", ".deleted", function (event) {
        event.preventDefault();
        var id_ = $(this).data("id");
        var button = this;
        var resepturdetail_id = $(this).data("rdid");
        if(isEditReseptur) {
            var ResData = {
                "id" : id_,
                "type" : type,
                "resepturdetail_id" : resepturdetail_id
            };
        } else if(isEditResep) {
            var ResData = {
                "id" : id_,
                "type" : type,
                "obatalkespasien_id" : resepturdetail_id
            };
        }
        
        if (count_list.length <= 1) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Jumlah obat tidak boleh kurang dari 1"));
            return false;
        }

        $(this).docoForm('click',{
            url: "/apotek/informasi-reseptur/mark-deleted",
            confirmTitle: i18next.t("Konfirmasi"),
            confirmMessage: i18next.t("Apa anda yakin ingin membatalkan data ini?"),
            data: ResData,
            method: "GET",
            before: function () {
                $(button).html("<i class=\"fa fa-spin fa-spinner\"></i>");
                $(button).prop("disabled", true);
            },
            success: function (data) {
                transObat = data.detail;
                appendObat(transObat);
            }
        });
    });
});
