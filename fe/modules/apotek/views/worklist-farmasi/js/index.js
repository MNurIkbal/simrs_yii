$(document).ready(function () {
    var history = {}
    var status_keys = []
    var identifier = null
    var instalasiasal_id = null

    $("#search_no_resep").on("change", function(e) {
        $.ajax({
            url: "/apotek/worklist-farmasi/get-data-worklist",
            type: "get",
            data: {
                identifier: $(this).val()
            },
            success: function(response) {
                $(".worklist-resep").css("display", "block")
                $(".cancel-receipt").css("display", "none")
                $(".not-approved").css("display", "none")
                clearInfoResep()
                clearWorklist()
                history = null

                // populate header
                var header = response.header
                var detail = response.detail

                $(".search-pegawai").css("display", "block")

                // jika status belum dibayar untuk pasien RJ dan resep penjualan langsung,
                // tidak bisa proses worklist
                var disabled
                if(header.jenispenjualan_id == null) {
                    if(header.instalasi_id == 1) {
                        disabled = true
                    }
                } else {
                    if(header.instalasi_id == 1 || header.jenispenjualan_id == 343 || header.jenispenjualan_id == 345) {
                        disabled = true
                    }
                }

                if(header.instalasi_resptur_id != null) {
                    instalasiasal_id = header.instalasi_resptur_id;
                }

                if(header.status_bayar_id == 349 && header.status_reseptur_id != 432 && disabled) {
                    PNotify.removeAll()
                    new PNotify({
                        title: "Perhatian",
                        text: "Worklist belum dapat di proses karena resep belum dibayar",
                        addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                        type: "warning",
                        buttons: {
                            closer: true,
                            sticker: false,
                        },
                        hide: false
                    })
                    $("#search_pegawai").prop("disabled", true)
                } else {
                    PNotify.removeAll()
                    $("#search_pegawai").prop("disabled", false)
                }

                if(header.status_reseptur_id == 660 || header.status_reseptur_id == 432) {
                    $("#search_pegawai").prop("disabled", true)
                }

                if(header.status_reseptur_id == 432 || header.status_reseptur_id == 346) {
                    $(".search-pegawai").css("display", "none")
                }

                if(header.status_reseptur_id == 432) {
                    $(".cancel-receipt").css("display", "block")
                    $("#pesan_error").text("Resep ini telah dibatalkan.")
                } else if(header.status_reseptur_id == 346) {
                    $(".not-approved").css("display", "block")
                    $("#pesan_warning").text("Resep ini belum di-approved.")
                }

                $(".tanggal").text(header.tanggal)
                $(".pegawai_penginput").text(header.pegawai_penginput)
                $("#dokter").text(header.dokter)
                $("#jenis_resep").text(header.jenis_resep)
                $("#penjamin").text(header.penjamin_nama)
                $("#carabayar").text(header.carabayar_nama)

                $("#nama_pasien").text(header.nama_pasien)
                $("#no_rm").text(header.no_rm)
                $("#no_pendaftaran").text(header.no_pendaftaran)
                $("#diagnosa_utama").text(header.diagnosa_utama)
                $("#tanggal_lahir").text(header.tanggal_lahir)
                $("#umur").text(header.umur)

                if(header.kamar != null && header.no_bed != null) {
                    $("#ruangan_kamar_bed").text(header.ruangan + " - " + header.kamar + " - " + header.no_bed)
                } else {
                    $("#ruangan_kamar_bed").text(header.ruangan)
                }
                $("#tinggi_badan").text(header.tinggi_badan+"cm")
                $("#berat_badan").text(header.berat_badan+"kg")
                $("#alergi").text(header.alergi)
                $("#tipe_resep").text(header.kategori_resep_nama)
                $("#waktu_tunggu").text(header.waktu_tunggu)

                // populate detail
                populateDetail(detail)

                // populate proses worklist
                if(response.history != null) {
                    history = response.history.log_status
                    populateProsesWorklist(history)
                } else {
                    status_keys = []
                }
                return true;
            },
        });
    });

    function populateDetail(detail) {
        var _no = 1
        var _html = ""
        var check_racikan;
        var check_kronis;
        $.each(detail, function (key, value) {
            if(value.is_racikan) {
                check_racikan = `<i class="fa fa-check"></i>`
            } else {
                check_racikan = "-"
            }
            
            if(value.is_kronis) {
                check_kronis = `<i class="fa fa-check"></i>`;
            } else {
                check_kronis = "-";
            }

            _html += `
                <tr>
                    <td>`+ _no +`</td>
                    <td>`+ check_racikan +`</td>
                    <td>`+ value.rke +`</td>
                    <td style="text-align: left;">`+ value.nama_obat +`</td>
                    <td>`+ value.signa +`</td>
                    <td>`+ value.qty_resep +`</td>
                    <td>`+ value.qty_bayar +`</td>
                    <td>`+ value.satuan_input +`</td>
                    <td>`+ check_kronis +`</td>
                    <td style="text-align: left;">`+ value.etiket +`</td>
                </tr>
            `;

            _no++;
        })

        if (_html === "") {
          _html += "<tr>";
          _html +=
            '<td colspan="9" id="data-null" class="text-center">Data Tidak Ditemukan</td>';
          _html += "</tr>";
        }
        $("#detail-resep").html("");
        $("#detail-resep").prepend(_html);
    }

    function populateProsesWorklist(history) {
        status_keys = Object.keys(history)
        $.each(status_keys, function(key, val) {
            $("#tgl_" + history[val].status_worklist_id).text(history[val].tanggal)
            $("#pegawai_" + history[val].status_worklist_id).text(history[val].nama_pegawai)
        })
    }

    $("#search_pegawai").select2({
        language: {
            errorLoading: function() { return "Mohon Tunggu .." }
        },
        placeholder: "Cari NIK / Nama Pegawai",
        minimumInputLength: 3,
        ajax: {
            url: '/apotek/worklist-farmasi/search-pegawai',
            dataType: 'json',
            quietMillis: 250,
            delay: 250,
        }
    })

    $("#search_pegawai").on("select2:select", function(e) {
        var last_id, last_status
        identifier = $("#search_no_resep").val()

        if(status_keys.length > 0) {
            last_id = status_keys[status_keys.length - 1]
            last_status = history[last_id].status_worklist_id
        } else {
            last_status = 674
        }

        if(last_status == 348) {
            last_status = 674
        }

        $('#confirm-dialog-overlay').remove();
        var header = "Konfirmasi"
        var message = "Apa anda yakin ingin memproses resep ini?"
        var label = {
            buttons: {
                Yes: 'button-ok',
                No: 'button-no',
            }
        };

        $.showQuestionDialog(header, message, label, function(reaction) {
            if (reaction == 'Yes') {
                docoHelper.listen = false;
                hideQuestionDialog();
                $.ajax({
                    url: "/apotek/worklist/update-status",
                    type: "post",
                    data: {
                        identifier: identifier,
                        status_worklist: last_status,
                        pegawai_id: $("#search_pegawai").val(),
                        multi_status: true,
                        instalasiasal_id: instalasiasal_id
                    },
                    success: function(response) {
                        reloadStatusWorklist()
                    },
                    error: function(response) {
                        console.log(response);
                        if(response.status == 422) {
                            var responseText = response.responseJSON.data.data;
                            if(responseText.text != undefined) {
                                responseText = responseText.text
                            } else {
                                responseText = responseText.message
                            }

                            if(responseText == 'Gagal potong stok' || responseText == 'Gagal potong stok obat') {
                                responseText = 'Stok obat tidak mencukupi.';
                            }

                            PNotify.removeAll()
                            new PNotify({
                                title: response.responseJSON.meta.title,
                                text: responseText,
                                addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                                type: "warning",
                                buttons: {
                                    closer: true,
                                    sticker: false,
                                },
                                hide: false
                            })
                        }
                        $("#search_pegawai").val(null).trigger("change")

                        return true;
                    }
                });
            } else {
                docoHelper.listen = false;
                $('[data-popup="tooltip"]').tooltip();
            }
        });

        $(".button-ok").focus();
    })

    function reloadStatusWorklist() {
        $.ajax({
            url: "/apotek/worklist-farmasi/get-history-worklist",
            type: "get",
            data: {
                identifier: identifier
            },
            success: function(response) {
                history = response.history.log_status

                clearWorklist()
                $("#search_pegawai").val(null).trigger("change")
                populateProsesWorklist(history)
                last_id = status_keys[status_keys.length - 1]
                last_status = history[last_id].status_worklist_id
                waktu_tunggu = history[last_id].waktu_tunggu
                if(last_status == 660) {
                    $("#search_pegawai").prop("disabled", true)
                    $("#waktu_tunggu").text(waktu_tunggu)
                }
                return true;
            }
        });
    }

    function clearInfoResep() {
        $(".tanggal").text("-")
        $(".pegawai_penginput").text("-")
        $("#jenis_resep").text("-")
        $("#penjamin").text("-")
        $("#carabayar").text("-")
        $("#nama_pasien").text("-")
        $("#no_rm").text("-")
        $("#diagnosa_utama").text("-")
        $("#tanggal_lahir").text("-")
        $("#umur").text("-")
        $("#ruangan_kamar_bed").text("-")
        $("#tinggi_badan").text("-")
        $("#berat_badan").text("-")
        $("#alergi").text("-")
        $("#tipe_resep").text("-")
    }

    function clearWorklist() {
        $("#tgl_676").text("-")
        $("#tgl_675").text("-")
        $("#tgl_677").text("-")
        $("#tgl_678").text("-")
        $("#tgl_348").text("-")
        $("#tgl_660").text("-")
        $("#pegawai_676").text("-")
        $("#pegawai_675").text("-")
        $("#pegawai_677").text("-")
        $("#pegawai_678").text("-")
        $("#pegawai_348").text("-")
        $("#pegawai_660").text("-")
    }

    setTimeout(function(){
        $("#transaksinoworklistform-no_transaksi").focus();
    }, 200);
    $('#transaksinoworklistform-no_transaksi').on('keypress', function(e){
        if (e.which == 13 || e.keyCode == 13) {
            $('#search_no_resep').val($(this).val()).trigger('change')
            $(this).blur();
        }
    });
})
