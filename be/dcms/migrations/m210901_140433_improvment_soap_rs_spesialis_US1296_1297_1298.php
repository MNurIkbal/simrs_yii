<?php

use yii\db\Migration;

/**
 * Class m210901_140433_improvment_soap_rs_spesialis_US1296_1297_1298
 */
class m210901_140433_improvment_soap_rs_spesialis_US1296_1297_1298 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE permintaankepenunjang_t ADD IF NOT EXISTS alasan_batal TEXT;
        ');

        $this->execute('
            ALTER TABLE obatalkespasien_t ADD IF NOT EXISTS alasan_batal TEXT;
        ');

        $this->execute('
            ALTER TABLE tindakanpelayanan_t ADD IF NOT EXISTS alasan_batal TEXT;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."soaprs_v";
        ');

        $this->execute('
            CREATE VIEW "public"."soaprs_v" AS  SELECT \'RJ\'::text AS tipe,
                soaprj_t.pendaftaran_id,
                pendaftaran_t.pasien_id, 
                soaprj_t.ruangan_id,
                soaprj_t.pegawai_id,
                soaprj_t.tgl_soaprj,
                soaprj_t.a_diag_utama,
                soaprj_t.a_diag_penyerta,
                soaprj_t.subject,
                soaprj_t.object,
                soaprj_t.planning,
                soaprj_t.catatan_dokter,
                pegawai_m.nama_pegawai,
                kelompokpegawai_m.kelompokpegawai_id,
                kelompokpegawai_m.kelompokpegawai_nama,
                ruangan_m.ruangan_nama,
                pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
                soaprj_t.soaprj_id,
                NULL::integer AS cppt_id,
                NULL::integer AS referred_id,
                NULL::character varying AS kamarruangan_nokamar,
                NULL::character varying AS no_tempattidur,
                soaprj_t.instruksi,
                false AS is_verifikasi,
                NULL::text AS catatan_perawat,
                soaprj_t.is_deleted,
                NULL::character varying AS pegawai_verifikasi,
                NULL::date AS tgl_verifikasi,
                NULL::character varying AS pegawai_cppt,
                NULL::character varying AS pegawai_instruksi,
                NULL::integer AS pemberi_instruksi_id,
                false AS is_verifikasi_verbal,
                NULL::date AS tgl_verif_verbal,
                NULL::integer AS pegawai_verbal_id,
                NULL::character varying AS pegawai_verifikasi_verbal,
                NULL::integer AS dokteradmisi_id,
                NULL::character varying AS dokteradmisi_nama,
                false AS is_instruksi_pulang,
                pendaftaran_t.pasienadmisi_id,
                false AS is_lab,
                false AS is_rad,
                false AS is_reseptur,
                false AS is_konsul,
                pendaftaran_t.no_pendaftaran,
                pasien.no_rekam_medik,
                spesialis_m.spesialis_id,
                spesialis_m.spesialis_nama
               FROM (((((((soaprj_t
                 JOIN pendaftaran_t ON ((soaprj_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN ( SELECT pasien_m.pasien_id,
                        pasien_m.no_rekam_medik
                       FROM pasien_m) pasien ON ((pendaftaran_t.pasien_id = pasien.pasien_id)))
                 LEFT JOIN pegawai_m ON ((soaprj_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                 LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                 LEFT JOIN ruangan_m ON ((soaprj_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
              WHERE (soaprj_t.is_deleted IS FALSE)
            UNION ALL
             SELECT \'RD\'::text AS tipe,
                cppt_t.pendaftaran_id,
                pendaftaran_t.pasien_id,
                cppt_t.ruangan_id,
                cppt_t.pegawai_id,
                cppt_t.tgl_cppt AS tgl_soaprj,
                cppt_t.a_diag_utama,
                cppt_t.a_diag_penyerta,
                cppt_t.subject,
                cppt_t.object,
                cppt_t.planning,
                cppt_t.catatan_dokter,
                pegawai_m.nama_pegawai,
                kelompokpegawai_m.kelompokpegawai_id,
                kelompokpegawai_m.kelompokpegawai_nama,
                ruangan_m.ruangan_nama,
                pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
                NULL::integer AS soaprj_id,
                cppt_t.cppt_id,
                cppt_t.referred_id,
                NULL::character varying AS kamarruangan_nokamar,
                NULL::character varying AS no_tempattidur,
                cppt_t.instruksi,
                cppt_t.is_verifikasi,
                cppt_t.catatan_perawat,
                cppt_t.is_deleted,
                pegawai_verif.nama_pegawai AS pegawai_verifikasi,
                cppt_t.tgl_verifikasi,
                pegawai_m.nama_pegawai AS pegawai_cppt,
                pemberi_instruksi.nama_pegawai AS pegawai_instruksi,
                cppt_t.pemberi_instruksi_id,
                cppt_t.is_verifikasi_verbal,
                cppt_t.tgl_verif_verbal,
                cppt_t.pegawai_verbal_id,
                pegawai_verif2.nama_pegawai AS pegawai_verifikasi_verbal,
                NULL::integer AS dokteradmisi_id,
                NULL::character varying AS dokteradmisi_nama,
                cppt_t.is_instruksi_pulang,
                pendaftaran_t.pasienadmisi_id,
                    CASE COALESCE(lab.ct_lab, (0)::bigint)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_lab,
                    CASE COALESCE(rad.ct_rad, (0)::bigint)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_rad,
                    CASE COALESCE(reseptur.ct_reseptur, (0)::bigint)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_reseptur,
                false AS is_konsul,
                pendaftaran_t.no_pendaftaran,
                pasien.no_rekam_medik,
                spesialis_m.spesialis_id,
                spesialis_m.spesialis_nama
               FROM (((((((((((((cppt_t
                 JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN ( SELECT pasien_m.pasien_id,
                        pasien_m.no_rekam_medik
                       FROM pasien_m) pasien ON ((pendaftaran_t.pasien_id = pasien.pasien_id)))
                 LEFT JOIN pegawai_m ON ((cppt_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                 LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                 LEFT JOIN ruangan_m ON ((cppt_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN pegawai_m pegawai_verif ON ((cppt_t.pegawai_verifikasi_id = pegawai_verif.pegawai_id)))
                 LEFT JOIN pegawai_m pegawai_verif2 ON ((cppt_t.pegawai_verbal_id = pegawai_verif2.pegawai_id)))
                 LEFT JOIN pegawai_m pemberi_instruksi ON ((cppt_t.pemberi_instruksi_id = pemberi_instruksi.pegawai_id)))
                 LEFT JOIN ( SELECT count(instruksi_t.cppt_id) AS ct_lab,
                        instruksi_t.cppt_id
                       FROM ((instruksi_t
                         JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                         JOIN ruangan_m ruangan_m_1 ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m_1.ruangan_id)))
                      WHERE (ruangan_m_1.instalasi_id = 4)
                      GROUP BY instruksi_t.cppt_id) lab ON ((COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = lab.cppt_id)))
                 LEFT JOIN ( SELECT count(instruksi_t.cppt_id) AS ct_rad,
                        instruksi_t.cppt_id
                       FROM ((instruksi_t
                         JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                         JOIN ruangan_m ruangan_m_1 ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m_1.ruangan_id)))
                      WHERE (ruangan_m_1.instalasi_id = 5)
                      GROUP BY instruksi_t.cppt_id) rad ON ((COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = rad.cppt_id)))
                 LEFT JOIN ( SELECT count(instruksi_t.cppt_id) AS ct_reseptur,
                        instruksi_t.cppt_id
                       FROM (instruksi_t
                         JOIN reseptur_t ON ((instruksi_t.instruksi_id = reseptur_t.instruksi_id)))
                      GROUP BY instruksi_t.cppt_id) reseptur ON ((COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = reseptur.cppt_id)))
                 LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
              WHERE ((cppt_t.pasienadmisi_id IS NULL) AND (cppt_t.is_deleted IS FALSE))
            UNION ALL
             SELECT \'RI\'::text AS tipe,
                cppt_t.pendaftaran_id,
                pendaftaran_t.pasien_id,
                cppt_t.ruangan_id,
                cppt_t.pegawai_id,
                cppt_t.tgl_cppt AS tgl_soaprj,
                cppt_t.a_diag_utama,
                cppt_t.a_diag_penyerta,
                cppt_t.subject,
                cppt_t.object,
                cppt_t.planning,
                cppt_t.catatan_dokter,
                pegawai_m.nama_pegawai,
                kelompokpegawai_m.kelompokpegawai_id,
                kelompokpegawai_m.kelompokpegawai_nama,
                ruangan_m.ruangan_nama,
                pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
                NULL::integer AS soaprj_id,
                cppt_t.cppt_id,
                cppt_t.referred_id,
                kamarruangan_m.kamarruangan_nokamar,
                kamartempattidur_m.no_tempattidur,
                cppt_t.instruksi,
                cppt_t.is_verifikasi,
                cppt_t.catatan_perawat,
                cppt_t.is_deleted,
                pegawai_verif.nama_pegawai AS pegawai_verifikasi,
                cppt_t.tgl_verifikasi,
                pegawai_m.nama_pegawai AS pegawai_cppt,
                pemberi_instruksi.nama_pegawai AS pegawai_instruksi,
                cppt_t.pemberi_instruksi_id,
                cppt_t.is_verifikasi_verbal,
                cppt_t.tgl_verif_verbal,
                cppt_t.pegawai_verbal_id,
                pegawai_verif2.nama_pegawai AS pegawai_verifikasi_verbal,
                pasienadmisi_t.pegawai_id AS dokteradmisi_id,
                dokteradmisi.nama_pegawai AS dokteradmisi_nama,
                cppt_t.is_instruksi_pulang,
                pendaftaran_t.pasienadmisi_id,
                    CASE COALESCE(lab.ct_lab, (0)::bigint)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_lab,
                    CASE COALESCE(rad.ct_rad, (0)::bigint)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_rad,
                    CASE COALESCE(reseptur.ct_reseptur, (0)::bigint)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_reseptur,
                    CASE COALESCE(konsul.ct_konsul, (0)::bigint)
                        WHEN 0 THEN false
                        ELSE true
                    END AS is_konsul,
                pendaftaran_t.no_pendaftaran,
                pasien.no_rekam_medik,
                spesialis_m.spesialis_id,
                spesialis_m.spesialis_nama
               FROM ((((((((((((((((((cppt_t
                 JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN ( SELECT pasien_m.pasien_id,
                        pasien_m.no_rekam_medik
                       FROM pasien_m) pasien ON ((pendaftaran_t.pasien_id = pasien.pasien_id)))
                 LEFT JOIN pegawai_m ON ((cppt_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                 LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                 LEFT JOIN ruangan_m ON ((cppt_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN pasienadmisi_t ON ((cppt_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 LEFT JOIN kamarruangan_m ON ((cppt_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
                 LEFT JOIN kamartempattidur_m ON ((cppt_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
                 LEFT JOIN pegawai_m pegawai_verif ON ((cppt_t.pegawai_verifikasi_id = pegawai_verif.pegawai_id)))
                 LEFT JOIN pegawai_m pegawai_verif2 ON ((cppt_t.pegawai_verbal_id = pegawai_verif2.pegawai_id)))
                 LEFT JOIN pegawai_m dokteradmisi ON ((pasienadmisi_t.pegawai_id = dokteradmisi.pegawai_id)))
                 LEFT JOIN pegawai_m pemberi_instruksi ON ((cppt_t.pemberi_instruksi_id = pemberi_instruksi.pegawai_id)))
                 LEFT JOIN ( SELECT count(instruksi_t.cppt_id) AS ct_lab,
                        instruksi_t.cppt_id
                       FROM ((instruksi_t
                         JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                         JOIN ruangan_m ruangan_m_1 ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m_1.ruangan_id)))
                      WHERE (ruangan_m_1.instalasi_id = 4)
                      GROUP BY instruksi_t.cppt_id) lab ON ((COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = lab.cppt_id)))
                 LEFT JOIN ( SELECT count(instruksi_t.cppt_id) AS ct_rad,
                        instruksi_t.cppt_id
                       FROM ((instruksi_t
                         JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                         JOIN ruangan_m ruangan_m_1 ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m_1.ruangan_id)))
                      WHERE (ruangan_m_1.instalasi_id = 5)
                      GROUP BY instruksi_t.cppt_id) rad ON ((COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = rad.cppt_id)))
                 LEFT JOIN ( SELECT count(instruksi_t.cppt_id) AS ct_reseptur,
                        instruksi_t.cppt_id
                       FROM (instruksi_t
                         JOIN reseptur_t ON ((instruksi_t.instruksi_id = reseptur_t.instruksi_id)))
                      GROUP BY instruksi_t.cppt_id) reseptur ON ((COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = reseptur.cppt_id)))
                 LEFT JOIN ( SELECT count(permintaankonsul_t.cppt_id) AS ct_konsul,
                        permintaankonsul_t.cppt_id
                       FROM permintaankonsul_t
                      GROUP BY permintaankonsul_t.cppt_id) konsul ON ((COALESCE(cppt_t.referred_id, cppt_t.cppt_id) = konsul.cppt_id)))
                 LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
              WHERE ((cppt_t.pasienadmisi_id IS NOT NULL) AND (cppt_t.is_deleted IS FALSE));
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."soaprj_v";
        ');

        $this->execute('
            CREATE VIEW "public"."soaprj_v" AS  SELECT t1.jenis,
                t1.grouping_tipe,
                t1.jenis_urutan,
                t1.pendaftaran_id,
                t1.ruangan_id,
                t1.ruangan_nama,
                t1.pegawai_id, 
                t1.nama_pegawai,
                t1.kelompokpegawai_nama,
                t1.nama_profesi,
                t1.subject,
                t1.object,
                t1.a_diag_utama,
                t1.a_diag_penyerta,
                t1.tgl_tindakan,
                t1.instruksi,
                t1.soaprj_id,
                t1.tgl_soaprj,
                t1.planning,
                t1.catatan_dokter,
                t1.daftar_paket,
                t1.catatan_dokterpengirim,
                t1.is_hapus,
                t1.status,
                t1.cyto_tindakan,
                t1.qty,
                t1.no_penunjang,
                t1.verbal_instruksi,
                t1.pemberi_instruksi_id,
                t1.is_verifikasi_verbal,
                t1.tgl_verif_verbal,
                t1.pegawai_verbal_id,
                t1.pasien_id,
                t1.ruangan_penunjang_id,
                t1.ruangan_penunjang_nama,
                t1.instalasi_penunjang_id,
                t1.instalasi_penunjang_nama,
                t1.kelompoktindakan_id,
                t1.pegawai_soap,
                t1.kelompokpegawai_soap,
                t1.is_bayar,
                t1.permintaankepenunjang_id,
                t1.obatalkespasien_id,
                t1.tindakanpelayanan_id,
                t1.pasienkirimkeunitlain_id,
                t1.alasan_batal,
                t1.pegawai_hapus_id,
                t1.pegawai_hapus_nama,
                t1.tgl_batal,
                t1.spesialis_id,
                t1.spesialis_nama
               FROM ( SELECT \'TINDAKAN\'::text AS jenis,
                        \'Tindakan & BMHP\'::text AS grouping_tipe,
                        2 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        ruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        soaprj_t.pegawai_id,
                        pegawai_m.nama_pegawai,
                        kelompokpegawai_m.kelompokpegawai_nama,
                        pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
                        soaprj_t.subject,
                        soaprj_t.object,
                        soaprj_t.a_diag_utama,
                        soaprj_t.a_diag_penyerta,
                        tindakanpelayanan_t.tgl_tindakan,
                        daftartindakan_m.daftartindakan_nama AS instruksi,
                        soaprj_t.soaprj_id,
                        soaprj_t.tgl_soaprj,
                        soaprj_t.planning,
                        soaprj_t.catatan_dokter,
                        array_to_json(NULL::character varying[]) AS daftar_paket,
                        \'-\'::text AS catatan_dokterpengirim,
                        tindakanpelayanan_t.is_deleted AS is_hapus,
                        NULL::character varying AS status,
                        tindakanpelayanan_t.cyto_tindakan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        NULL::character varying AS no_penunjang,
                        soaprj_t.instruksi AS verbal_instruksi,
                        soaprj_t.pemberi_instruksi_id,
                        soaprj_t.is_verifikasi_verbal,
                        soaprj_t.tgl_verif_verbal,
                        soaprj_t.pegawai_verbal_id,
                        pendaftaran_t.pasien_id,
                        ruangan_m.ruangan_id AS ruangan_penunjang_id,
                        ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
                        instalasi_m.instalasi_id AS instalasi_penunjang_id,
                        instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
                        daftartindakan_m.kelompoktindakan_id,
                        COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
                        COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
                            CASE COALESCE(tindakanpelayanan_t.tindakansudahbayar_id, 0)
                                WHEN 0 THEN false
                                ELSE true
                            END AS is_bayar,
                        NULL::integer AS permintaankepenunjang_id,
                        NULL::integer AS obatalkespasien_id,
                        tindakanpelayanan_t.tindakanpelayanan_id,
                        NULL::integer AS pasienkirimkeunitlain_id,
                        tindakanpelayanan_t.alasan_batal,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        tindakanpelayanan_t.deleted_date AS tgl_batal,
                        spesialis_m.spesialis_id,
                        spesialis_m.spesialis_nama
                       FROM (((((((((((((pendaftaran_t
                         LEFT JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                         LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE(tindakanpelayanan_t.dokterpenanggungjawab_id, (pendaftaran_t.pegawai_id)::bigint) = pegawai_m.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
                         LEFT JOIN loginpemakai_k leg_deleted ON ((tindakanpelayanan_t.deleted_by = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                         LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
                      WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL)
                    UNION ALL
                     SELECT \'PAKET\'::text AS jenis,
                        \'Tindakan & BMHP\'::text AS grouping_tipe,
                        2 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        ruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai,
                        kelompokpegawai_m.kelompokpegawai_nama,
                        pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
                        soaprj_t.subject,
                        soaprj_t.object,
                        soaprj_t.a_diag_utama,
                        soaprj_t.a_diag_penyerta,
                        tindakanpelayanan_t.tgl_tindakan,
                        tipepaket_m.tipepaket_nama AS instruksi,
                        soaprj_t.soaprj_id,
                        soaprj_t.tgl_soaprj,
                        soaprj_t.planning,
                        soaprj_t.catatan_dokter,
                        array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
                               FROM (paketpelayanan_mp
                                 LEFT JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                              WHERE (paketpelayanan_mp.tipepaket_id = tindakanpelayanan_t.tipepaket_id))) AS daftar_paket,
                        \'-\'::text AS catatan_dokterpengirim,
                        tindakanpelayanan_t.is_deleted AS is_hapus,
                        NULL::character varying AS status,
                        tindakanpelayanan_t.cyto_tindakan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        NULL::character varying AS no_penunjang,
                        soaprj_t.instruksi AS verbal_instruksi,
                        soaprj_t.pemberi_instruksi_id,
                        soaprj_t.is_verifikasi_verbal,
                        soaprj_t.tgl_verif_verbal,
                        soaprj_t.pegawai_verbal_id,
                        pendaftaran_t.pasien_id,
                        ruangan_m.ruangan_id AS ruangan_penunjang_id,
                        ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
                        instalasi_m.instalasi_id AS instalasi_penunjang_id,
                        instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
                        NULL::integer AS kelompoktindakan_id,
                        COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
                        COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
                            CASE COALESCE(tindakanpelayanan_t.tindakansudahbayar_id, 0)
                                WHEN 0 THEN false
                                ELSE true
                            END AS is_bayar,
                        NULL::integer AS permintaankepenunjang_id,
                        NULL::integer AS obatalkespasien_id,
                        tindakanpelayanan_t.tindakanpelayanan_id,
                        NULL::integer AS pasienkirimkeunitlain_id,
                        tindakanpelayanan_t.alasan_batal,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        tindakanpelayanan_t.deleted_date AS tgl_batal,
                        spesialis_m.spesialis_id,
                        spesialis_m.spesialis_nama
                       FROM (((((((((((((pendaftaran_t
                         JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                         LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE(tindakanpelayanan_t.dokterpenanggungjawab_id, (pendaftaran_t.pegawai_id)::bigint) = pegawai_m.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                         LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
                         LEFT JOIN loginpemakai_k leg_deleted ON ((tindakanpelayanan_t.deleted_by = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                         LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
                      WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL)
                    UNION ALL
                     SELECT \'BMHP\'::text AS jenis,
                        \'Tindakan & BMHP\'::text AS grouping_tipe,
                        2 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        ruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai,
                        kelompokpegawai_m.kelompokpegawai_nama,
                        pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
                        soaprj_t.subject,
                        soaprj_t.object,
                        soaprj_t.a_diag_utama,
                        soaprj_t.a_diag_penyerta,
                        obatalkespasien_t.tglpelayanan AS tgl_tindakan,
                        obatalkes_m.obatalkes_nama AS instruksi,
                        soaprj_t.soaprj_id,
                        soaprj_t.tgl_soaprj,
                        soaprj_t.planning,
                        soaprj_t.catatan_dokter,
                        array_to_json(NULL::character varying[]) AS daftar_paket,
                        \'-\'::text AS catatan_dokterpengirim,
                        obatalkespasien_t.is_deleted AS is_hapus,
                        NULL::character varying AS status,
                        NULL::boolean AS cyto_tindakan,
                        obatalkespasien_t.qty_oa AS qty,
                        NULL::character varying AS no_penunjang,
                        soaprj_t.instruksi AS verbal_instruksi,
                        soaprj_t.pemberi_instruksi_id,
                        soaprj_t.is_verifikasi_verbal,
                        soaprj_t.tgl_verif_verbal,
                        soaprj_t.pegawai_verbal_id,
                        pendaftaran_t.pasien_id,
                        ruangan_m.ruangan_id AS ruangan_penunjang_id,
                        ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
                        instalasi_m.instalasi_id AS instalasi_penunjang_id,
                        instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
                        NULL::integer AS kelompoktindakan_id,
                        COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
                        COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
                            CASE COALESCE(obatalkespasien_t.obatsudahbayar_id, 0)
                                WHEN 0 THEN false
                                ELSE true
                            END AS is_bayar,
                        NULL::integer AS permintaankepenunjang_id,
                        obatalkespasien_t.obatalkespasien_id,
                        NULL::integer AS tindakanpelayanan_id,
                        NULL::integer AS pasienkirimkeunitlain_id,
                        obatalkespasien_t.alasan_batal,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        obatalkespasien_t.deleted_date AS tgl_batal,
                        spesialis_m.spesialis_id,
                        spesialis_m.spesialis_nama
                       FROM (((((((((((((pendaftaran_t
                         LEFT JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                         LEFT JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE(obatalkespasien_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                         LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
                         LEFT JOIN loginpemakai_k leg_deleted ON ((obatalkespasien_t.deleted_by = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                         LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
                      WHERE (obatalkespasien_t.status_bmhp IS NOT NULL)
                    UNION ALL
                     SELECT
                            CASE
                                WHEN ((racikan_m.racikan_singkatan)::text = \'OR\'::text) THEN \'Racikan\'::text
                                ELSE \'Non Racikan\'::text
                            END AS jenis,
                        \'Reseptur\'::text AS grouping_tipe,
                        1 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        ruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai,
                        kelompokpegawai_m.kelompokpegawai_nama,
                        pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
                        soaprj_t.subject,
                        soaprj_t.object,
                        soaprj_t.a_diag_utama,
                        soaprj_t.a_diag_penyerta,
                        resepturdetail_t.created_date AS tgl_tindakan,
                        obatalkes_m.obatalkes_nama AS instruksi,
                        soaprj_t.soaprj_id,
                        soaprj_t.tgl_soaprj,
                        soaprj_t.planning,
                        soaprj_t.catatan_dokter,
                        array_to_json(NULL::character varying[]) AS daftar_paket,
                        \'-\'::text AS catatan_dokterpengirim,
                        resepturdetail_t.is_deleted AS is_hapus,
                        (reseptur_t.status_reseptur)::character varying AS status,
                        NULL::boolean AS cyto_tindakan,
                        resepturdetail_t.qty_reseptur AS qty,
                        NULL::character varying AS no_penunjang,
                        soaprj_t.instruksi AS verbal_instruksi,
                        soaprj_t.pemberi_instruksi_id,
                        soaprj_t.is_verifikasi_verbal,
                        soaprj_t.tgl_verif_verbal,
                        soaprj_t.pegawai_verbal_id,
                        pendaftaran_t.pasien_id,
                        ruangan_m.ruangan_id AS ruangan_penunjang_id,
                        ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
                        instalasi_m.instalasi_id AS instalasi_penunjang_id,
                        instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
                        NULL::integer AS kelompoktindakan_id,
                        COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
                        COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
                            CASE COALESCE(obatalkespasien_t.obatsudahbayar_id, 0)
                                WHEN 0 THEN false
                                ELSE true
                            END AS is_bayar,
                        NULL::integer AS permintaankepenunjang_id,
                        obatalkespasien_t.obatalkespasien_id,
                        NULL::integer AS tindakanpelayanan_id,
                        NULL::integer AS pasienkirimkeunitlain_id,
                        obatalkespasien_t.alasan_batal,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        obatalkespasien_t.deleted_date AS tgl_batal,
                        spesialis_m.spesialis_id,
                        spesialis_m.spesialis_nama
                       FROM ((((((((((((((((reseptur_t
                         JOIN resepturdetail_t ON ((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id)))
                         LEFT JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                         LEFT JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
                         JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = reseptur_t.pendaftaran_id)))
                         LEFT JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE(reseptur_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
                         LEFT JOIN obatalkespasien_t ON ((resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id)))
                         LEFT JOIN loginpemakai_k leg_deleted ON ((obatalkespasien_t.deleted_by = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                         LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
                      WHERE ((racikan_m.racikan_singkatan)::text <> \'OR\'::text)
                    UNION ALL
                     SELECT \'Racikan\'::text AS jenis,
                        \'Reseptur\'::text AS grouping_tipe,
                        1 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        ruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai,
                        kelompokpegawai_m.kelompokpegawai_nama,
                        pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
                        soaprj_t.subject,
                        soaprj_t.object,
                        soaprj_t.a_diag_utama,
                        soaprj_t.a_diag_penyerta,
                        resepturracikan_t.created_date AS tgl_tindakan,
                        resepturracikan_t.racikan AS instruksi,
                        soaprj_t.soaprj_id,
                        soaprj_t.tgl_soaprj,
                        soaprj_t.planning,
                        soaprj_t.catatan_dokter,
                        array_to_json(NULL::character varying[]) AS daftar_paket,
                        \'-\'::text AS catatan_dokterpengirim,
                        resepturracikan_t.is_deleted AS is_hapus,
                        (reseptur_t.status_reseptur)::character varying AS status,
                        NULL::boolean AS cyto_tindakan,
                        0 AS qty,
                        NULL::character varying AS no_penunjang,
                        soaprj_t.instruksi AS verbal_instruksi,
                        soaprj_t.pemberi_instruksi_id,
                        soaprj_t.is_verifikasi_verbal,
                        soaprj_t.tgl_verif_verbal,
                        soaprj_t.pegawai_verbal_id,
                        pendaftaran_t.pasien_id,
                        ruangan_m.ruangan_id AS ruangan_penunjang_id,
                        ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
                        instalasi_m.instalasi_id AS instalasi_penunjang_id,
                        instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
                        NULL::integer AS kelompoktindakan_id,
                        COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
                        COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
                            CASE COALESCE(obatalkespasien_t.obatsudahbayar_id, 0)
                                WHEN 0 THEN false
                                ELSE true
                            END AS is_bayar,
                        NULL::integer AS permintaankepenunjang_id,
                        obatalkespasien_t.obatalkespasien_id,
                        NULL::integer AS tindakanpelayanan_id,
                        NULL::integer AS pasienkirimkeunitlain_id,
                        obatalkespasien_t.alasan_batal,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        obatalkespasien_t.deleted_date AS tgl_batal,
                        spesialis_m.spesialis_id,
                        spesialis_m.spesialis_nama
                       FROM (((((((((((((((reseptur_t
                         JOIN resepturracikan_t ON ((reseptur_t.reseptur_id = resepturracikan_t.reseptur_id)))
                         JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = reseptur_t.pendaftaran_id)))
                         LEFT JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE(reseptur_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
                         LEFT JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
                         LEFT JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
                         LEFT JOIN loginpemakai_k leg_deleted ON ((obatalkespasien_t.deleted_by = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                         LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
                    UNION ALL
                     SELECT \'TINDAKAN\'::text AS jenis,
                        \'Penunjang\'::text AS grouping_tipe,
                        3 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        ruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai,
                        kelompokpegawai_m.kelompokpegawai_nama,
                        pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
                        soaprj_t.subject,
                        soaprj_t.object,
                        soaprj_t.a_diag_utama,
                        soaprj_t.a_diag_penyerta,
                        pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_tindakan,
                        daftartindakan_m.daftartindakan_nama AS instruksi,
                        soaprj_t.soaprj_id,
                        soaprj_t.tgl_soaprj,
                        soaprj_t.planning,
                        soaprj_t.catatan_dokter,
                        array_to_json(NULL::character varying[]) AS daftar_paket,
                        pasienkirimkeunitlain_t.catatan_dokterpengirim,
                        permintaankepenunjang_t.is_deleted AS is_hapus,
                            CASE
                                WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
                                ELSE pasienmasukpenunjang_t.status_periksa
                            END AS status,
                        NULL::boolean AS cyto_tindakan,
                        permintaankepenunjang_t.qtypermintaan AS qty,
                        pasienkirimkeunitlain_t.no_orderkeunitlain AS no_penunjang,
                        soaprj_t.instruksi AS verbal_instruksi,
                        soaprj_t.pemberi_instruksi_id,
                        soaprj_t.is_verifikasi_verbal,
                        soaprj_t.tgl_verif_verbal,
                        soaprj_t.pegawai_verbal_id,
                        pendaftaran_t.pasien_id,
                        r_penunjang.ruangan_id AS ruangan_penunjang_id,
                        r_penunjang.ruangan_nama AS ruangan_penunjang_nama,
                        i_penunjang.instalasi_id AS instalasi_penunjang_id,
                        i_penunjang.instalasi_nama AS instalasi_penunjang_nama,
                        daftartindakan_m.kelompoktindakan_id,
                        COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
                        COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
                            CASE COALESCE(tindakanpelayanan_t.tindakansudahbayar_id, 0)
                                WHEN 0 THEN false
                                ELSE true
                            END AS is_bayar,
                        permintaankepenunjang_t.permintaankepenunjang_id,
                        NULL::integer AS obatalkespasien_id,
                        tindakanpelayanan_t.tindakanpelayanan_id,
                        pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                        COALESCE(tindakanpelayanan_t.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        COALESCE(tindakanpelayanan_t.deleted_date, permintaankepenunjang_t.deleted_date) AS tgl_batal,
                        spesialis_m.spesialis_id,
                        spesialis_m.spesialis_nama
                       FROM (((((((((((((((((pasienkirimkeunitlain_t
                         JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                         LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                         JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = pasienkirimkeunitlain_t.pendaftaran_id)))
                         LEFT JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE(pasienmasukpenunjang_t.pegawai_id, pasienkirimkeunitlain_t.pegawai_id) = pegawai_m.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         LEFT JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         LEFT JOIN ruangan_m r_penunjang ON ((pasienkirimkeunitlain_t.ruangan_id = r_penunjang.ruangan_id)))
                         LEFT JOIN instalasi_m i_penunjang ON ((pasienkirimkeunitlain_t.instalasi_id = i_penunjang.instalasi_id)))
                         LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
                         LEFT JOIN tindakanpelayanan_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id) AND (permintaankepenunjang_t.daftartindakan_id = tindakanpelayanan_t.daftartindakan_id))))
                         LEFT JOIN loginpemakai_k leg_deleted ON ((COALESCE(tindakanpelayanan_t.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                         LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
                    UNION ALL
                     SELECT \'PAKET\'::text AS jenis,
                        \'Penunjang\'::text AS grouping_tipe,
                        3 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        ruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai,
                        kelompokpegawai_m.kelompokpegawai_nama,
                        pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
                        soaprj_t.subject,
                        soaprj_t.object,
                        soaprj_t.a_diag_utama,
                        soaprj_t.a_diag_penyerta,
                        pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_tindakan,
                        tipepaket_m.tipepaket_nama AS instruksi,
                        soaprj_t.soaprj_id,
                        soaprj_t.tgl_soaprj,
                        soaprj_t.planning,
                        soaprj_t.catatan_dokter,
                        array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
                               FROM (paketpelayanan_mp
                                 LEFT JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                              WHERE (paketpelayanan_mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id))) AS daftar_paket,
                        pasienkirimkeunitlain_t.catatan_dokterpengirim,
                        permintaankepenunjang_t.is_deleted AS is_hapus,
                            CASE
                                WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
                                ELSE pasienmasukpenunjang_t.status_periksa
                            END AS status,
                        NULL::boolean AS cyto_tindakan,
                        permintaankepenunjang_t.qtypermintaan AS qty,
                        pasienkirimkeunitlain_t.no_orderkeunitlain AS no_penunjang,
                        soaprj_t.instruksi AS verbal_instruksi,
                        soaprj_t.pemberi_instruksi_id,
                        soaprj_t.is_verifikasi_verbal,
                        soaprj_t.tgl_verif_verbal,
                        soaprj_t.pegawai_verbal_id,
                        pendaftaran_t.pasien_id,
                        r_penunjang.ruangan_id AS ruangan_penunjang_id,
                        r_penunjang.ruangan_nama AS ruangan_penunjang_nama,
                        i_penunjang.instalasi_id AS instalasi_penunjang_id,
                        i_penunjang.instalasi_nama AS instalasi_penunjang_nama,
                        NULL::integer AS kelompoktindakan_id,
                        COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
                        COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
                            CASE COALESCE(tindakanpelayanan_t.tindakansudahbayar_id, 0)
                                WHEN 0 THEN false
                                ELSE true
                            END AS is_bayar,
                        permintaankepenunjang_t.permintaankepenunjang_id,
                        NULL::integer AS obatalkespasien_id,
                        tindakanpelayanan_t.tindakanpelayanan_id,
                        pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                        COALESCE(tindakanpelayanan_t.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        COALESCE(tindakanpelayanan_t.deleted_date, permintaankepenunjang_t.deleted_date) AS tgl_batal,
                        spesialis_m.spesialis_id,
                        spesialis_m.spesialis_nama
                       FROM (((((((((((((((((pasienkirimkeunitlain_t
                         JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                         LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                         JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = pasienkirimkeunitlain_t.pendaftaran_id)))
                         LEFT JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE(pasienmasukpenunjang_t.pegawai_id, pasienkirimkeunitlain_t.pegawai_id) = pegawai_m.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                         LEFT JOIN ruangan_m r_penunjang ON ((pasienkirimkeunitlain_t.ruangan_id = r_penunjang.ruangan_id)))
                         LEFT JOIN instalasi_m i_penunjang ON ((pasienkirimkeunitlain_t.instalasi_id = i_penunjang.instalasi_id)))
                         LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
                         LEFT JOIN tindakanpelayanan_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tipepaket_id = permintaankepenunjang_t.tipepaket_id))))
                         LEFT JOIN loginpemakai_k leg_deleted ON ((COALESCE(tindakanpelayanan_t.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                         LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
                    UNION ALL
                     SELECT \'Verbal Order\'::text AS jenis,
                        \'Verbal Order\'::text AS grouping_tipe,
                        5 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        ruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai,
                        kelompokpegawai_m.kelompokpegawai_nama,
                        \'-\'::text AS nama_profesi,
                        soaprj_t.subject,
                        soaprj_t.object,
                        array_to_json(NULL::character varying[]) AS a_diag_utama,
                        array_to_json(NULL::character varying[]) AS a_diag_penyerta,
                        soaprj_t.tgl_soaprj AS tgl_tindakan,
                        soaprj_t.instruksi,
                        soaprj_t.soaprj_id,
                        soaprj_t.tgl_soaprj,
                        soaprj_t.planning,
                        soaprj_t.catatan_dokter,
                        array_to_json(NULL::character varying[]) AS daftar_paket,
                        \'-\'::text AS catatan_dokterpengirim,
                        soaprj_t.is_deleted AS is_hapus,
                        \'-\'::text AS status,
                        NULL::boolean AS cyto_tindakan,
                        NULL::double precision AS qty,
                        \'-\'::text AS no_penunjang,
                        soaprj_t.instruksi AS verbal_instruksi,
                        soaprj_t.pemberi_instruksi_id,
                        soaprj_t.is_verifikasi_verbal,
                        soaprj_t.tgl_verif_verbal,
                        soaprj_t.pegawai_verbal_id,
                        pendaftaran_t.pasien_id,
                        ruangan_m.ruangan_id AS ruangan_penunjang_id,
                        ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
                        instalasi_m.instalasi_id AS instalasi_penunjang_id,
                        instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
                        NULL::integer AS kelompoktindakan_id,
                        COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
                        COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
                        false AS is_bayar,
                        NULL::integer AS permintaankepenunjang_id,
                        NULL::integer AS obatalkespasien_id,
                        NULL::integer AS tindakanpelayanan_id,
                        NULL::integer AS pasienkirimkeunitlain_id,
                        NULL::text AS alasan_batal,
                        NULL::integer AS pegawai_hapus_id,
                        NULL::text AS pegawai_hapus_nama,
                        NULL::timestamp without time zone AS tgl_batal,
                        spesialis_m.spesialis_id,
                        spesialis_m.spesialis_nama
                       FROM ((((((((pendaftaran_t
                         JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE(soaprj_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id)))
                         JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
                         LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
                      WHERE (soaprj_t.instruksi IS NOT NULL)) t1
              ORDER BY t1.pendaftaran_id DESC;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."cpptrjdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."cpptrjdetail_v" AS  SELECT \'NON_PAKET\'::text AS jenis,
                pendaftaran_t.pendaftaran_id,
                soaprj_t.ruangan_id,
                ruangan_m.ruangan_nama,
                soaprj_t.pegawai_id, 
                pegawai_m.nama_pegawai,
                kelompokpegawai_m.kelompokpegawai_nama,
                soaprj_t.subject,
                soaprj_t.object,
                soaprj_t.a_diag_utama,
                soaprj_t.a_diag_penyerta,
                tindakanpelayanan_t.tgl_tindakan,
                daftartindakan_m.daftartindakan_nama AS instruksi,
                soaprj_t.soaprj_id,
                soaprj_t.tgl_soaprj,
                soaprj_t.planning,
                pendaftaran_t.pasien_id,
                spesialis_m.spesialis_id,
                spesialis_m.spesialis_nama
               FROM (((((((pendaftaran_t
                 JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                 JOIN ruangan_m ON ((soaprj_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN pegawai_m ON ((soaprj_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                 LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                 LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
            UNION ALL
             SELECT \'PAKET\'::text AS jenis,
                pendaftaran_t.pendaftaran_id,
                soaprj_t.ruangan_id,
                ruangan_m.ruangan_nama,
                soaprj_t.pegawai_id,
                pegawai_m.nama_pegawai,
                kelompokpegawai_m.kelompokpegawai_nama,
                soaprj_t.subject,
                soaprj_t.object,
                soaprj_t.a_diag_utama,
                soaprj_t.a_diag_penyerta,
                tindakanpelayanan_t.tgl_tindakan,
                concat(tipepaket_m.tipepaket_nama, \'-\', daftartindakan_m.daftartindakan_nama) AS instruksi,
                soaprj_t.soaprj_id,
                soaprj_t.tgl_soaprj,
                soaprj_t.planning,
                pendaftaran_t.pasien_id,
                spesialis_m.spesialis_id,
                spesialis_m.spesialis_nama
               FROM (((((((((pendaftaran_t
                 JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                 JOIN ruangan_m ON ((soaprj_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN pegawai_m ON ((soaprj_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                 LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                 LEFT JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 LEFT JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                 LEFT JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
            UNION ALL
             SELECT \'OBAT\'::text AS jenis,
                pendaftaran_t.pendaftaran_id,
                soaprj_t.ruangan_id,
                ruangan_m.ruangan_nama,
                soaprj_t.pegawai_id,
                pegawai_m.nama_pegawai,
                kelompokpegawai_m.kelompokpegawai_nama,
                soaprj_t.subject,
                soaprj_t.object,
                soaprj_t.a_diag_utama,
                soaprj_t.a_diag_penyerta,
                obatalkespasien_t.tglpelayanan AS tgl_tindakan,
                obatalkes_m.obatalkes_nama AS instruksi,
                soaprj_t.soaprj_id,
                soaprj_t.tgl_soaprj,
                soaprj_t.planning,
                pendaftaran_t.pasien_id,
                spesialis_m.spesialis_id,
                spesialis_m.spesialis_nama
               FROM (((((((pendaftaran_t
                 JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                 JOIN ruangan_m ON ((soaprj_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN pegawai_m ON ((soaprj_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                 LEFT JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
                 LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                 LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210901_140433_improvment_soap_rs_spesialis_US1296_1297_1298 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210901_140433_improvment_soap_rs_spesialis_US1296_1297_1298 cannot be reverted.\n";

        return false;
    }
    */
}
