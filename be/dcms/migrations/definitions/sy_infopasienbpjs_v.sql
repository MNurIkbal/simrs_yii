CREATE OR REPLACE VIEW public.sy_infopasienbpjs_v
AS SELECT sy_kunjungan.kunjungan_id,
    sy_kunjungan.no_pendaftaran,
    sy_kunjungan.no_rekammedik,
    sy_kunjungan.pasien_id,
    sy_kunjungan.nama_pasien,
    sy_kunjungan.jenis_kelamin,
    sy_kunjungan.tgl_lahir,
    sy_kunjungan.umur,
    sy_kunjungan.tgl_pendaftaran,
    COALESCE(akomodasi_ranap.tgl_pulang, sy_kunjungan.tgl_pulang) AS tgl_pulang,
    sy_kunjungan.instalasi_kode,
    sy_kunjungan.instalasi_nama,
    sy_kunjungan.ruangan_kode,
    sy_kunjungan.ruangan_nama,
    sy_kunjungan.carabayar_kode,
    sy_kunjungan.carabayar_nama,
    sy_kunjungan.penjamin_kode,
    sy_kunjungan.penjamin_nama,
    sy_kunjungan.kelas_kode,
    sy_kunjungan.kelas_nama,
    sy_kunjungan.dokter_kode,
    sy_kunjungan.dokter_nama,
    sy_kunjungan.no_sep,
    sy_kunjungan.status_kunjungan,
    sy_kunjungan.no_kamar,
    sy_kunjungan.no_tempattidur,
    sy_kunjungan.hak_kelasbpjs,
    sy_kunjungan.carakeluar_kode,
    sy_kunjungan.lama_rawat,
    sy_kunjungan.no_asuransi,
    sy_kunjungan.is_verifikasi,
    sy_kunjungan.total_verifikasi,
    sy_kunjungan.tgl_verifikasi,
    sy_kunjungan.identitas_id,
    sy_kunjungan.identitas_nama,
    sy_kunjungan.identitas_value,
    sy_kunjungan.no_klaimcovid,
    sy_kunjungan.nosep,
    look_verifikasi.verifikasi,
    COALESCE(tagihan.layanan_tarif, 0::numeric) AS layanan_tarif,
    COALESCE(tagihan.jasa_rs, 0::numeric) AS jasa_rs,
    COALESCE(tagihan.jasa_dokter, 0::numeric) AS jasa_dokter,
    ((((sy_kunjungan.info_response_bpjs::json -> 'peserta'::text) ->> 'hakKelas'::text)::json) -> 'kode'::text)::character varying AS peserta_hakkelas,
    sy_kunjungan.sistole,
    sy_kunjungan.diastole,
    sy_kunjungan.caramasuk
   FROM sy_kunjungan
     JOIN ( SELECT a.lookup_id,
            a.lookup_name AS verifikasi
           FROM lookup_m a) look_verifikasi ON sy_kunjungan.status_kunjungan = look_verifikasi.lookup_id
     LEFT JOIN ( SELECT a.kunjungan_id,
            sum(a.layanan_tarif) AS layanan_tarif,
            sum(a.jasa_rs) AS jasa_rs,
            sum(a.jasa_dokter) AS jasa_dokter
           FROM sy_kunjungantagihan a
          WHERE a.is_deleted = false
          GROUP BY a.kunjungan_id) tagihan ON sy_kunjungan.kunjungan_id = tagihan.kunjungan_id
     LEFT JOIN ( SELECT DISTINCT pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_stopakomodasi AS tgl_pulang
           FROM pendaftaran_t
          WHERE pendaftaran_t.pasienadmisi_id IS NOT NULL) akomodasi_ranap ON sy_kunjungan.no_pendaftaran::text = akomodasi_ranap.no_pendaftaran::text
  WHERE sy_kunjungan.is_active = true AND sy_kunjungan.is_deleted = false