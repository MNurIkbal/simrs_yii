<?php

use yii\db\Migration;

/**
 * Class m220315_094632_migrate_DHC92_view_soaprj_v
 */
class m220315_094632_migrate_DHC92_view_soaprj_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
                t1.spesialis_nama,
                t1.is_cyto,
                t1.is_concern,
                t1.is_deleted,
                t1.grouping_tipe_key,
                t1.pemberi_instruksi_nama,
                t1.is_deleted_soap,
                t1.last_modified_by,
                t1.pegawai_update_nama,
                t1.status_periksa,
                    CASE
                        WHEN ((t1.status_periksa)::text = \'4\'::text) THEN true
                        WHEN ((t1.status_periksa)::text = \'433\'::text) THEN true
                        ELSE false
                    END AS is_pulang,
                t1.created_date
               FROM ( SELECT \'TINDAKAN\'::text AS jenis,
                        \'Tindakan & BMHP\'::text AS grouping_tipe,
                        2 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        ruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        COALESCE(peg_soap.pegawai_id, pegawai_m.pegawai_id) AS pegawai_id,
                        COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS nama_pegawai,
                        kelompokpegawai_m.kelompokpegawai_nama,
                        pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
                        soaprj_t.subject,
                        soaprj_t.object,
                        soaprj_t.a_diag_utama,
                        soaprj_t.a_diag_penyerta,
                        COALESCE(instruksitindakan_t.tgl_tindakan, tindakanpelayanan_t.tgl_tindakan) AS tgl_tindakan,
                        concat(daftartindakan_m.daftartindakan_nama, \' - \',
                            CASE
                                WHEN (COALESCE(instruksitindakan_t.is_cyto, tindakanpelayanan_t.cyto_tindakan) = true) THEN \'Cyto - \'::text
                                ELSE \'\'::text
                            END,
                            CASE
                                WHEN (COALESCE(instruksitindakan_t.is_concern, false) = true) THEN \'Informed Consent - \'::text
                                ELSE \'\'::text
                            END, COALESCE(instruksitindakan_t.qty, tindakanpelayanan_t.qty_tindakan)) AS instruksi,
                        soaprj_t.soaprj_id,
                        soaprj_t.tgl_soaprj,
                        soaprj_t.planning,
                        soaprj_t.catatan_dokter,
                        array_to_json(NULL::character varying[]) AS daftar_paket,
                        \'-\'::text AS catatan_dokterpengirim,
                        COALESCE(instruksitindakan_t.is_deleted, tindakanpelayanan_t.is_deleted) AS is_hapus,
                        NULL::character varying AS status,
                        COALESCE(instruksitindakan_t.is_cyto, tindakanpelayanan_t.cyto_tindakan) AS cyto_tindakan,
                        COALESCE(instruksitindakan_t.qty, tindakanpelayanan_t.qty_tindakan) AS qty,
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
                        COALESCE(instruksitindakan_t.alasan_batal, tindakanpelayanan_t.alasan_batal) AS alasan_batal,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        COALESCE(instruksitindakan_t.deleted_date, tindakanpelayanan_t.deleted_date) AS tgl_batal,
                        spesialis_m.spesialis_id,
                        spesialis_m.spesialis_nama,
                        instruksitindakan_t.is_cyto,
                        instruksitindakan_t.is_concern,
                        instruksitindakan_t.instruksitindakan_id,
                        COALESCE(instruksitindakan_t.is_deleted, tindakanpelayanan_t.is_deleted, soaprj_t.is_deleted) AS is_deleted,
                        \'tindakanbmhp\'::text AS grouping_tipe_key,
                        instruksi.nama_pegawai AS pemberi_instruksi_nama,
                        COALESCE(soaprj_t.is_deleted, false) AS is_deleted_soap,
                        COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) AS last_modified_by,
                        peg_updated.nama_pegawai AS pegawai_update_nama,
                        pendaftaran_t.status_periksa,
                        soaprj_t.created_date
                       FROM (((((((((((((((((pendaftaran_t
                         LEFT JOIN soaprj_t ON (((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id) AND (soaprj_t.is_deleted = false))))
                         LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                         LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                         LEFT JOIN instruksitindakan_t ON ((tindakanpelayanan_t.instruksitindakan_id = instruksitindakan_t.instruksitindakan_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE(tindakanpelayanan_t.dokterpenanggungjawab_id, (pendaftaran_t.pegawai_id)::bigint) = pegawai_m.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         LEFT JOIN daftartindakan_m ON ((COALESCE(instruksitindakan_t.daftartindakan_id, tindakanpelayanan_t.daftartindakan_id) = daftartindakan_m.daftartindakan_id)))
                         LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
                         LEFT JOIN loginpemakai_k leg_deleted ON ((tindakanpelayanan_t.deleted_by = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                         LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
                         LEFT JOIN pegawai_m instruksi ON ((soaprj_t.pemberi_instruksi_id = instruksi.pegawai_id)))
                         LEFT JOIN loginpemakai_k log_updated ON ((COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) = log_updated.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_updated ON ((log_updated.pegawai_id = peg_updated.pegawai_id)))
                      WHERE ((tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL) AND (soaprj_t.pemberi_instruksi_id IS NULL))
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
                        COALESCE(instruksitindakan_t.tgl_tindakan, tindakanpelayanan_t.tgl_tindakan) AS tgl_tindakan,
                        concat(tipepaket_m.tipepaket_nama, \' - \',
                            CASE
                                WHEN (COALESCE(instruksitindakan_t.is_cyto, tindakanpelayanan_t.cyto_tindakan) = true) THEN \'Cyto - \'::text
                                ELSE \'\'::text
                            END,
                            CASE
                                WHEN (COALESCE(instruksitindakan_t.is_concern, false) = true) THEN \'Informed Consent - \'::text
                                ELSE \'\'::text
                            END, COALESCE(instruksitindakan_t.qty, tindakanpelayanan_t.qty_tindakan)) AS instruksi,
                        soaprj_t.soaprj_id,
                        soaprj_t.tgl_soaprj,
                        soaprj_t.planning,
                        soaprj_t.catatan_dokter,
                        array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
                               FROM (paketpelayanan_mp
                                 LEFT JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                              WHERE (paketpelayanan_mp.tipepaket_id = tindakanpelayanan_t.tipepaket_id))) AS daftar_paket,
                        \'-\'::text AS catatan_dokterpengirim,
                        COALESCE(instruksitindakan_t.is_deleted, tindakanpelayanan_t.is_deleted) AS is_hapus,
                        NULL::character varying AS status,
                        COALESCE(instruksitindakan_t.is_cyto, tindakanpelayanan_t.cyto_tindakan) AS cyto_tindakan,
                        COALESCE(instruksitindakan_t.qty, tindakanpelayanan_t.qty_tindakan) AS qty,
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
                        COALESCE(instruksitindakan_t.alasan_batal, tindakanpelayanan_t.alasan_batal) AS alasan_batal,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        COALESCE(instruksitindakan_t.deleted_date, tindakanpelayanan_t.deleted_date) AS tgl_batal,
                        spesialis_m.spesialis_id,
                        spesialis_m.spesialis_nama,
                        instruksitindakan_t.is_cyto,
                        instruksitindakan_t.is_concern,
                        instruksitindakan_t.instruksitindakan_id,
                        instruksitindakan_t.is_deleted,
                        \'tindakanbmhp\'::text AS grouping_tipe_key,
                        instruksi.nama_pegawai AS pemberi_instruksi_nama,
                        COALESCE(soaprj_t.is_deleted, false) AS is_deleted_soap,
                        COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) AS last_modified_by,
                        peg_updated.nama_pegawai AS pegawai_update_nama,
                        pendaftaran_t.status_periksa,
                        soaprj_t.created_date
                       FROM (((((((((((((((((pendaftaran_t
                         JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                         LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                         LEFT JOIN instruksitindakan_t ON ((tindakanpelayanan_t.instruksitindakan_id = instruksitindakan_t.instruksitindakan_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE(tindakanpelayanan_t.dokterpenanggungjawab_id, (pendaftaran_t.pegawai_id)::bigint) = pegawai_m.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         JOIN tipepaket_m ON ((COALESCE(instruksitindakan_t.tipepaket_id, tindakanpelayanan_t.tipepaket_id) = tipepaket_m.tipepaket_id)))
                         LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
                         LEFT JOIN loginpemakai_k leg_deleted ON ((tindakanpelayanan_t.deleted_by = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                         LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
                         LEFT JOIN pegawai_m instruksi ON ((soaprj_t.pemberi_instruksi_id = instruksi.pegawai_id)))
                         LEFT JOIN loginpemakai_k log_updated ON ((COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) = log_updated.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_updated ON ((log_updated.pegawai_id = peg_updated.pegawai_id)))
                      WHERE ((tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL) AND (soaprj_t.pemberi_instruksi_id IS NULL))
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
                        concat(obatalkes_m.obatalkes_nama, \' - \', COALESCE(obatalkespasien_t.qty_oa, (0)::double precision)) AS instruksi,
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
                        spesialis_m.spesialis_nama,
                        false AS is_cyto,
                        false AS is_concern,
                        NULL::integer AS instruksitindakan_id,
                        obatalkespasien_t.is_deleted,
                        \'tindakanbmhp\'::text AS grouping_tipe_key,
                        instruksi.nama_pegawai AS pemberi_instruksi_nama,
                        COALESCE(soaprj_t.is_deleted, false) AS is_deleted_soap,
                        COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) AS last_modified_by,
                        peg_updated.nama_pegawai AS pegawai_update_nama,
                        pendaftaran_t.status_periksa,
                        soaprj_t.created_date
                       FROM ((((((((((((((((pendaftaran_t
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
                         LEFT JOIN pegawai_m instruksi ON ((soaprj_t.pemberi_instruksi_id = instruksi.pegawai_id)))
                         LEFT JOIN loginpemakai_k log_updated ON ((COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) = log_updated.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_updated ON ((log_updated.pegawai_id = peg_updated.pegawai_id)))
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
                        concat(obatalkes_m.obatalkes_nama, \' - \', resepturdetail_t.qty_reseptur) AS instruksi,
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
                        spesialis_m.spesialis_nama,
                        false AS is_cyto,
                        false AS is_concern,
                        NULL::integer AS instruksitindakan_id,
                        resepturdetail_t.is_deleted,
                        \'reseptur\'::text AS grouping_tipe_key,
                        instruksi.nama_pegawai AS pemberi_instruksi_nama,
                        COALESCE(soaprj_t.is_deleted, false) AS is_deleted_soap,
                        COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) AS last_modified_by,
                        peg_updated.nama_pegawai AS pegawai_update_nama,
                        pendaftaran_t.status_periksa,
                        soaprj_t.created_date
                       FROM (((((((((((((((((((reseptur_t
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
                         LEFT JOIN pegawai_m instruksi ON ((soaprj_t.pemberi_instruksi_id = instruksi.pegawai_id)))
                         LEFT JOIN loginpemakai_k log_updated ON ((COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) = log_updated.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_updated ON ((log_updated.pegawai_id = peg_updated.pegawai_id)))
                    UNION ALL
                     SELECT
                            CASE
                                WHEN ((COALESCE(resepturracikan_t.type, \'OR\'::character varying))::text = \'OR\'::text) THEN \'Racikan\'::text
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
                        spesialis_m.spesialis_nama,
                        false AS is_cyto,
                        false AS is_concern,
                        0 AS instruksitindakan_id,
                        resepturracikan_t.is_deleted,
                        \'reseptur\'::text AS grouping_tipe_key,
                        instruksi.nama_pegawai AS pemberi_instruksi_nama,
                        COALESCE(soaprj_t.is_deleted, false) AS is_deleted_soap,
                        COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) AS last_modified_by,
                        peg_updated.nama_pegawai AS pegawai_update_nama,
                        pendaftaran_t.status_periksa,
                        soaprj_t.created_date
                       FROM ((((((((((((((((((reseptur_t
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
                         LEFT JOIN pegawai_m instruksi ON ((soaprj_t.pemberi_instruksi_id = instruksi.pegawai_id)))
                         LEFT JOIN loginpemakai_k log_updated ON ((COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) = log_updated.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_updated ON ((log_updated.pegawai_id = peg_updated.pegawai_id)))
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
                        concat(daftartindakan_m.daftartindakan_nama, \' - \', permintaankepenunjang_t.qtypermintaan) AS instruksi,
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
                        spesialis_m.spesialis_nama,
                        false AS is_cyto,
                        false AS is_concern,
                        0 AS instruksitindakan_id,
                        permintaankepenunjang_t.is_deleted,
                        \'penunjang\'::text AS grouping_tipe_key,
                        instruksi.nama_pegawai AS pemberi_instruksi_nama,
                        COALESCE(soaprj_t.is_deleted, false) AS is_deleted_soap,
                        COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) AS last_modified_by,
                        peg_updated.nama_pegawai AS pegawai_update_nama,
                        pendaftaran_t.status_periksa,
                        soaprj_t.created_date
                       FROM ((((((((((((((((((((pasienkirimkeunitlain_t
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
                         LEFT JOIN pegawai_m instruksi ON ((soaprj_t.pemberi_instruksi_id = instruksi.pegawai_id)))
                         LEFT JOIN loginpemakai_k log_updated ON ((COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) = log_updated.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_updated ON ((log_updated.pegawai_id = peg_updated.pegawai_id)))
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
                        concat(tipepaket_m.tipepaket_nama, \' - \', permintaankepenunjang_t.qtypermintaan) AS instruksi,
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
                        spesialis_m.spesialis_nama,
                        false AS is_cyto,
                        false AS is_concern,
                        0 AS instruksitindakan_id,
                        permintaankepenunjang_t.is_deleted,
                        \'penunjang\'::text AS grouping_tipe_key,
                        instruksi.nama_pegawai AS pemberi_instruksi_nama,
                        COALESCE(soaprj_t.is_deleted, false) AS is_deleted_soap,
                        COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) AS last_modified_by,
                        peg_updated.nama_pegawai AS pegawai_update_nama,
                        pendaftaran_t.status_periksa,
                        soaprj_t.created_date
                       FROM ((((((((((((((((((((pasienkirimkeunitlain_t
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
                         LEFT JOIN pegawai_m instruksi ON ((soaprj_t.pemberi_instruksi_id = instruksi.pegawai_id)))
                         LEFT JOIN loginpemakai_k log_updated ON ((COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) = log_updated.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_updated ON ((log_updated.pegawai_id = peg_updated.pegawai_id)))
                    UNION ALL
                     SELECT \'TINDAKAN\'::text AS jenis,
                        \'SOAP FISIOTERAPI\'::text AS grouping_tipe,
                        2 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        ruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        COALESCE(peg_soap.pegawai_id, pegawai_m.pegawai_id) AS pegawai_id,
                        COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS nama_pegawai,
                        kelompokpegawai_m.kelompokpegawai_nama,
                        pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
                        soapfisioterapi_t.subject,
                        soapfisioterapi_t.object,
                        soapfisioterapi_t.a_diag_utama,
                        soapfisioterapi_t.a_diag_penyerta,
                        soapfisioterapi_t.tgl_soapfisioterapi AS tgl_tindakan,
                        soapfisioterapi_t.instruksi,
                        soapfisioterapi_t.soapfisioterapi_id AS soaprj_id,
                        soapfisioterapi_t.tgl_soapfisioterapi AS tgl_soaprj,
                        soapfisioterapi_t.planning,
                        soapfisioterapi_t.catatan_dokter,
                        array_to_json(NULL::character varying[]) AS daftar_paket,
                        \'-\'::text AS catatan_dokterpengirim,
                        soapfisioterapi_t.is_deleted AS is_hapus,
                        NULL::character varying AS status,
                        NULL::boolean AS cyto_tindakan,
                        NULL::double precision AS qty,
                        NULL::character varying AS no_penunjang,
                        soapfisioterapi_t.instruksi AS verbal_instruksi,
                        NULL::integer AS pemberi_instruksi_id,
                        NULL::boolean AS is_verifikasi_verbal,
                        NULL::timestamp without time zone AS tgl_verif_verbal,
                        NULL::integer AS pegawai_verbal_id,
                        pendaftaran_t.pasien_id,
                        ruangan_m.ruangan_id AS ruangan_penunjang_id,
                        ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
                        instalasi_m.instalasi_id AS instalasi_penunjang_id,
                        instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
                        NULL::integer AS kelompoktindakan_id,
                        COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
                        COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
                        NULL::boolean AS is_bayar,
                        NULL::integer AS permintaankepenunjang_id,
                        NULL::integer AS obatalkespasien_id,
                        NULL::integer AS tindakanpelayanan_id,
                        NULL::integer AS pasienkirimkeunitlain_id,
                        NULL::text AS alasan_batal,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        NULL::timestamp without time zone AS tgl_batal,
                        spesialis_m.spesialis_id,
                        spesialis_m.spesialis_nama,
                        NULL::boolean AS is_cyto,
                        NULL::boolean AS is_concern,
                        NULL::integer AS instruksitindakan_id,
                        soapfisioterapi_t.is_deleted,
                        \'tindakanfisioterapi\'::text AS grouping_tipe_key,
                        instruksi.nama_pegawai AS pemberi_instruksi_nama,
                        COALESCE(soapfisioterapi_t.is_deleted, false) AS is_deleted_soap,
                        COALESCE(soapfisioterapi_t.last_modified_by, soapfisioterapi_t.created_by) AS last_modified_by,
                        peg_updated.nama_pegawai AS pegawai_update_nama,
                        pendaftaran_t.status_periksa,
                        soapfisioterapi_t.created_date
                       FROM ((((((((((((((pendaftaran_t
                         LEFT JOIN soapfisioterapi_t ON (((pendaftaran_t.pendaftaran_id = soapfisioterapi_t.pendaftaran_id) AND (soapfisioterapi_t.is_deleted = false))))
                         LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                         LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         LEFT JOIN pegawai_m peg_soap ON ((soapfisioterapi_t.terapis_id = peg_soap.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
                         LEFT JOIN loginpemakai_k leg_deleted ON ((soapfisioterapi_t.deleted_by = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                         LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
                         LEFT JOIN pegawai_m instruksi ON ((soapfisioterapi_t.terapis_id = instruksi.pegawai_id)))
                         LEFT JOIN loginpemakai_k log_updated ON ((COALESCE(soapfisioterapi_t.last_modified_by, soapfisioterapi_t.created_by) = log_updated.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_updated ON ((log_updated.pegawai_id = peg_updated.pegawai_id)))
                      WHERE (ruangan_m.ruangan_id = 935)
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
                        spesialis_m.spesialis_nama,
                        false AS is_cyto,
                        false AS is_concern,
                        NULL::integer AS instruksitindakan_id,
                        soaprj_t.is_deleted,
                        \'verbalorder\'::text AS grouping_tipe_key,
                        instruksi.nama_pegawai AS pemberi_instruksi_nama,
                        COALESCE(soaprj_t.is_deleted, false) AS is_deleted_soap,
                        COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) AS last_modified_by,
                        peg_updated.nama_pegawai AS pegawai_update_nama,
                        pendaftaran_t.status_periksa,
                        soaprj_t.created_date
                       FROM (((((((((((pendaftaran_t
                         JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE(soaprj_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id)))
                         JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
                         LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
                         LEFT JOIN pegawai_m instruksi ON ((soaprj_t.pemberi_instruksi_id = instruksi.pegawai_id)))
                         LEFT JOIN loginpemakai_k log_updated ON ((COALESCE(soaprj_t.last_modified_by, soaprj_t.created_by) = log_updated.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_updated ON ((log_updated.pegawai_id = peg_updated.pegawai_id)))
                      WHERE ((soaprj_t.instruksi IS NOT NULL) AND (soaprj_t.pemberi_instruksi_id IS NOT NULL))) t1
              ORDER BY t1.pendaftaran_id DESC;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220315_094632_migrate_DHC92_view_soaprj_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220315_094632_migrate_DHC92_view_soaprj_v cannot be reverted.\n";

        return false;
    }
    */
}
