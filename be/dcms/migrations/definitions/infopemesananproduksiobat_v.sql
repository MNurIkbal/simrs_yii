CREATE VIEW "public"."infopemesananproduksiobat_v" AS  SELECT pemesananproduksiobat_t.pemesananproduksiobat_id,
    ruangandaninstalasi.instalasi_id,
    ruangandaninstalasi.instalasi_nama,
    ruangandaninstalasi.ruangan_id,
    ruangandaninstalasi.ruangan_nama,
    pemesananproduksiobat_t.nopemesanan,
    pemesananproduksiobat_t.tglpemesanan,
    ( SELECT pegawai_m.nama_pegawai
           FROM pegawai_m
          WHERE pegawai_m.pegawai_id = pemesananproduksiobat_t.pegawaipemesanan_id) AS pegawai_pemesanan,
    ( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = COALESCE(produksiobatalkes_t.status_produksi, pemesananproduksiobat_t.status_pemesanan)) AS status_pemesanan,
    pemesananproduksiobat_t.tgl_aprove,
    ( SELECT pegawai_m.nama_pegawai
           FROM pegawai_m
          WHERE pegawai_m.pegawai_id = pemesananproduksiobat_t.pegawaiaprove_id) AS pegawai_approve,
    pemesananproduksiobat_t.catatan_bahanbaku,
    string_agg(pemesananproduksiobatdetail_t.obatalkes_nama::text, ','::text) AS obatalkes_nama,
    pemesananproduksiobat_t.pegawaipemesanan_id,
    produksiobatalkes_t.noproduksiobat
   FROM pemesananproduksiobat_t
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.pemesananproduksiobat_id,
            obatalkes_m.obatalkes_nama
           FROM pemesananproduksiobatdetail_t a
             LEFT JOIN ( SELECT a_1.obatalkes_id,
                    a_1.obatalkes_kode,
                    a_1.obatalkes_nama
                   FROM obatalkes_m a_1) obatalkes_m ON obatalkes_m.obatalkes_id = a.obatalkes_id
          WHERE a.is_deleted = false) pemesananproduksiobatdetail_t ON pemesananproduksiobatdetail_t.pemesananproduksiobat_id = pemesananproduksiobat_t.pemesananproduksiobat_id
     LEFT JOIN ( SELECT ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama
           FROM ruangan_m
             JOIN ( SELECT instalasi_m_1.instalasi_id,
                    instalasi_m_1.instalasi_nama
                   FROM instalasi_m instalasi_m_1) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id) ruangandaninstalasi ON ruangandaninstalasi.instalasi_id = pemesananproduksiobat_t.instalasi_id AND ruangandaninstalasi.ruangan_id = pemesananproduksiobat_t.ruangan_id
     LEFT JOIN ( SELECT pt.pemesananproduksiobat_id,
            pt.noproduksiobat,
            pt.status_produksi
           FROM produksiobatalkes_t pt) produksiobatalkes_t ON pemesananproduksiobat_t.pemesananproduksiobat_id = produksiobatalkes_t.pemesananproduksiobat_id
  WHERE pemesananproduksiobat_t.is_deleted = false
  GROUP BY pemesananproduksiobat_t.pemesananproduksiobat_id, ruangandaninstalasi.instalasi_id, ruangandaninstalasi.instalasi_nama, ruangandaninstalasi.ruangan_id, ruangandaninstalasi.ruangan_nama, pemesananproduksiobat_t.nopemesanan, pemesananproduksiobat_t.tglpemesanan, produksiobatalkes_t.noproduksiobat, (( SELECT pegawai_m.nama_pegawai
           FROM pegawai_m
          WHERE pegawai_m.pegawai_id = pemesananproduksiobat_t.pegawaipemesanan_id)), (( SELECT lookup_m.lookup_name
           FROM lookup_m
          WHERE lookup_m.lookup_id = COALESCE(produksiobatalkes_t.status_produksi, pemesananproduksiobat_t.status_pemesanan))), (( SELECT pegawai_m.nama_pegawai
           FROM pegawai_m
          WHERE pegawai_m.pegawai_id = pemesananproduksiobat_t.pegawaiaprove_id)), pemesananproduksiobat_t.catatan_bahanbaku;