$(document).ready(function () {
    var arrayData = [];
    var disabledDataResep = [];
    var disabledDataBmhp = [];

    appendObat(transaksiResep)
    appendBmhp(transaksiBmhp)
    sumRetur('resep');
    sumRetur('bmhp');
    
    if(typeRetur == "lihat") {
        verifType = "verifikasi";
        $("#ruangan_retur").attr("disabled", true);
        $("#id_retur").attr("disabled", true);
        $("#save-retur").addClass('hidden');
        $(".data-edit").removeClass('hidden');
        $(".btn-log-activity").removeClass('hidden');
        $("#batal-retur").removeClass('hidden');
        $(".qty_retur").attr("disabled", true);
        $(".alasan_edit").addClass('hidden');
        $(".qty_retur_bmhp").attr("disabled", true);
        $("#ruangan_retur").val(ruangan_id).trigger("change");
        $("#tanggal_retur").val(tgl_retur).trigger("change");
    }
    if(typeRetur == "tambah"){
        $("#ruangan_retur").attr("disabled", false);
        $("#id_retur").attr("disabled", false);
        $("#save-retur").removeClass('hidden');
        $(".btn-log-activity").addClass('hidden');
        $(".alasan_edit").addClass('hidden');
        $(".data-edit").addClass('hidden');
        $(".qty_retur").attr("disabled", false);
        $(".qty_retur_bmhp").attr("disabled", false);
    }
    
    $('#edit-retur').on("click", function () {  
        $.each(disabledDataResep, function (key, value) {
            $(`#qty_retur_resep_${key}`).attr("disabled", value);
        });
        $.each(disabledDataBmhp, function (key, value) {
            $(`#qty_retur_bmhp_${key}`).attr("disabled", value);
        });
        typeRetur = 'edit';
        verifType = "simpan-verifikasi";
        const btn = document.getElementById('verifikasi');
        btn.innerHTML = '<b><i class = "fa fa-check"></i></b> Simpan & Verifikasi';
        $('#alasan_edit').removeClass("hidden")
        $("#batal-retur").addClass('hidden');
        $("#ruangan_retur").attr("disabled", false);
        $("#id_retur").attr("disabled", false);
        $("#save-retur").removeClass('hidden');
        $(".btn-log-activity").removeClass('hidden');
        $(".data-edit").addClass('hidden');
        $("#ruangan_retur").val(ruangan_id).trigger("change");
        $("#tanggal_retur").val(tgl_retur).trigger("change");
    });
    
    /**
     * Append data Obat.
     * 
     * @author Novia.S.
     * @param {object} object 
     */
    function appendObat(object) {
        var _no = 0;
        var _html = "";
        var dataResep = [];
        $(".default-value").attr("style", "display:none");
        if (typeof object !== 'undefined') {
            $.each(object, function (x, y) {
                if (typeof object[x] !== "undefined") {

                    setTimeout(() => {
                        if(y.is_racikan) { 
                            $(`#qty_retur_resep_${x}`).prop("disabled", true)
                        }
                        dataResep.push(y.is_racikan ? true : false);

                        if(parseInt($(`#qty_retur_resep_${x}`).val()) > y.qty_resep && isReturPendaftaran) {
                            $(`#qty_retur_resep_${x}`).closest("tr").css("background-color", "#ffcece");
                        }
                    }, 500)

                    _no++;
                    _html += "<tr class=\"resep\" id='"+ x +"'>";
                    _html += "<td>" + _no + "</td>";
                    _html += "<td>" + y.obatalkes_nama + "</td>";
                    if(isReturPendaftaran) {
                        _html += "<td>" + y.qty_resep + "</td>";
                    }
                    _html += "<td>" + y.satuan_resep + "</td>";
                    _html += "<td style='text-align: right;'>"+docoHelper.numberFormat(parseInt(y.harga_satuan)) +"</td>";
                    if(typeRetur == "tambah"){
                        _html += "<td><input type='text' id='qty_retur_resep_"+x+"' autocomplete='off' data-id='"+x+"' data-value='"+ y.qty_resep +"' data-satuan='"+parseInt(y.harga_satuan)+"' class='form-control qty_retur text-right'></td>";
                        _html += "<td style='text-align: right;' data-sub-resep='0' id=total-obat-"+x+" class='total-sum'></td>";
                    }else{
                        _html += "<td><input type='text' id='qty_retur_resep_"+x+"' autocomplete='off' data-id='"+x+"' data-value='"+ y.qty_resep +"' data-satuan='"+parseInt(y.harga_satuan)+"' class='form-control qty_retur text-right' value='"+y.qty_retur+"'></td>";
                        _html += "<td style='text-align: right;' data-sub-resep='"+parseInt(y.harga_satuan) * y.qty_retur+"' id=total-obat-"+x+" class='total-sum'>"+docoHelper.numberFormat(parseInt(y.harga_satuan) * y.qty_retur)+"</td>";
                    }
                    _html += "</tr>";
                }
                object[x] = y;
            });
        }

        disabledDataResep = dataResep;
        let colspan;
        if(isReturPendaftaran) {
            colspan = 10;
        } else {
            colspan = 9;
        }
        if (_html === "") {
            _html += "<tr>";
            _html += "<td colspan=\""+ colspan +"\" id=\"data-null\" class=\"text-center\">Data transaksi resep tidak ditemukan</td>";
            _html += "</tr>";
        }
        $("#list-obat").html("");
        $("#list-obat").prepend(_html);
        $(".styled, .multiselect-container input").uniform({
            radioClass: 'choice'
        });
    }

    /**
     * Append data BMHP.
     * 
     * @author Maulana Muhammad Rizky.
     * @param {object} object 
     */
    function appendBmhp(object) {
        var _no = 0;
        var _html = "";
        var dataBmhp = [];
        $(".default-value").attr("style", "display:none");
        if (typeof object !== 'undefined') {
            $.each(object, function (x, y) {
                if (typeof object[x] !== "undefined") {
                    setTimeout(() => {
                        if(y.is_racikan) { 
                            $(`#qty_retur_bmhp_${x}`).prop("disabled", true)
                        }
                        dataBmhp.push(y.is_racikan ? true : false);
                        if(parseInt($(`#qty_retur_bmhp_${x}`).val()) > y.qty_resep) {
                            $(`#qty_retur_bmhp_${x}`).closest("tr").css("background-color", "#ffcece");
                        }
                    }, 500)

                    _no++;
                    _html += "<tr class=\"resep\" id='"+ x +"'>";
                    _html += "<td>" + _no + "</td>";
                    _html += "<td>" + y.obatalkes_nama + "</td>";
                    _html += "<td>" + y.qty_resep + "</td>";
                    _html += "<td>" + y.satuan_resep + "</td>";
                    _html += "<td style='text-align: right;'>" +docoHelper.numberFormat(parseInt(y.harga_satuan))+ "</td>";
                    if(typeRetur == "tambah"){
                        _html += "<td><input type='text' id='qty_retur_bmhp_"+x+"' autocomplete='off' data-id='"+x+"' data-value='"+ y.qty_resep +"' data-satuan='"+parseInt(y.harga_satuan)+"' class='form-control doco-number qty_retur_bmhp text-right'></td>";
                        _html += "<td style='text-align: right;' id=total-bmhp-"+x+" data-sub-bmhp='0' class='total-sum'></td>";
                    }else{
                        _html += "<td><input type='text' id='qty_retur_bmhp_"+x+"' autocomplete='off' data-id='"+x+"' data-value='"+ y.qty_resep +"' data-satuan='"+parseInt(y.harga_satuan)+"' class='form-control doco-number qty_retur_bmhp text-right' value='"+y.qty_retur+"'></td>";
                        _html += "<td style='text-align: right;' id=total-bmhp-"+x+" data-sub-bmhp='"+parseInt(y.harga_satuan) * y.qty_retur+"' class='total-sum'>"+docoHelper.numberFormat(parseInt(y.harga_satuan) * y.qty_retur)+"</td>";
                    }
                    _html += "</tr>";
                }
                object[x] = y;
            });
        }

        disabledDataBmhp = dataBmhp;
        if (_html === "") {
            _html += "<tr>";
            _html += "<td colspan=\"10\" id=\"data-null\" class=\"text-center\">Data transaksi resep tidak ditemukan</td>";
            _html += "</tr>";
        }
        $("#list-bmhp").html("");
        $("#list-bmhp").prepend(_html);
    }  

    function sumRetur(param) {
        var sum_subtotalItem = 0;   
        $.each($(".total-sum"), function () {
            $(`#subtotalItem${param.toLowerCase()}`).html("0");
            var value = parseFloat($(this).attr(`data-sub-${param}`));
            if (isNaN(value)) {
                value = 0;
            }
            sum_subtotalItem += value;
        });

        $(`#subtotalItem${param.toLowerCase()}`).html(docoHelper.convertToRupiah(sum_subtotalItem));
        $(`#subtotalItem${param.toLowerCase()}`).attr('data-total', sum_subtotalItem);
    }
    
    $('.qty_retur').on("input", function () {
        match        = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
        this.value   = match[1] + match[2];

        let qtyPemberian = parseInt($(this).attr("data-value"))
        let qtyId = $(this).attr("data-id")
        let qtyRetur = this.value
        let qtySatuan = $(this).attr("data-satuan")
        let _parent = $(this).closest('tr');
        let _idParent = _parent.attr('id');
        
        /**
         * Validasi Qty
         */
        if(qtyRetur > qtyPemberian) {
            $(this).val(qtyPemberian)
            qtyRetur = qtyPemberian
        }

        _parent.css("background-color", "");

        if (typeof transaksiResep[_idParent] != "undefined") {
            transaksiResep[_idParent].qty_retur = Math.ceil(qtyRetur);
        }

        let totalRetur = qtySatuan * qtyRetur;
        if(totalRetur >= 0) {
            $(`#total-obat-${qtyId}`).html(docoHelper.numberFormat(totalRetur))
            $(`#total-obat-${qtyId}`).attr('data-sub-resep', totalRetur)
        }
        sumRetur('resep')
    });

    $('.qty_retur_bmhp').on("input", function () {
        match        = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
        this.value   = match[1] + match[2];

        let qtyPemberian = parseInt($(this).attr("data-value"))
        let qtyId = $(this).attr("data-id")
        let qtyRetur = this.value
        let qtySatuan = $(this).attr("data-satuan")
        let _parent = $(this).closest('tr')
        let _idParent = _parent.attr('id')

        /**
         * Validasi Qty
         */
        if(qtyRetur > qtyPemberian) {
            $(this).val(qtyPemberian)
            qtyRetur = qtyPemberian
        }

        _parent.css("background-color", "");

        if (typeof transaksiBmhp[_idParent] != "undefined") {
            transaksiBmhp[_idParent].qty_retur = Math.ceil(qtyRetur);
        }

        let totalRetur = qtySatuan * qtyRetur;
        if(totalRetur >= 0) {
            $(`#total-bmhp-${qtyId}`).html(docoHelper.numberFormat(totalRetur))
            $(`#total-bmhp-${qtyId}`).attr('data-sub-bmhp', totalRetur)
        }
        sumRetur('bmhp')
    });

    $('#save-retur').on("click", function () {  
        let tanggal = $('#tanggal_retur').val()
        let ruangan = $('#ruangan_retur').val()
        let alasan_edit = $('#edit_alasan').val()
        let validate;

        assignDataResep();
        assignDataBmhp();

        if(typeRetur == "tambah"){
            validate = validateEmptyRuangan(ruangan) && validateEmptyTransaction() && validateQtyRetur();
        }else{
            validate = validateEmptyRuangan(ruangan) && validateEmptyTransaction() && validateQtyRetur() && validateEmptyAlasan(alasan_edit);
        }

        if(validate) {
            let url = `/apotek/informasi-retur/retur-resep?id=${pendaftaran_id}`
    
            if(typeRetur == 'edit') {
                url = `/apotek/informasi-retur/edit-retur-resep?id=${pendaftaran_id}`
                editRetur(url, arrayData, tanggal, ruangan)
            }
    
            if(typeRetur == 'tambah') {
                simpanRetur(url, arrayData, tanggal, ruangan)
                return false;
            }
        }
    });

    /**
     * Function ini untuk melakukan editsimpan retur resep.
     * 
     * @param {string} url 
     * @param {array} arrayData 
     * @param {string} tanggal 
     * @param {integer} ruangan 
     */
    function editRetur(url, arrayData, tanggal, ruangan) {
        let returresep_id = $('#returresep_id').val()
        add = $('#confirm-form').clone().removeClass('hidden');
        add.find('.input-pemakai').removeAttr('readonly');
        add.find('.input-pemakai').attr('value', '');
        add.find('.input-pemakai').attr('id', 'pemakai-validasi');
        add.find('.input-pemakai').attr('placeholder', 'Username');
        add.find('.input-sandi').attr('id', 'sandi-validasi');
        add = add.html();
        var header = 'Perhatian !'
        var message = 'Apakah anda yakin untuk menyimpan data ini ?' + add
        var label = {
            buttons: {
                'Yes': 'button-yes',
                'No': 'button-no'
            }, hidden:true
        };
        $.showQuestionDialog(header, message, label, function (reaction) {
            user = $('#pemakai-validasi').val();
            pass = $('#sandi-validasi').val();
            if (reaction == 'Yes') { 
                var user = $('#pemakai-validasi').val();
                var pass = $('#sandi-validasi').val();
                $().docoForm('click', {
                    url: url,
                    skipConfirm: true,
                    skipErrorNotif: true,
                    skipSuccessNotif: true,
                    data: {
                        data_retur : arrayData,
                        tanggal: tanggal,
                        ruangan: ruangan,
                        returresep_id: returresep_id,
                        alasan_edit: $('#edit_alasan').val(),
                        nama_pemakai: user,
                        katakunci_pemakai: pass
                    },
                    success: function (response) {
                        new PNotify({
                            title: "Success",
                            text: `Transaksi retur dengan di edit!`,
                            addclass: "alert alert-success alert-arrow-right alert-styled-right",
                            type: "success"
                        });

                        setTimeout(() => {
                            window.location.href = `/apotek/informasi-retur/index`
                        }, 1200)
                    },
                    error: function(jqXhr) {
                        if(jqXhr.responseJSON?.httpStatusCode == 422 || jqXhr.responseJSON?.httpStatusCode == 400) {
                            new PNotify({
                                title: jqXhr.responseJSON?.meta.title,
                                text: jqXhr.responseJSON?.message,
                                addclass: "alert alert-error alert-arrow-right alert-styled-right",
                                type: "error"
                            });
                        }
                    }
                })
            } 
            if (reaction == 'No') {
                hideQuestionDialog();
                $('[data-popup="tooltip"]').tooltip();
            }    
        }); 
    }

    /**
     * Function ini untuk melakukan simpan retur resep.
     * 
     * @param {string} url 
     * @param {array} arrayData 
     * @param {string} tanggal 
     * @param {integer} ruangan 
     */
    function simpanRetur(url, arrayData, tanggal, ruangan) {
        $('#save-retur').docoForm("click", {
            url: url,
            data: {
                data_retur : arrayData,
                tanggal: tanggal,
                ruangan: ruangan
            },
            skipSuccessNotif: true,
            skipErrorNotif: true,
            success: function (data) {
                new PNotify({
                    title: "Success",
                    text: `Transaksi retur dengan No. ${data.no_returresep} berhasil dibuat !`,
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: "success"
                });

                setTimeout(() => {
                    window.location.href = `/apotek/informasi-retur/index`
                }, 1200)
            },
            error: function(jqXhr) {
                if(jqXhr.responseJSON?.httpStatusCode == 422) {
                    new PNotify({
                        title: jqXhr.responseJSON?.title,
                        text: jqXhr.responseJSON?.text,
                        addclass: "alert alert-error alert-arrow-right alert-styled-right",
                        type: "error"
                    });
                }
            }
        });
    }

    function assignDataResep() {
        // Assign data resep.
        if(transaksiResep != null) {
            transaksiResep.map((value, index) => {
                if(value.is_racikan == false && value.qty_retur != undefined && value.qty_retur != 0) {
                    arrayData.push(value)
                }
            })
        }
    }

    function assignDataBmhp() {
        // Assign data BMHP.
        if(transaksiBmhp != null) {
            transaksiBmhp.map((value, index) => {
                if(value.is_racikan == false && value.qty_retur != undefined && value.qty_retur != 0) {
                    arrayData.push(value)
                }
            })
        }
    }

    function validateEmptyAlasan(alasan) {
        if(alasan == undefined || alasan == "") {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Alasan Edit tidak boleh kosong !"));
            return false;
        }
        return true;
    }

    function validateEmptyRuangan(ruangan) {
        if(ruangan == undefined || ruangan == "") {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Ruangan Belum Dipilih !"));
            return false;
        }
        return true;
    }

    function validateEmptyTransaction() {
        let valid = false;
        $.each(arrayData, function(key, value) {
            if(!valid && value.qty_retur > 0) {
                valid = true;
            }
        });

        if(!valid || arrayData.length == 0) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Qty Retur/BMHP tidak Boleh Kosong !"));
            return false;
        }

        return true;
    }

    function validateQtyRetur() {
        let validate = true;
        $.each(arrayData, function(key, value) {
            if(value.qty_retur > value.qty_resep) {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Qty Retur tidak boleh melebihi Total Pemberian !"));
                validate = false;
            }
        });
        
        return validate;
    }

    $('#verifikasi').on("click", function () {
        let tanggal = $('#tanggal_retur').val()
        let ruangan = $('#ruangan_retur').val()
        let alasan_edit = $('#edit_alasan').val()
        let validate;

        assignDataResep();
        assignDataBmhp();

        if(typeRetur == "lihat") {
            validate = validateEmptyRuangan(ruangan) && validateEmptyTransaction();
        } else if(typeRetur == "tambah"){
            validate = validateEmptyRuangan(ruangan) && validateEmptyTransaction() && validateQtyRetur();
        }else{
            validate = validateEmptyRuangan(ruangan) && validateEmptyTransaction() && validateQtyRetur() && validateEmptyAlasan(alasan_edit);
        }

        if(validate) {
            add = $('#confirm-form').clone().removeClass('hidden');
            add.find('.input-pemakai').removeAttr('readonly');
            add.find('.input-pemakai').attr('value', '');
            add.find('.input-pemakai').attr('id', 'pemakai-validasi');
            add.find('.input-pemakai').attr('placeholder', 'Username');
            add.find('.input-sandi').attr('id', 'sandi-validasi');
            add = add.html();
            var header = 'Perhatian !'
            if(typeRetur == "lihat") {
                var message = 'Apakah Anda yakin data sudah sesuai untuk diverifikasi?'
            }else{
                var message = 'Apakah Anda yakin data sudah sesuai untuk diverifikasi?' + add
            }
            var label = {
                buttons: {
                    'Yes': 'button-yes',
                    'No': 'button-no'
                }, hidden:true
            };
            $.showQuestionDialog(header, message, label, function (reaction) {
                user = $('#pemakai-validasi').val();
                pass = $('#sandi-validasi').val();
                if (reaction == 'Yes') { 
				    var user = $('#pemakai-validasi').val();
                    var pass = $('#sandi-validasi').val();
                    $().docoForm('click', {
                        url: '/apotek/informasi-retur/verifikasi',
                        skipConfirm: true,
                        skipSuccessNotif: true,
                        skipErrorNotif: true,
                        data: {
                            pendaftaran_id: pendaftaran_id,
                            data_retur : arrayData,
                            tanggal: tanggal,
                            ruangan: ruangan,
                            verif_type: verifType,
                            returresep_id: returResepId,
                            alasan_edit: alasan_edit,
                            nama_pemakai: user,
                            katakunci_pemakai: pass
                        },
                        success: function (response) {
                            docoNotification('success', 'Berhasil !', 'Retur resep berhasil diverifikasi');
                            setTimeout(() => {
                                window.location.href = `/apotek/informasi-retur/index`
                            }, 1200)
                        }
                    })
                } 
                if (reaction == 'No') {
                    hideQuestionDialog();
                    $('[data-popup="tooltip"]').tooltip();
                }    
            }); 
        }
    });

    $('#batal-retur').on("click", function () {
        $('#batal-retur').docoForm("click", {
            url: '/apotek/informasi-retur/batal-retur',
            data: {
                returresep_id: [returResepId]
            },
            confirmMessage: "Apakah Anda yakin akan membatalkan proses retur?",
            skipErrorNotif: true,
            success: function (data) {
                docoNotification('success', 'Berhasil !', 'Retur resep berhasil dibatalkan');
                setTimeout(() => {
                    window.location.href = `/apotek/informasi-retur/index`
                }, 1200)
            }
        });
    });
});