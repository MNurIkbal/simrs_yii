-- public.sy_infopasienbpjsklaim_v source

CREATE OR REPLACE VIEW public.sy_infopasienbpjsklaim_v
AS SELECT sy_kunjungantagihan.kunjungantagihan_id,
    sy_kunjungantagihan.kunjungan_id,
    sy_kunjungan.no_pendaftaran,
    sy_kunjungan.no_rekammedik,
    sy_kunjungan.instalasi_kode,
    sy_kunjungan.tgl_pendaftaran,
    COALESCE(akomodasi_ranap.tgl_pulang, sy_kunjungan.tgl_pulang) AS tgl_pulang,
    sy_kunjungantagihan.ruangan_kode,
    sy_kunjungantagihan.ruangan_nama,
    sy_kunjungantagihan.dokter_kode,
    sy_kunjungantagihan.dokter_nama,
    sy_kunjungantagihan.layanan_qty,
    sy_kunjungantagihan.layanan_kode,
    sy_kunjungantagihan.layanan_nama,
    sy_kunjungantagihan.layanan_tarif,
    sy_kunjungantagihan.jasa_rs,
    sy_kunjungantagihan.jasa_dokter,
    sy_kunjungantagihan.created_date,
    sy_kunjungantagihan.groupinacbg_nama
   FROM sy_kunjungantagihan
     JOIN ( SELECT a.kunjungan_id,
            a.no_pendaftaran,
            a.no_rekammedik,
            a.instalasi_kode,
            a.tgl_pendaftaran,
            a.tgl_pulang
           FROM sy_kunjungan a) sy_kunjungan ON sy_kunjungan.kunjungan_id = sy_kunjungantagihan.kunjungan_id
     LEFT JOIN ( SELECT DISTINCT pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_stopakomodasi AS tgl_pulang
           FROM pendaftaran_t
          WHERE pendaftaran_t.pasienadmisi_id IS NOT NULL) akomodasi_ranap ON sy_kunjungan.no_pendaftaran::text = akomodasi_ranap.no_pendaftaran::text
  WHERE sy_kunjungantagihan.is_active = true AND sy_kunjungantagihan.is_deleted = false;