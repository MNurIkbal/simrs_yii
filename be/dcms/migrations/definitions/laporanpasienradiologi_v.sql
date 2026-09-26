-- public.laporanpasienradiologi_v source

CREATE OR REPLACE VIEW public.laporanpasienradiologi_v
AS SELECT pasienmasukpenunjang_t.tglmasukpenunjang,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tglperiksa,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    dokter_perujuk.nama_pegawai,
    string_agg(DISTINCT dokter_penunjang.nama_pegawai::text, '; '::text) AS dokter_penunjang,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ruangan_m.ruangan_nama,
    ruangan_asal.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama AS ruanganasal_nama,
    rujukan_t.asalrujukan_id,
        CASE
            WHEN pendaftaran_t.is_aps IS TRUE AND asalrujukan_m.asalrujukan_nama IS NULL THEN 'APS'::character varying
            WHEN asalrujukan_m.asalrujukan_nama IS NULL THEN 'ORDER'::character varying
            ELSE asalrujukan_m.asalrujukan_nama
        END AS asalrujukan_nama,
        CASE
            WHEN (EXISTS ( SELECT 1
               FROM permintaankepenunjang_t permintaankepenunjang_t_1
              WHERE permintaankepenunjang_t_1.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)) THEN ( SELECT
                    CASE
                        WHEN count(*) FILTER (WHERE permintaankepenunjang_t_1.is_cyto IS TRUE) >= 1 THEN true
                        ELSE false
                    END AS "case"
               FROM permintaankepenunjang_t permintaankepenunjang_t_1
              WHERE permintaankepenunjang_t_1.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id AND permintaankepenunjang_t_1.is_deleted = false)
            ELSE false
        END AS is_cyto,
    NULL::text AS status_cito,
    COALESCE(pasienmasukpenunjang_t.status_periksa, 477::character varying) AS status_periksa,
        CASE
            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN false
            WHEN tindakanpelayanan_t.is_deleted = true THEN true
            ELSE false
        END AS status_batal,
    tindakanpelayanan_t.tindakanpelayananasal_id
   FROM pasienmasukpenunjang_t
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id
     LEFT JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m dokter_perujuk ON COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id
     LEFT JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id AND permintaankepenunjang_t.is_deleted = false
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.instalasi_id = 5
     LEFT JOIN pegawai_m dokter_penunjang ON COALESCE(permintaankepenunjang_t.dokter_id::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) = dokter_penunjang.pegawai_id
     LEFT JOIN penjamin_m ON COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5
     LEFT JOIN ruangan_m ruangan_asal ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_asal.ruangan_id
  GROUP BY pasienmasukpenunjang_t.tglmasukpenunjang, pasienmasukpenunjang_t.no_masukpenunjang, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, dokter_perujuk.nama_pegawai, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, ruangan_m.ruangan_nama, ruangan_asal.ruangan_id, ruangan_asal.ruangan_nama, rujukan_t.asalrujukan_id, pendaftaran_t.is_aps, asalrujukan_m.asalrujukan_nama, pasienmasukpenunjang_t.status_periksa, tindakanpelayanan_t.tindakansudahbayar_id, tindakanpelayanan_t.is_deleted, tindakanpelayanan_t.tindakanpelayananasal_id, pasienkirimkeunitlain_t.pasienkirimkeunitlain_id;