    var tableObatRs;
    var tableTindakanRs;
    var ambulan_id;
    var ambulanIdRs;
    $(document).ready(function($) {
        $("#pasien_id").select2({
            placeholder: "Pilih",
            minimumInputLength: 3,
            ajax : {
                url: "/ambulan/permintaan-ambulan/search-pasien",
                dataType: 'json',
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { return m; },
            },
        }).on('select2:select', function(e){
            var data = e.params.data;
            var nama_pasien = data.nama_pasien;
            var tempat_lahir = data.tempat_lahir;
            var tanggal_lahir = data.tanggal_lahir;
            var jenis_kelamin = data.jenis_kelamin;
            var ruangan_nama = data.ruangan_nama;
            var instalasi_nama = data.instalasi_nama;
            var diagnosa = data.diagnosa;
            var pendaftaran_id = data.pendaftaran_id;
            var kelas = data.kelas;
            var cara_bayar = data.cara_bayar;

            $(".pemesan").text(nama_pasien);
            $(".jenis_kelamin").text(jenis_kelamin);
            $(".tempat_lahir").text(tempat_lahir);
            $(".tgl_lahir").text(tanggal_lahir);
            $(".ruangan_asal").text(ruangan_nama);
            $(".instalasi_asal").text(instalasi_nama);
            $(".diagnosa_pasien").text(diagnosa);
            $(".pendaftaran_id").val(pendaftaran_id);
            $(".kelaspelayanan_nama").text(kelas);
            $(".carabayar_nama").text(cara_bayar);
        });

        $("#obatalkes_id_rs").select2({
            placeholder: "Pilih",
            minimumInputLength: 3,
            ajax : {
                url: "/ambulan/permintaan-ambulan/search-obat",
                dataType: 'json',
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { return m; },
            },
        }).on('select2:select', function(e){
            var data = e.params.data;
        });

        $("#daftartindakan_id_rs").select2({
            placeholder: "Pilih",
            minimumInputLength: 3,
            ajax : {
                url: "/ambulan/permintaan-ambulan/search-tindakan",
                dataType: 'json',
                quietMillis: 250,
                data: function (params) {
                    var _ambulan_id = $(".ambulan_id_rs").val();
                    params.ambulan_id = _ambulan_id;
                      var query = {
                        search: params
                      }
                  return params;
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { return m; },
            },
        }).on('select2:select', function(e){
            var data = e.params.data;
        });

        $(document).on("keyup", ".qty_tindakan_rs", function() {
            var _qtyObat = parseInt($(this).val());
            var _curr = parseInt($(this).attr('data-val'));
            if (_qtyObat > 0) {
                var dataPost = {
                    daftartindakan_id: $(this).attr("data-id"),
                    qty: $(this).val()
                };
                $.ajax({
                    method: 'POST',
                    data: dataPost,
                    url: '/ambulan/permintaan-ambulan/update-cache?tipe=2',
                    success: function(data) {
                        tableTindakanRs.draw();
                    }
                });
                return true;
            }
            docoNotification("error","Proses Gagal !", "Qty tidak boleh 0.");
            $(this).val(_curr);
        });

        tableObatRs = $("#tabel-temp-obat-rs").docoTabel({
            filter: false,
            displayLength: 50,
            lengthChange : false,
            processing: true,
            paginate : false,
            info : false,
            serverSide: true,
            sorting: [[1, "asc"]],
            ajax: baseUrl+"ambulan/permintaan-ambulan/get-list-obat-rs" ,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Nama Obat Alkes',
                    data: "obatalkes_nama",
                    orderable: false
                },
                {
                    title: "Qty",
                    data: "qty",
                    searchable: false,
                    orderable: false,
                    class:"text-right"
                },
                {
                    title: "Aksi",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
            fnRowCallback : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var stok = parseInt(aData.stok);
                if (stok == 0) {
                    $(nRow).css("background", "rgba(255, 188, 188, 0.58)");
                    $(nRow).find('input').prop("disabled",true);
                }
            }
        });

        tableTindakanRs = $("#tabel-temp-tindakan-rs").docoTabel({
            filter: false,
            displayLength: 50,
            lengthChange : false,
            processing: true,
            paginate : false,
            info : false,
            serverSide: true,
            sorting: [[2, "asc"]],
            ajax: baseUrl+"ambulan/permintaan-ambulan/get-list-tindakan-rs" ,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Tindakan',
                    data: "daftartindakan_nama",
                },
                {
                    title: "Kelompok Biaya",
                    data: "is_default",
                    orderable: false
                },
                {
                    title: "Qty",
                    data: "qty",
                    searchable: false,
                    orderable: false,
                    width: "10%"
                },
                {
                    title: "Tarif Satuan (Rp.)",
                    data: "harga_tariftindakan",
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "Jumlah Tarif (Rp.)",
                    data: "jumlah_tarif",
                    searchable: false,
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "Aksi",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
            drawCallback : function (settings) {
                var api = this.api();
                var dataRows = api.rows( {page:"current"} ).data();
                var estimasi_biaya = 0;
                $.each(dataRows, function (key, val) {
                    estimasi_biaya += parseInt(val.jumlah_tarif2);
                });
                $(".estimasi_biaya_rs").text(docoHelper.convertToRupiah(estimasi_biaya));
            },
            fnRowCallback : function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var _harga = parseInt(aData.harga);
                if (_harga == 0) {
                    $(nRow).css("background", "rgba(255, 188, 188, 0.58)");
                    $(nRow).find('input').prop("disabled",true);
                }
            }
        });

        $(".btn-cek-rs").on("click", function(){
            var pasien_id = $("#pasien_id").val();
            if(pasien_id == null) {
                docoNotification('error', "Error", "No Rekam Medik harus di Pilih Terlebih Dahulu.");
                return false;
            }
            else {
                return true;
            }
        });
    });
