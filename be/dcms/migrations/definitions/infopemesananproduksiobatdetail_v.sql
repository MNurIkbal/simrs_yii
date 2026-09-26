CREATE VIEW "public"."infopemesananproduksiobatdetail_v" AS  SELECT pemesananproduksiobatdetail_t.pemesananproduksiobatdetail_id,
    pemesananproduksiobatdetail_t.pemesananproduksiobat_id,
    pemesananproduksiobat_t.nopemesanan,
    pemesananproduksiobatdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.harganetto,
    pemesananproduksiobatdetail_t.qty,
    satuanunit_m.satuanunit_id,
    satuanunit_m.satuanunit_nama,
    satuankecil.satuanunit_id AS satuankecil_id,
    satuankecil.satuanunit_nama AS satuankecil_nama,
    pemesananproduksiobat_t.tgl_aprove,
    pemesananproduksiobatdetail_t.qty_konversi,
    ( SELECT pegawai_m.nama_pegawai
           FROM pegawai_m
          WHERE pegawai_m.pegawai_id = pemesananproduksiobat_t.pegawaipemesanan_id) AS pegawai_pemesanan,
    pemesananproduksiobatdetail_t.additional_data
   FROM pemesananproduksiobatdetail_t
     JOIN ( SELECT pemesananproduksiobat_t_1.pemesananproduksiobat_id,
            pemesananproduksiobat_t_1.nopemesanan,
            pemesananproduksiobat_t_1.tgl_aprove,
            pemesananproduksiobat_t_1.pegawaipemesanan_id
           FROM pemesananproduksiobat_t pemesananproduksiobat_t_1
          WHERE pemesananproduksiobat_t_1.is_deleted = false) pemesananproduksiobat_t ON pemesananproduksiobat_t.pemesananproduksiobat_id = pemesananproduksiobatdetail_t.pemesananproduksiobat_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_kode,
            a.obatalkes_nama,
            a.harganetto,
            a.satuankecil_id
           FROM obatalkes_m a) obatalkes_m ON obatalkes_m.obatalkes_id = pemesananproduksiobatdetail_t.obatalkes_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuanunit_m ON satuanunit_m.satuanunit_id = pemesananproduksiobatdetail_t.satuan_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuankecil ON satuankecil.satuanunit_id = obatalkes_m.satuankecil_id
  WHERE pemesananproduksiobatdetail_t.is_deleted = false;