var { columnsLabel, filter } = phpVars

$(document).ready(function() {
    // Generate Table
    var _test = function(data){
        var totalsatuan = 0
        var cyto = 0
        var total = 0
        var qty = 0
        var kelompokarr = []
        var jenisarr = []
        var pemeriksaanarr = []
        $.each(data.data, function(k,v){
            var tarifcyto = (v.tarifcyto_tindakan != "0") ? docoHelper.convertToAngka(v.tarifcyto_tindakan) : 0
            totalsatuan += parseInt(v.tarif_tindakan)
            cyto += parseInt(tarifcyto)
            total += ( parseInt(tarifcyto) + parseInt(v.tarif_tindakan) ) * parseInt(v.qty_tindakan)
            qty += parseInt(v.qty_tindakan)
            if(jQuery.inArray(v.nama_kelompok, kelompokarr) < 0){
                kelompokarr.push(v.nama_kelompok)
            }
            if(jQuery.inArray(v.jenispemeriksaanlab_nama, jenisarr) < 0){
                jenisarr.push(v.jenispemeriksaanlab_nama)
            }
            if(jQuery.inArray(v.daftartindakan_nama, pemeriksaanarr) < 0){
                pemeriksaanarr.push(v.daftartindakan_nama)
            }
        })
        $("tfoot").find(".total-kelompok").empty().text(kelompokarr.length)
        $("tfoot").find(".total-jenis").empty().text(jenisarr.length)
        $("tfoot").find(".total-pemeriksaan").empty().text(pemeriksaanarr.length)
        $("tfoot").find(".total-harga").empty().text("Rp. "+docoHelper.convertToRupiah(totalsatuan))
        $("tfoot").find(".total-qty").empty().text(qty)
        $("tfoot").find(".total-cyto").empty().text("Rp. "+docoHelper.convertToRupiah(cyto))
        $("tfoot").find(".total-semua").empty().text("Rp. "+docoHelper.convertToRupiah(total))
    }
    table = $("#table-pasien-lab").docoTabel({
        filter: true,
        scrollY: false,
        scrollX: true,
        sorting: [[1, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: function(data, callback, settings){
            $.ajax({
                url: baseUrl+"laboratorium/lap-pemeriksaan/get-data",
                data: data,
                success: function(data)
                {
                    _test(data)
                    callback(data);
                }
            });
            
        },
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: columnsLabel.tanggalMasuk,
                data: "tglmasukpenunjang"
            },
            {
                title: columnsLabel.noPendaftaran,
                data: "no_pendaftaran"
            },
            {
                title: columnsLabel.noRekamMedis,
                data: "no_rekam_medik"
            },
            {
                title: columnsLabel.namaPasien,
                data: "nama_pasien"
            },
            {
                title: columnsLabel.namaDokter,
                data: "dokter", name: "pegawai_id"
            },
            {
                title: columnsLabel.namaDokterDPJP,
                data: "dokter_dpjp_nama",
                searchable: false,
            },
            {
                title: columnsLabel.kelasPelayanan,
                data: "kelaspelayanan_id",
                render: function ( data, type, row ) {
                    return row.kelaspelayanan_nama
                },
                name: "kelaspelayanan_id"
            },
            {
                title: columnsLabel.kelompokPemeriksaan,
                data: "nama_kelompok",
                name: "kelompokpemeriksaanlab_id"
            },
            {
                title: columnsLabel.jenisPemeriksaan,
                data: "jenispemeriksaanlab_nama",
                name: "jenispemeriksaanlab_id"
            },
            {
                title: columnsLabel.namaPemeriksaan,
                data: "daftartindakan_nama",
                name: "daftartindakan_id"
            },
            {
                title: columnsLabel.qty,
                data: "qty_tindakan",
                searchable: false
            },
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [1, filter.tglMasuk],
        [5, filter.selectDokter],
        [7, filter.selectKelasPelayanan],
        [8, filter.selectKelompokPemeriksaan],
        [9, filter.selectJenisPemeriksaan],
        [10, filter.selectNamaPemeriksaan],
    ], {
        1:0,
        2:1,
        3:2,
        4:3,
        5:4,
        7:5,
        8:6,
        9:7,
        10:8,
    }, true);
    dateRangeHelper(".startDate",".endDate",".targetDate");
    $(".selectDokter").select2InfinityScroll({
        url: "/laboratorium/lap-pemeriksaan/filters?type=dokter",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                }
            }
        }
    })
    $(".selectKelompok").select2InfinityScroll({
        url: "/laboratorium/lap-pemeriksaan/filters?type=kelompok",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                }
            }
        }
    })
    $(".selectJenis").select2InfinityScroll({
        url: "/laboratorium/lap-pemeriksaan/filters?type=jenis_pemeriksaan",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                    kelompokpemeriksaanlab_id: $(".selectKelompok").val(),
                }
            }
        }
    })
    $(".selectPemeriksaan").select2InfinityScroll({
        url: "/laboratorium/lap-pemeriksaan/filters?type=nama_pemeriksaan",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                    jenispemeriksaanlab_id: $(".selectJenis").val(),
                }
            }
        }
    })
});
