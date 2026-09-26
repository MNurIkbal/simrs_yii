CREATE VIEW "public"."infoproduksiobatalkesdetail_v" AS  SELECT produksiobatalkesdetail_t.produksiobatalkesdetail_id,
    produksiobatalkesdetail_t.produksiobatalkes_id,
    produksiobatalkesdetail_t.pemesananproduksiobatdetail_id,
    produksiobatalkes_t.noproduksiobat,
    obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.obatalkes_nama,
    satuanunit_m.satuanunit_nama AS satuan,
    produksiobatalkesdetail_t.satuankecil_id,
    produksiobatalkesdetail_t.qty_produksi,
    obatalkes_m.harganetto AS harganetto_awal,
    produksiobatalkesbahanbaku_t.harganetto_baru,
    produksiobatalkes_t.nopemesanan,
    produksiobatalkes_t.pegawai_pemesanan,
    produksiobatalkes_t.tglpemesanan,
    produksiobatalkes_t.status_produksi
   FROM produksiobatalkesdetail_t
     JOIN ( SELECT produksiobatalkes_t_1.produksiobatalkes_id,
            pemesananproduksiobat_t.pemesananproduksiobat_id,
                CASE
                    WHEN produksiobatalkes_t_1.is_approve = true THEN produksiobatalkes_t_1.noproduksiobat::text
                    ELSE NULL::text
                END AS noproduksiobat,
            produksiobatalkes_t_1.tglproduksiobat,
            status_produksi.lookup_name AS status_produksi,
            pemesananproduksiobat_t.nopemesanan,
            pemesananproduksiobat_t.tglpemesanan,
            pemesananproduksiobat_t.pegawai_pemesanan,
            produksiobatalkes_t_1.is_approve,
            produksiobatalkes_t_1.tgl_approve,
            ( SELECT pegawai_m.nama_pegawai
                   FROM pegawai_m
                  WHERE pegawai_m.pegawai_id = produksiobatalkes_t_1.pegawai_approve) AS pegawai_approve
           FROM produksiobatalkes_t produksiobatalkes_t_1
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) status_produksi ON status_produksi.lookup_id = produksiobatalkes_t_1.status_produksi
             JOIN ( SELECT a.pemesananproduksiobat_id,
                    a.nopemesanan,
                    a.tglpemesanan,
                    ( SELECT pegawai_m.nama_pegawai
                           FROM pegawai_m
                          WHERE pegawai_m.pegawai_id = a.pegawaipemesanan_id) AS pegawai_pemesanan
                   FROM pemesananproduksiobat_t a
                  WHERE a.is_deleted = false) pemesananproduksiobat_t ON pemesananproduksiobat_t.pemesananproduksiobat_id = produksiobatalkes_t_1.pemesananproduksiobat_id
          WHERE produksiobatalkes_t_1.is_deleted = false) produksiobatalkes_t ON produksiobatalkes_t.produksiobatalkes_id = produksiobatalkesdetail_t.produksiobatalkes_id
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_kode,
            a.obatalkes_nama,
            a.harganetto
           FROM obatalkes_m a) obatalkes_m ON obatalkes_m.obatalkes_id = produksiobatalkesdetail_t.obatalkes_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuanunit_m ON satuanunit_m.satuanunit_id = produksiobatalkesdetail_t.satuankecil_id
     LEFT JOIN ( SELECT a.produksiobatalkesdetail_id,
            sum(a.harganetto) AS harganetto_baru
           FROM produksiobatalkesbahanbaku_t a
          WHERE a.is_deleted = false
          GROUP BY a.produksiobatalkesdetail_id) produksiobatalkesbahanbaku_t ON produksiobatalkesbahanbaku_t.produksiobatalkesdetail_id = produksiobatalkesdetail_t.produksiobatalkesdetail_id
  WHERE produksiobatalkesdetail_t.is_deleted = false;

