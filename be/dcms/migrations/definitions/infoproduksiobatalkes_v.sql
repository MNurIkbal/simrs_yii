CREATE VIEW "public"."infoproduksiobatalkes_v" AS  SELECT produksiobatalkes_t.produksiobatalkes_id,
    pemesananproduksiobat_t.pemesananproduksiobat_id,
    pemesananproduksiobat_t.ruangan_id AS ruangan_pemesanan_id,
        CASE
            WHEN produksiobatalkes_t.is_approve = true THEN produksiobatalkes_t.noproduksiobat::text
            ELSE NULL::text
        END AS noproduksiobat,
    produksiobatalkes_t.tglproduksiobat,
    status_produksi.lookup_name AS status_produksi,
    pemesananproduksiobat_t.nopemesanan,
    pemesananproduksiobat_t.tglpemesanan,
    pemesananproduksiobat_t.pegawai_pemesanan,
    pemesananproduksiobat_t.catatan_bahanbaku,
    produksiobatalkes_t.is_approve,
    produksiobatalkes_t.tgl_approve,
    ( SELECT pegawai_m.nama_pegawai
           FROM pegawai_m
          WHERE pegawai_m.pegawai_id = produksiobatalkes_t.pegawai_approve) AS pegawai_approve,
    produksiobatalkes_t.status_produksi AS status_produksi_id
   FROM produksiobatalkes_t
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) status_produksi ON status_produksi.lookup_id = produksiobatalkes_t.status_produksi
     JOIN ( SELECT a.pemesananproduksiobat_id,
            a.nopemesanan,
            a.tglpemesanan,
            a.catatan_bahanbaku,
            a.ruangan_id,
            ( SELECT pegawai_m.nama_pegawai
                   FROM pegawai_m
                  WHERE pegawai_m.pegawai_id = a.pegawaipemesanan_id) AS pegawai_pemesanan
           FROM pemesananproduksiobat_t a
          WHERE a.is_deleted = false) pemesananproduksiobat_t ON pemesananproduksiobat_t.pemesananproduksiobat_id = produksiobatalkes_t.pemesananproduksiobat_id
  WHERE produksiobatalkes_t.is_deleted = false;