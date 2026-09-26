-- public.sy_infopasienbpjsklaimlist_v source

CREATE OR REPLACE VIEW public.sy_infopasienbpjsklaimlist_v
AS SELECT sy_kunjungan.kunjungan_id,
    pendaftaran_t.pendaftaran_id,
    sy_kunjungan.no_pendaftaran,
    sy_kunjungan.no_rekammedik,
    sy_kunjungan.nama_pasien,
    sy_kunjungan.jenis_kelamin,
    sy_kunjungan.tgl_lahir,
    sy_kunjungan.umur,
    sy_kunjungan.tgl_pendaftaran,
    COALESCE(pendaftaran_t.tgl_pulang, sy_kunjungan.tgl_pulang) AS tgl_pulang,
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
    sy_kunjungan.pasien_id,
    sy_kunjungan.additional_data,
    sy_kunjungan.created_date,
    sy_kunjungan.created_by,
    sy_kunjungan.modified_count,
    sy_kunjungan.last_modified_date,
    sy_kunjungan.last_modified_by,
    sy_kunjungan.is_deleted,
    sy_kunjungan.is_active,
    sy_kunjungan.deleted_date,
    sy_kunjungan.deleted_by,
    sy_kunjungan.nosep,
    sy_kunjungan.jeniskasuspenyakit_id,
    sy_kunjungan.jeniskasuspenyakit_nama,
    sy_kunjungan.instalasi_id,
    sy_kunjungan.ruangan_id,
    sy_kunjungan.status_unduh_dokumen,
        CASE
            WHEN (EXISTS ( SELECT sy_kunjungantagihan.kunjungan_id
               FROM sy_kunjungantagihan
              WHERE sy_kunjungantagihan.kunjungan_id = sy_kunjungan.kunjungan_id
             LIMIT 1)) THEN ( SELECT sum(sy_kunjungantagihan.layanan_tarif) AS sum
               FROM sy_kunjungantagihan
              WHERE sy_kunjungantagihan.kunjungan_id = sy_kunjungan.kunjungan_id)
            ELSE 0::numeric
        END AS tarif_rs,
        CASE
            WHEN (EXISTS ( SELECT sy_klaiminacbg.kunjungan_id,
                tagihan.tarif_inacbg
               FROM sy_klaiminacbg
                 JOIN ( SELECT sy_klaimgroup_t.sy_klaimgroup_id,
                        sum(sy_klaimgroup_t.total) AS tarif_inacbg
                       FROM sy_klaimgroup_t
                      GROUP BY sy_klaimgroup_t.sy_klaimgroup_id) tagihan ON sy_klaiminacbg.klaimgroup_id = tagihan.sy_klaimgroup_id
              WHERE sy_klaiminacbg.is_deleted IS FALSE AND sy_klaiminacbg.is_active IS TRUE AND sy_klaiminacbg.kunjungan_id = sy_kunjungan.kunjungan_id
             LIMIT 1)) THEN ( SELECT sum(tagihan.tarif_inacbg) AS sum
               FROM sy_klaiminacbg
                 JOIN ( SELECT sy_klaimgroup_t.sy_klaimgroup_id,
                        sum(sy_klaimgroup_t.total) AS tarif_inacbg
                       FROM sy_klaimgroup_t
                      GROUP BY sy_klaimgroup_t.sy_klaimgroup_id) tagihan ON sy_klaiminacbg.klaimgroup_id = tagihan.sy_klaimgroup_id
              WHERE sy_klaiminacbg.is_deleted IS FALSE AND sy_klaiminacbg.is_active IS TRUE AND sy_klaiminacbg.kunjungan_id = sy_kunjungan.kunjungan_id)
            ELSE 0::double precision
        END AS plafon,
    sy_kunjungan.no_pembayaran,
        CASE
            WHEN sy_kunjungan.instalasi_kode::text = 'RI'::text THEN pasienadmisi_t.bpjs_id
            ELSE pendaftaran_t.bpjs_id
        END AS bpjs_id,
    tanggal_klaim.tanggal_klaim
   FROM sy_kunjungan
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.tgl_stopakomodasi AS tgl_pulang,
            a.pasienadmisi_id,
            a.bpjs_id,
            a.tgl_pendaftaran
           FROM pendaftaran_t a
          ORDER BY a.pasienadmisi_id) pendaftaran_t ON sy_kunjungan.no_pendaftaran::text = pendaftaran_t.no_pendaftaran::text
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.bpjs_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     LEFT JOIN ( SELECT sy_klaiminacbg.kunjungan_id,
            last_klaim.tanggal_klaim
           FROM sy_klaiminacbg
             JOIN ( SELECT sy_klaimgroup_t.sy_klaimgroup_id,
                    sy_klaimgroup_t.created_date AS tanggal_klaim
                   FROM sy_klaimgroup_t
                  WHERE sy_klaimgroup_t.is_deleted IS FALSE AND sy_klaimgroup_t.is_active IS TRUE) last_klaim ON sy_klaiminacbg.klaimgroup_id = last_klaim.sy_klaimgroup_id
          WHERE sy_klaiminacbg.is_deleted IS FALSE AND sy_klaiminacbg.is_active IS TRUE) tanggal_klaim ON tanggal_klaim.kunjungan_id = sy_kunjungan.kunjungan_id
  WHERE sy_kunjungan.is_deleted IS FALSE AND sy_kunjungan.is_active IS TRUE;