-- public.laporanjasapelayananrajal_v source

CREATE OR REPLACE VIEW public.laporanjasapelayananrajal_v
AS WITH pelayanan_rajal AS (
         SELECT sy_kunjungan.nosep,
            sy_kunjungan.no_pendaftaran,
            sy_kunjungan.tgl_pendaftaran,
            sy_klaimgroup_t.total AS total_tarif_jkn,
            sy_klaimgroup_t.total * 44::double precision / 100::double precision AS tarif_sarana,
            sy_klaimgroup_t.total * 56::double precision / 100::double precision AS tarif_pelayanan,
            sy_klaiminacbg.total_tarifrs AS total_tarif_rs,
            sy_kunjungan.no_rekammedik,
            sy_kunjungan.nama_pasien,
            sy_kunjungan.dokter_nama AS dpjp,
            konsul.dokter_konsul,
            konsul.num_konsul,
            radiologi.dokter_radiologi,
            radiologi.num_radiologi,
            operasi.tindakan_operasi,
            sy_kunjungan.ruangan_nama
           FROM sy_klaiminacbg
             JOIN sy_kunjungan ON sy_klaiminacbg.kunjungan_id = sy_kunjungan.kunjungan_id AND (sy_kunjungan.instalasi_nama::text = ANY (ARRAY['Rawat Jalan'::character varying::text, 'IGD'::character varying::text])) AND sy_kunjungan.status_kunjungan = 551
             JOIN sy_klaimgroup_t ON sy_klaiminacbg.klaimgroup_id = sy_klaimgroup_t.sy_klaimgroup_id
             JOIN pendaftaran_t ON sy_kunjungan.no_pendaftaran::text = pendaftaran_t.no_pendaftaran::text AND pendaftaran_t.status_periksa::integer <> 433
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    pegawai_m.nama_pegawai AS dokter_konsul,
                    row_number() OVER (PARTITION BY a.pendaftaran_id ORDER BY a.konsulpoli_id) AS num_konsul
                   FROM konsulpoli_t a
                     JOIN pegawai_m ON a.pegawai_id = pegawai_m.pegawai_id
                  WHERE (a.status_periksa::integer <> ALL (ARRAY[402, 411])) AND a.status_approve = 565) konsul ON pendaftaran_t.pendaftaran_id = konsul.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    pegawai_m.nama_pegawai AS dokter_radiologi,
                    row_number() OVER (PARTITION BY a.pendaftaran_id ORDER BY a.pasienmasukpenunjang_id) AS num_radiologi
                   FROM pasienmasukpenunjang_t a
                     JOIN ruangan_m ON a.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 5 AND ruangan_m.is_deleted = false AND ruangan_m.is_active = true
                     JOIN tindakanpelayanan_t ON a.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted = false
                     JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
                  WHERE a.status_periksa::integer <> 476) radiologi ON pendaftaran_t.pendaftaran_id = radiologi.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    string_agg(daftartindakan_m.daftartindakan_nama::text, ', '::text) AS tindakan_operasi
                   FROM pasienmasukpenunjang_t a
                     JOIN ruangan_m ON a.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 12 AND ruangan_m.is_deleted = false AND ruangan_m.is_active = true
                     JOIN tindakanpelayanan_t ON a.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.is_deleted = false
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                  WHERE a.status_periksa::integer <> 476
                  GROUP BY a.pendaftaran_id) operasi ON pendaftaran_t.pendaftaran_id = operasi.pendaftaran_id
          WHERE sy_klaiminacbg.is_deleted = false AND sy_klaiminacbg.is_active = true
        )
 SELECT pelayanan_rajal.nosep AS "No SEP",
    pelayanan_rajal.no_pendaftaran AS "No Pendaftaran",
    pelayanan_rajal.tgl_pendaftaran AS "Tgl Pendaftaran",
    pelayanan_rajal.total_tarif_jkn AS "Total Tarif JKN",
    pelayanan_rajal.tarif_sarana AS "Sarana",
    pelayanan_rajal.tarif_pelayanan AS "Pelayanan",
    pelayanan_rajal.total_tarif_rs AS "Total Tarif RS",
    pelayanan_rajal.no_rekammedik AS "No RM",
    pelayanan_rajal.nama_pasien AS "Nama Pasien",
    pelayanan_rajal.dpjp AS "DPJP",
    max(
        CASE
            WHEN pelayanan_rajal.num_konsul = 1 THEN pelayanan_rajal.dokter_konsul
            ELSE NULL::character varying
        END::text) AS "Konsul 1",
    max(
        CASE
            WHEN pelayanan_rajal.num_konsul = 2 THEN pelayanan_rajal.dokter_konsul
            ELSE NULL::character varying
        END::text) AS "Konsul 2",
    max(
        CASE
            WHEN pelayanan_rajal.num_konsul = 3 THEN pelayanan_rajal.dokter_konsul
            ELSE NULL::character varying
        END::text) AS "Konsul 3",
    max(
        CASE
            WHEN pelayanan_rajal.num_konsul = 4 THEN pelayanan_rajal.dokter_konsul
            ELSE NULL::character varying
        END::text) AS "Konsul 4",
    max(
        CASE
            WHEN pelayanan_rajal.num_konsul = 5 THEN pelayanan_rajal.dokter_konsul
            ELSE NULL::character varying
        END::text) AS "Konsul 5",
    max(
        CASE
            WHEN pelayanan_rajal.num_radiologi = 1 THEN pelayanan_rajal.dokter_radiologi
            ELSE NULL::character varying
        END::text) AS "Dokter Radiologi 1",
    max(
        CASE
            WHEN pelayanan_rajal.num_radiologi = 2 THEN pelayanan_rajal.dokter_radiologi
            ELSE NULL::character varying
        END::text) AS "Dokter Radiologi 2",
    pelayanan_rajal.tindakan_operasi AS "Operasi",
    pelayanan_rajal.ruangan_nama AS "Unit"
   FROM pelayanan_rajal
  GROUP BY pelayanan_rajal.nosep, pelayanan_rajal.no_pendaftaran, pelayanan_rajal.tgl_pendaftaran, pelayanan_rajal.total_tarif_jkn, pelayanan_rajal.tarif_sarana, pelayanan_rajal.tarif_pelayanan, pelayanan_rajal.total_tarif_rs, pelayanan_rajal.no_rekammedik, pelayanan_rajal.nama_pasien, pelayanan_rajal.dpjp, pelayanan_rajal.tindakan_operasi, pelayanan_rajal.ruangan_nama;