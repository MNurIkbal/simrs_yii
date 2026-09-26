CREATE VIEW "public"."laporanmutasibarang_v" AS  SELECT pesanbarang_t.tgl_pesanbarang,
    pesanbarang_t.no_pemesanan,
    pegawai_pemesan.nama_pegawai AS pegawai_pemesan,
    mutasibarangdetail_t.qty_dipesan,
    satuanunit_m.satuanunit_nama AS uom_pesan,
    mutasibarangdetail_t.mutasibarangdetail_id,
    mutasibarangdetail_t.mutasibarang_id,
    mutasibarang_t.nomutasi_barang,
    mutasibarang_t.tgl_mutasibarang,
    mutasibarang_t.ruanganasal_id,
    ruangan_asal.ruangan_nama AS ruangan_asal,
    instalasi_asal.instalasi_id AS instalasiasal_id,
    instalasi_asal.instalasi_nama AS instalasiasal_nama,
    mutasibarang_t.ruangantujuan_id,
    ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
    instalasi_tujuan.instalasi_id AS instalasitujuan_id,
    instalasi_tujuan.instalasi_nama AS instalasitujuan_nama,
    mutasibarang_t.pegawaipengirim_id,
    pegawai_pengirim.nama_pegawai AS pegawai_pengirim,
    mutasibarang_t.pegawaimengetahui_id,
    pegawai_mengetahui.nama_pegawai AS pegawai_mengetahui,
    mutasibarang_t.status_mutasi,
    mutasibarang_t.status,
    mutasibarangdetail_t.barang_id,
    barang_m.barang_kode,
    barang_m.barang_nama,
    mutasibarangdetail_t.qty_mutasi,
    satuanunit_m.satuanunit_nama AS uom_mutasi,
    mutasibarangdetail_t.jumlah_input,
    mutasibarangdetail_t.satuanbesar_id,
    terimamutasibarangdetail_t.jmlterima,
    satuan_terima.satuanunit_nama AS uom_terima,
    terimamutasibarang_t.tglterima,
    terimamutasibarang_t.noterimamutasi,
    pegawai_penerima.nama_pegawai AS pegawai_penerima,
    terimamutasibarang_t.keterangan_terima
   FROM mutasibarangdetail_t
     JOIN ( SELECT a.mutasibarang_id,
            a.nomutasi_barang,
            a.tgl_mutasibarang,
            a.ruanganasal_id,
            a.ruangantujuan_id,
            a.pegawaipengirim_id,
            a.pegawaimengetahui_id,
            a.status_mutasi,
            lookup_m.lookup_name AS status,
            a.pesanbarang_id
           FROM mutasibarang_t a
             LEFT JOIN ( SELECT b.lookup_id,
                    b.lookup_name
                   FROM lookup_m b) lookup_m ON lookup_m.lookup_id = a.status_mutasi
          WHERE a.is_deleted = false) mutasibarang_t ON mutasibarangdetail_t.mutasibarang_id = mutasibarang_t.mutasibarang_id
     JOIN ( SELECT a.pesanbarang_id,
            a.pegawaipemesan_id,
            a.tgl_pesanbarang,
            a.no_pemesanan
           FROM pesanbarang_t a) pesanbarang_t ON mutasibarang_t.pesanbarang_id = pesanbarang_t.pesanbarang_id
     JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuanunit_m ON mutasibarangdetail_t.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN ( SELECT a.terimamutasibarang_id,
            a.tglterima,
            a.noterimamutasi,
            a.keterangan_terima,
            a.pegawaipenerima_id,
            a.mutasibarang_id,
            a.created_by
           FROM terimamutasibarang_t a) terimamutasibarang_t ON terimamutasibarang_t.mutasibarang_id = mutasibarang_t.mutasibarang_id
     LEFT JOIN ( SELECT a.mutasibarangdetail_id,
            a.jmlterima,
            a.satuankecil_id,
            a.ruangan_id
           FROM terimamutasibarangdetail_t a) terimamutasibarangdetail_t ON terimamutasibarangdetail_t.mutasibarangdetail_id = mutasibarangdetail_t.mutasibarangdetail_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuan_terima ON terimamutasibarangdetail_t.satuankecil_id = satuan_terima.satuanunit_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_asal ON mutasibarang_t.ruanganasal_id = ruangan_asal.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_asal ON ruangan_asal.instalasi_id = instalasi_asal.instalasi_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_tujuan ON terimamutasibarangdetail_t.ruangan_id = ruangan_tujuan.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_pemesan ON pesanbarang_t.pegawaipemesan_id = pegawai_pemesan.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_pengirim ON mutasibarang_t.pegawaipengirim_id = pegawai_pengirim.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_mengetahui ON mutasibarang_t.pegawaimengetahui_id = pegawai_mengetahui.pegawai_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k a
             JOIN pegawai_m ON a.pegawai_id = pegawai_m.pegawai_id) pegawai_penerima ON terimamutasibarang_t.created_by = pegawai_penerima.loginpemakai_id
     JOIN ( SELECT a.barang_id,
            a.barang_kode,
            a.barang_nama
           FROM barang_m a) barang_m ON mutasibarangdetail_t.barang_id = barang_m.barang_id
  WHERE mutasibarangdetail_t.is_active = true AND mutasibarangdetail_t.is_deleted = false;