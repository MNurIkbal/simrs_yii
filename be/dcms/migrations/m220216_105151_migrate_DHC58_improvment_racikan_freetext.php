<?php

use yii\db\Migration;

/**
 * Class m220216_105151_migrate_DHC58_improvment_racikan_freetext
 */
class m220216_105151_migrate_DHC58_improvment_racikan_freetext extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE resepturracikan_t ADD IF NOT EXISTS type VARCHAR(30) DEFAULT \'OR\';
        ');

        $this->execute('
            ALTER TABLE konfigfarmasi_k ADD IF NOT EXISTS is_others BOOLEAN DEFAULT FALSE;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infoinstruksi_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoinstruksi_v" AS  SELECT \'TINDAKAN\'::text AS tipe_instruksi,
                instruksi_t.instruksi_id,
                instruksi_t.cppt_id, 
                instruksi_t.catatan_instruksi,
                instruksitindakan_t.instruksitindakan_id,
                instruksitindakan_t.tgl_tindakan AS tgl_instruksi,
                instruksitindakan_t.daftartindakan_id,
                concat(daftartindakan_m.daftartindakan_nama, \' - \',
                    CASE
                        WHEN (instruksitindakan_t.is_cyto = true) THEN \'Cyto - \'::text
                        ELSE NULL::text
                    END,
                    CASE
                        WHEN (instruksitindakan_t.is_concern = true) THEN \'Informed Consent - \'::text
                        ELSE NULL::text
                    END, instruksitindakan_t.qty) AS instruksi,
                NULL::character varying AS paket,
                NULL::text AS tindakan,
                instruksitindakan_t.qty,
                instruksitindakan_t.is_cyto,
                instruksitindakan_t.qty_sisa,
                instruksitindakan_t.dokterdpjp_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                instruksitindakan_t.status_implementasi,
                fgetnamalookup((instruksitindakan_t.status_implementasi)::integer) AS status,
                instruksi_t.is_deleted AS instruksi_deleted,
                instruksitindakan_t.is_deleted AS tindakan_deleted,
                daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
                    CASE
                        WHEN (instruksitindakan_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END AS ket_cyto,
                NULL::character varying AS ket_racik_nama,
                NULL::character varying AS ket_racik,
                cppt_t.pegawai_id AS cpptpegawai_id,
                cppt_t.is_verifikasi AS is_verifikasi_dpjp,
                array_to_json(NULL::character varying[]) AS daftar_paket,
                instruksi_t.tgl_instruksi AS tanggal_terapi,
                \'TINDAKANBMHP\'::text AS grouping_tipe,
                    CASE
                        WHEN ((instruksitindakan_t.status_implementasi)::text = (\'455\'::character varying)::text) THEN true
                        ELSE false
                    END AS is_telah_implementasi,
                ruangan_m.ruangan_nama AS ruangan_pertindakan,
                NULL::json AS bmhp_tindakandetail,
                instruksi_t.created_date AS tanggal_input,
                cppt_t.pendaftaran_id,
                cppt_t.pasienadmisi_id,
                daftartindakan_m.kelompoktindakan_id,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_bayar,
                NULL::integer AS pasienkirimkeunitlain_id,
                COALESCE(instruksitindakan_t.alasan_batal, tindakan_deleted.alasan_batal) AS alasan_batal,
                COALESCE(pegawai_hapus.pegawai_id, tindakan_deleted.pegawai_hapus_id) AS pegawai_hapus_id,
                COALESCE(pegawai_hapus.nama_pegawai, tindakan_deleted.pegawai_hapus_nama) AS pegawai_hapus_nama,
                COALESCE(instruksitindakan_t.deleted_date, tindakan_deleted.tgl_batal) AS tgl_batal,
                COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa
               FROM (((((((((instruksi_t
                 JOIN instruksitindakan_t ON ((instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id)))
                 JOIN daftartindakan_m ON ((instruksitindakan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pegawai_m dokter ON ((instruksitindakan_t.dokterdpjp_id = dokter.pegawai_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN ruangan_m ON ((instruksitindakan_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
                        tindakanpelayanan_t.instruksitindakan_id,
                        tindakanpelayanan_t.daftartindakan_id,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        tindakanpelayanan_t.deleted_date AS tgl_batal,
                        tindakanpelayanan_t.alasan_batal,
                        tindakanpelayanan_t.is_penatajasa
                       FROM ((tindakanpelayanan_t
                         LEFT JOIN loginpemakai_k leg_deleted ON ((tindakanpelayanan_t.deleted_by = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                      WHERE (tindakanpelayanan_t.is_deleted IS TRUE)) tindakan_deleted ON (((instruksitindakan_t.pendaftaran_id = tindakan_deleted.pendaftaran_id) AND (instruksitindakan_t.instruksitindakan_id = tindakan_deleted.instruksitindakan_id) AND (instruksitindakan_t.daftartindakan_id = tindakan_deleted.daftartindakan_id))))
                 LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
                        pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai
                       FROM (loginpemakai_k
                         JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))) pegawai_hapus ON ((instruksitindakan_t.deleted_by = pegawai_hapus.loginpemakai_id)))
            UNION ALL
             SELECT \'PAKET\'::text AS tipe_instruksi,
                instruksi_t.instruksi_id,
                instruksi_t.cppt_id,
                instruksi_t.catatan_instruksi,
                instruksitindakan_t.instruksitindakan_id,
                instruksitindakan_t.tgl_tindakan AS tgl_instruksi,
                instruksitindakan_t.tipepaket_id AS daftartindakan_id,
                concat(tipepaket_m.tipepaket_nama, \' - \',
                    CASE
                        WHEN (instruksitindakan_t.is_cyto = true) THEN \'Cyto - \'::text
                        ELSE NULL::text
                    END, \' - \',
                    CASE
                        WHEN (instruksitindakan_t.is_concern = true) THEN \'Informed Consent - \'::text
                        ELSE NULL::text
                    END, (\' - \'::text || instruksitindakan_t.qty)) AS instruksi,
                tipepaket_m.tipepaket_nama AS paket,
                NULL::text AS tindakan,
                instruksitindakan_t.qty,
                instruksitindakan_t.is_cyto,
                instruksitindakan_t.qty_sisa,
                instruksitindakan_t.dokterdpjp_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                instruksitindakan_t.status_implementasi,
                fgetnamalookup((instruksitindakan_t.status_implementasi)::integer) AS status,
                instruksi_t.is_deleted AS instruksi_deleted,
                instruksitindakan_t.is_deleted AS tindakan_deleted,
                tipepaket_m.tipepaket_nama AS tindakaninstruksi_nama,
                    CASE
                        WHEN (instruksitindakan_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END AS ket_cyto,
                NULL::character varying AS ket_racik_nama,
                NULL::character varying AS ket_racik,
                cppt_t.pegawai_id AS cpptpegawai_id,
                cppt_t.is_verifikasi AS is_verifikasi_dpjp,
                array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
                       FROM (paketpelayanan_mp
                         LEFT JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                      WHERE (paketpelayanan_mp.tipepaket_id = instruksitindakan_t.tipepaket_id))) AS daftar_paket,
                instruksi_t.tgl_instruksi AS tanggal_terapi,
                \'TINDAKANBMHP\'::text AS grouping_tipe,
                    CASE
                        WHEN ((instruksitindakan_t.status_implementasi)::text = (\'455\'::character varying)::text) THEN true
                        ELSE false
                    END AS is_telah_implementasi,
                ruangan_m.ruangan_nama AS ruangan_pertindakan,
                NULL::json AS bmhp_tindakandetail,
                instruksi_t.created_date AS tanggal_input,
                cppt_t.pendaftaran_id,
                cppt_t.pasienadmisi_id,
                NULL::integer AS kelompoktindakan_id,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_bayar,
                NULL::integer AS pasienkirimkeunitlain_id,
                COALESCE(instruksitindakan_t.alasan_batal, tindakan_deleted.alasan_batal) AS alasan_batal,
                COALESCE(pegawai_hapus.pegawai_id, tindakan_deleted.pegawai_hapus_id) AS pegawai_hapus_id,
                COALESCE(pegawai_hapus.nama_pegawai, tindakan_deleted.pegawai_hapus_nama) AS pegawai_hapus_nama,
                COALESCE(instruksitindakan_t.deleted_date, tindakan_deleted.tgl_batal) AS tgl_batal,
                COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa
               FROM (((((((((instruksi_t
                 JOIN instruksitindakan_t ON ((instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id)))
                 JOIN tipepaket_m ON ((instruksitindakan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN pegawai_m dokter ON ((instruksitindakan_t.dokterdpjp_id = dokter.pegawai_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN ruangan_m ON ((instruksitindakan_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
                        tindakanpelayanan_t.instruksitindakan_id,
                        tindakanpelayanan_t.tipepaket_id,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        tindakanpelayanan_t.deleted_date AS tgl_batal,
                        tindakanpelayanan_t.alasan_batal,
                        tindakanpelayanan_t.is_penatajasa
                       FROM ((tindakanpelayanan_t
                         LEFT JOIN loginpemakai_k leg_deleted ON ((tindakanpelayanan_t.deleted_by = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                      WHERE (tindakanpelayanan_t.is_deleted IS TRUE)) tindakan_deleted ON (((instruksitindakan_t.pendaftaran_id = tindakan_deleted.pendaftaran_id) AND (instruksitindakan_t.instruksitindakan_id = tindakan_deleted.instruksitindakan_id) AND (instruksitindakan_t.tipepaket_id = tindakan_deleted.tipepaket_id))))
                 LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
                        pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai
                       FROM (loginpemakai_k
                         JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))) pegawai_hapus ON ((instruksitindakan_t.deleted_by = pegawai_hapus.loginpemakai_id)))
            UNION ALL
             SELECT \'BMHP\'::text AS tipe_instruksi,
                instruksi_t.instruksi_id,
                instruksi_t.cppt_id,
                instruksi_t.catatan_instruksi,
                instruksitindakanbmhp_t.instruksitindakanbmhp_id AS instruksitindakan_id,
                instruksitindakanbmhp_t.tgl_pelayanan AS tgl_instruksi,
                instruksitindakanbmhp_t.obatalkes_id AS daftartindakan_id,
                concat(obatalkes_m.obatalkes_nama, (\'-\'::text || instruksitindakanbmhp_t.qty)) AS instruksi,
                tipepaket_m.tipepaket_nama AS paket,
                daftartindakan_m.daftartindakan_nama AS tindakan,
                instruksitindakanbmhp_t.qty,
                NULL::boolean AS is_cyto,
                instruksitindakanbmhp_t.qty_sisa,
                instruksitindakanbmhp_t.dokter_id,
                dokter.nama_pegawai AS dokter,
                instruksitindakanbmhp_t.status_implementasi,
                fgetnamalookup((instruksitindakanbmhp_t.status_implementasi)::integer) AS status,
                instruksi_t.is_deleted AS instruksi_deleted,
                instruksitindakanbmhp_t.is_deleted AS tindakan_deleted,
                obatalkes_m.obatalkes_nama AS tindakaninstruksi_nama,
                NULL::text AS ket_cyto,
                NULL::character varying AS ket_racik_nama,
                NULL::character varying AS ket_racik,
                cppt_t.pegawai_id AS cpptpegawai_id,
                cppt_t.is_verifikasi AS is_verifikasi_dpjp,
                array_to_json(NULL::character varying[]) AS daftar_paket,
                instruksi_t.tgl_instruksi AS tanggal_terapi,
                \'TINDAKANBMHP\'::text AS grouping_tipe,
                    CASE
                        WHEN ((instruksitindakanbmhp_t.status_implementasi)::text = (\'455\'::character varying)::text) THEN true
                        ELSE false
                    END AS is_telah_implementasi,
                ruangan_m.ruangan_nama AS ruangan_pertindakan,
                ( SELECT row_to_json(json_bmhp.*) AS row_to_json
                       FROM ( SELECT inst.instruksitindakan_id,
                                inst.dokterdpjp_id,
                                inst.daftartindakan_id,
                                dokterbmhp.nama_pegawai,
                                daftartindakanbmhp.daftartindakan_nama
                               FROM ((instruksitindakan_t inst
                                 LEFT JOIN pegawai_m dokterbmhp ON ((dokterbmhp.pegawai_id = inst.dokterdpjp_id)))
                                 LEFT JOIN daftartindakan_m daftartindakanbmhp ON ((daftartindakanbmhp.daftartindakan_id = inst.daftartindakan_id)))
                              WHERE (inst.instruksitindakan_id = instruksitindakanbmhp_t.instruksitindakan_id)) json_bmhp) AS bmhp_tindakandetail,
                instruksi_t.created_date AS tanggal_input,
                cppt_t.pendaftaran_id,
                cppt_t.pasienadmisi_id,
                daftartindakan_m.kelompoktindakan_id,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_bayar,
                NULL::integer AS pasienkirimkeunitlain_id,
                COALESCE(instruksitindakanbmhp_t.alasan_batal, obat_deleted.alasan_batal) AS alasan_batal,
                COALESCE(pegawai_hapus.pegawai_id, obat_deleted.pegawai_hapus_id) AS pegawai_hapus_id,
                COALESCE(pegawai_hapus.nama_pegawai, obat_deleted.pegawai_hapus_nama) AS pegawai_hapus_nama,
                COALESCE(instruksitindakanbmhp_t.deleted_date, obat_deleted.tgl_batal) AS tgl_batal,
                COALESCE(obat_deleted.is_penatajasa, false) AS is_penatajasa
               FROM ((((((((((((instruksi_t
                 JOIN instruksitindakanbmhp_t ON ((instruksi_t.instruksi_id = instruksitindakanbmhp_t.instruksi_id)))
                 LEFT JOIN instruksitindakan_t ON ((instruksitindakanbmhp_t.instruksitindakan_id = instruksitindakan_t.instruksitindakan_id)))
                 JOIN obatalkes_m ON ((instruksitindakanbmhp_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                 LEFT JOIN daftartindakan_m ON ((instruksitindakanbmhp_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN tipepaket_m ON ((instruksitindakanbmhp_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 LEFT JOIN pegawai_m dokter ON ((instruksitindakanbmhp_t.dokter_id = dokter.pegawai_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN ruangan_m ON ((instruksitindakanbmhp_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN ( SELECT obatalkespasien_t.instruksitindakanbmhp_id,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        obatalkespasien_t.deleted_date AS tgl_batal,
                        obatalkespasien_t.alasan_batal,
                        obatalkespasien_t.is_penatajasa
                       FROM ((obatalkespasien_t
                         LEFT JOIN loginpemakai_k leg_deleted ON ((obatalkespasien_t.deleted_by = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                      WHERE (obatalkespasien_t.is_deleted IS TRUE)) obat_deleted ON ((instruksitindakanbmhp_t.instruksitindakanbmhp_id = obat_deleted.instruksitindakanbmhp_id)))
                 LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
                        pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai
                       FROM (loginpemakai_k
                         JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))) pegawai_hapus ON ((instruksitindakanbmhp_t.deleted_by = pegawai_hapus.loginpemakai_id)))
            UNION ALL
             SELECT
                    CASE
                        WHEN ((racikan_m.racikan_singkatan)::text = \'OR\'::text) THEN \'RACIKAN\'::text
                        ELSE \'NONRACIKAN\'::text
                    END AS tipe_instruksi,
                instruksi_t.instruksi_id,
                instruksi_t.cppt_id,
                instruksi_t.catatan_instruksi,
                resepturdetail_t.resepturdetail_id AS instruksitindakan_id,
                    CASE
                        WHEN (resepturdetail_t.tgl_resepturdetail IS NULL) THEN resepturdetail_t.created_date
                        ELSE resepturdetail_t.tgl_resepturdetail
                    END AS tgl_instruksi,
                resepturdetail_t.obatalkes_id AS daftartindakan_id,
                concat(obatalkes_m.obatalkes_nama, (\' - \'::text || resepturdetail_t.qty_reseptur)) AS instruksi,
                NULL::character varying AS paket,
                NULL::text AS tindakan,
                resepturdetail_t.qty_reseptur AS qty,
                NULL::boolean AS is_cyto,
                NULL::integer AS qty_sisa,
                reseptur_t.pegawai_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                (reseptur_t.status_reseptur)::character varying AS status_implementasi,
                fgetnamalookup(reseptur_t.status_reseptur) AS status,
                instruksi_t.is_deleted AS instruksi_deleted,
                resepturdetail_t.is_deleted AS tindakan_deleted,
                obatalkes_m.obatalkes_nama AS tindakaninstruksi_nama,
                NULL::text AS ket_cyto,
                racikan_m.racikan_nama AS ket_racik_nama,
                racikan_m.racikan_singkatan AS ket_racik,
                cppt_t.pegawai_id AS cpptpegawai_id,
                cppt_t.is_verifikasi AS is_verifikasi_dpjp,
                array_to_json(NULL::character varying[]) AS daftar_paket,
                instruksi_t.tgl_instruksi AS tanggal_terapi,
                \'RESEPTUR\'::text AS grouping_tipe,
                    CASE
                        WHEN (((reseptur_t.status_reseptur)::character varying)::text = (\'347\'::character varying)::text) THEN true
                        ELSE false
                    END AS is_telah_implementasi,
                ruangan_m.ruangan_nama AS ruangan_pertindakan,
                NULL::json AS bmhp_tindakandetail,
                instruksi_t.created_date AS tanggal_input,
                cppt_t.pendaftaran_id,
                cppt_t.pasienadmisi_id,
                NULL::integer AS kelompoktindakan_id,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_bayar,
                NULL::integer AS pasienkirimkeunitlain_id,
                obat_deleted.alasan_batal,
                obat_deleted.pegawai_hapus_id,
                obat_deleted.pegawai_hapus_nama,
                obat_deleted.tgl_batal,
                COALESCE(obat_deleted.is_penatajasa, false) AS is_penatajasa
               FROM ((((((((((instruksi_t
                 JOIN reseptur_t ON ((instruksi_t.instruksi_id = reseptur_t.instruksi_id)))
                 JOIN resepturdetail_t ON ((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id)))
                 JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                 JOIN pegawai_m dokter ON ((reseptur_t.pegawai_id = dokter.pegawai_id)))
                 LEFT JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN ruangan_m ON ((reseptur_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN ( SELECT obatalkespasien_t.resepturdetail_id,
                        peg_deleted.pegawai_id AS pegawai_hapus_id,
                        peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                        obatalkespasien_t.deleted_date AS tgl_batal,
                        obatalkespasien_t.alasan_batal,
                        obatalkespasien_t.is_penatajasa
                       FROM ((obatalkespasien_t
                         LEFT JOIN loginpemakai_k leg_deleted ON ((obatalkespasien_t.deleted_by = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                      WHERE (obatalkespasien_t.is_deleted IS TRUE)) obat_deleted ON ((resepturdetail_t.resepturdetail_id = obat_deleted.resepturdetail_id)))
            UNION ALL
             SELECT
                    CASE
                        WHEN ((COALESCE(resepturracikan_t.type, \'OR\'::character varying))::text = \'OR\'::text) THEN \'RACIKAN\'::text
                        ELSE \'NONRACIKAN\'::text
                    END AS tipe_instruksi,
                instruksi_t.instruksi_id,
                instruksi_t.cppt_id,
                instruksi_t.catatan_instruksi,
                resepturracikan_t.resepturracikan_id AS instruksitindakan_id,
                resepturracikan_t.created_date AS tgl_instruksi,
                NULL::integer AS daftartindakan_id,
                resepturracikan_t.racikan AS instruksi,
                NULL::character varying AS paket,
                NULL::text AS tindakan,
                NULL::double precision AS qty,
                NULL::boolean AS is_cyto,
                NULL::integer AS qty_sisa,
                reseptur_t.pegawai_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                (reseptur_t.status_reseptur)::character varying AS status_implementasi,
                fgetnamalookup(reseptur_t.status_reseptur) AS status,
                instruksi_t.is_deleted AS instruksi_deleted,
                resepturracikan_t.is_deleted AS tindakan_deleted,
                resepturracikan_t.racikan AS tindakaninstruksi_nama,
                NULL::text AS ket_cyto,
                    CASE
                        WHEN ((COALESCE(resepturracikan_t.type, \'OR\'::character varying))::text = \'OR\'::text) THEN \'Racikan\'::text
                        WHEN ((COALESCE(resepturracikan_t.type, \'OR\'::character varying))::text = \'OT\'::text) THEN \'Other\'::text
                        ELSE \'Non Racikan\'::text
                    END AS ket_racik_nama,
                COALESCE(resepturracikan_t.type, \'OR\'::character varying) AS ket_racik,
                cppt_t.pegawai_id AS cpptpegawai_id,
                cppt_t.is_verifikasi AS is_verifikasi_dpjp,
                array_to_json(NULL::character varying[]) AS daftar_paket,
                instruksi_t.tgl_instruksi AS tanggal_terapi,
                \'RESEPTUR\'::text AS grouping_tipe,
                    CASE
                        WHEN (((reseptur_t.status_reseptur)::character varying)::text = (\'347\'::character varying)::text) THEN true
                        ELSE false
                    END AS is_telah_implementasi,
                ruangan_m.ruangan_nama AS ruangan_pertindakan,
                NULL::json AS bmhp_tindakandetail,
                instruksi_t.created_date AS tanggal_input,
                cppt_t.pendaftaran_id,
                cppt_t.pasienadmisi_id,
                NULL::integer AS kelompoktindakan_id,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_bayar,
                NULL::integer AS pasienkirimkeunitlain_id,
                NULL::text AS alasan_batal,
                NULL::integer AS pegawai_hapus_id,
                NULL::text AS pegawai_hapus_nama,
                NULL::timestamp without time zone AS tgl_batal,
                false AS is_penatajasa
               FROM (((((((instruksi_t
                 JOIN reseptur_t ON ((instruksi_t.instruksi_id = reseptur_t.instruksi_id)))
                 JOIN resepturracikan_t ON ((reseptur_t.reseptur_id = resepturracikan_t.reseptur_id)))
                 JOIN pegawai_m dokter ON ((reseptur_t.pegawai_id = dokter.pegawai_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN ruangan_m ON ((reseptur_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            UNION ALL
             SELECT \'LAB_TINDAKAN\'::text AS tipe_instruksi,
                instruksi_t.instruksi_id,
                instruksi_t.cppt_id,
                instruksi_t.catatan_instruksi,
                permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
                permintaankepenunjang_t.daftartindakan_id,
                concat(daftartindakan_m.daftartindakan_nama, \'-\',
                    CASE
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto - \'::text
                        ELSE NULL::text
                    END, permintaankepenunjang_t.qtypermintaan) AS instruksi,
                NULL::character varying AS paket,
                NULL::text AS tindakan,
                permintaankepenunjang_t.qtypermintaan AS qty,
                permintaankepenunjang_t.is_cyto,
                NULL::integer AS qty_sisa,
                pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                    CASE
                        WHEN (hasil_wynacom.no_masukpenunjang IS NOT NULL) THEN \'475\'::character varying
                        WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
                        ELSE pasienmasukpenunjang_t.status_periksa
                    END AS status_implementasi,
                    CASE
                        WHEN (hasil_wynacom.no_masukpenunjang IS NOT NULL) THEN \'SELESAI\'::character varying
                        WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer)
                        ELSE fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer)
                    END AS status,
                instruksi_t.is_deleted AS instruksi_deleted,
                permintaankepenunjang_t.is_deleted AS tindakan_deleted,
                daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
                    CASE
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END AS ket_cyto,
                NULL::character varying AS ket_racik_nama,
                NULL::character varying AS ket_racik,
                cppt_t.pegawai_id AS cpptpegawai_id,
                cppt_t.is_verifikasi AS is_verifikasi_dpjp,
                array_to_json(NULL::character varying[]) AS daftar_paket,
                instruksi_t.tgl_instruksi AS tanggal_terapi,
                \'PENUNJANG\'::text AS grouping_tipe,
                    CASE
                        WHEN ((pasienkirimkeunitlain_t.status_penunjang)::text = (\'471\'::character varying)::text) THEN true
                        ELSE false
                    END AS is_telah_implementasi,
                ruangan_m.ruangan_nama AS ruangan_pertindakan,
                NULL::json AS bmhp_tindakandetail,
                instruksi_t.created_date AS tanggal_input,
                cppt_t.pendaftaran_id,
                cppt_t.pasienadmisi_id,
                daftartindakan_m.kelompoktindakan_id,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_bayar,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                COALESCE(tindakan_deleted.alasan_batal, batal_order.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
                peg_deleted.pegawai_id AS pegawai_hapus_id,
                peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                COALESCE(batal_order.tgl_batal, tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal,
                COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa
               FROM ((((((((((((((instruksi_t
                 JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
                 LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.daftartindakan_id,
                        tindakanpelayanan_t.deleted_date AS tgl_batal,
                        tindakanpelayanan_t.alasan_batal,
                        tindakanpelayanan_t.deleted_by,
                        tindakanpelayanan_t.is_penatajasa
                       FROM tindakanpelayanan_t
                      WHERE (tindakanpelayanan_t.is_deleted IS TRUE)) tindakan_deleted ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id) AND (permintaankepenunjang_t.daftartindakan_id = tindakan_deleted.daftartindakan_id))))
                 LEFT JOIN ( SELECT batalorderpenunjang_t.pasienkirimkeunitlain_id,
                        batalorderpenunjang_t.tgl_batalorder AS tgl_batal,
                        batalorderpenunjang_t.alasan AS alasan_batal,
                        batalorderpenunjang_t.created_by,
                        batalorderpenunjang_t.is_active,
                        batalorderpenunjang_t.peg_menyetujui_id
                       FROM batalorderpenunjang_t) batal_order ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id)))
                 LEFT JOIN loginpemakai_k leg_deleted ON ((COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_deleted ON ((COALESCE(batal_order.peg_menyetujui_id, leg_deleted.pegawai_id) = peg_deleted.pegawai_id)))
                 LEFT JOIN ( SELECT hasilpemeriksaanlab_wynacom_t.his_reg_no AS no_masukpenunjang
                       FROM hasilpemeriksaanlab_wynacom_t
                      WHERE (hasilpemeriksaanlab_wynacom_t.is_deleted = false)
                      GROUP BY hasilpemeriksaanlab_wynacom_t.his_reg_no) hasil_wynacom ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil_wynacom.no_masukpenunjang)::text)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 4)
            UNION ALL
             SELECT \'LAB_PAKET\'::text AS tipe_instruksi,
                instruksi_t.instruksi_id,
                instruksi_t.cppt_id,
                instruksi_t.catatan_instruksi,
                permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
                permintaankepenunjang_t.tipepaket_id AS daftartindakan_id,
                concat(tipepaket_m.tipepaket_nama, \'-\',
                    CASE
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto - \'::text
                        ELSE NULL::text
                    END, permintaankepenunjang_t.qtypermintaan) AS instruksi,
                tipepaket_m.tipepaket_nama AS paket,
                NULL::text AS tindakan,
                permintaankepenunjang_t.qtypermintaan AS qty,
                permintaankepenunjang_t.is_cyto,
                NULL::integer AS qty_sisa,
                pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                    CASE
                        WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
                        ELSE pasienmasukpenunjang_t.status_periksa
                    END AS status_implementasi,
                    CASE
                        WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer)
                        ELSE fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer)
                    END AS status,
                instruksi_t.is_deleted AS instruksi_deleted,
                permintaankepenunjang_t.is_deleted AS tindakan_deleted,
                tipepaket_m.tipepaket_nama AS tindakaninstruksi_nama,
                    CASE
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END AS ket_cyto,
                NULL::character varying AS ket_racik_nama,
                NULL::character varying AS ket_racik,
                cppt_t.pegawai_id AS cpptpegawai_id,
                cppt_t.is_verifikasi AS is_verifikasi_dpjp,
                array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
                       FROM (paketpelayanan_mp
                         LEFT JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                      WHERE (paketpelayanan_mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id))) AS daftar_paket,
                instruksi_t.tgl_instruksi AS tanggal_terapi,
                \'PENUNJANG\'::text AS grouping_tipe,
                    CASE
                        WHEN ((pasienkirimkeunitlain_t.status_penunjang)::text = (\'471\'::character varying)::text) THEN true
                        ELSE false
                    END AS is_telah_implementasi,
                ruangan_m.ruangan_nama AS ruangan_pertindakan,
                NULL::json AS bmhp_tindakandetail,
                instruksi_t.created_date AS tanggal_input,
                cppt_t.pendaftaran_id,
                cppt_t.pasienadmisi_id,
                NULL::integer AS kelompoktindakan_id,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_bayar,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                COALESCE(tindakan_deleted.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
                peg_deleted.pegawai_id AS pegawai_hapus_id,
                peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                COALESCE(tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal,
                COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa
               FROM ((((((((((((instruksi_t
                 JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
                 LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.tipepaket_id,
                        tindakanpelayanan_t.deleted_date AS tgl_batal,
                        tindakanpelayanan_t.alasan_batal,
                        tindakanpelayanan_t.deleted_by,
                        tindakanpelayanan_t.is_penatajasa
                       FROM tindakanpelayanan_t
                      WHERE (tindakanpelayanan_t.is_deleted IS TRUE)) tindakan_deleted ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id) AND (permintaankepenunjang_t.tipepaket_id = tindakan_deleted.tipepaket_id))))
                 LEFT JOIN loginpemakai_k leg_deleted ON ((COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 4)
            UNION ALL
             SELECT \'RAD_TINDAKAN\'::text AS tipe_instruksi,
                instruksi_t.instruksi_id,
                instruksi_t.cppt_id,
                instruksi_t.catatan_instruksi,
                permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
                permintaankepenunjang_t.daftartindakan_id,
                concat(daftartindakan_m.daftartindakan_nama, \'-\',
                    CASE
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto - \'::text
                        ELSE NULL::text
                    END, permintaankepenunjang_t.qtypermintaan) AS instruksi,
                NULL::character varying AS paket,
                NULL::text AS tindakan,
                permintaankepenunjang_t.qtypermintaan AS qty,
                permintaankepenunjang_t.is_cyto,
                NULL::integer AS qty_sisa,
                pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                    CASE
                        WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
                        ELSE pasienmasukpenunjang_t.status_periksa
                    END AS status_implementasi,
                    CASE
                        WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer)
                        ELSE fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer)
                    END AS status,
                instruksi_t.is_deleted AS instruksi_deleted,
                COALESCE(batal_order.is_active, permintaankepenunjang_t.is_deleted) AS tindakan_deleted,
                daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
                    CASE
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END AS ket_cyto,
                NULL::character varying AS ket_racik_nama,
                NULL::character varying AS ket_racik,
                cppt_t.pegawai_id AS cpptpegawai_id,
                cppt_t.is_verifikasi AS is_verifikasi_dpjp,
                array_to_json(NULL::character varying[]) AS daftar_paket,
                instruksi_t.tgl_instruksi AS tanggal_terapi,
                \'PENUNJANG\'::text AS grouping_tipe,
                    CASE
                        WHEN ((pasienkirimkeunitlain_t.status_penunjang)::text = (\'471\'::character varying)::text) THEN true
                        ELSE false
                    END AS is_telah_implementasi,
                ruangan_m.ruangan_nama AS ruangan_pertindakan,
                NULL::json AS bmhp_tindakandetail,
                instruksi_t.created_date AS tanggal_input,
                cppt_t.pendaftaran_id,
                cppt_t.pasienadmisi_id,
                daftartindakan_m.kelompoktindakan_id,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_bayar,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                COALESCE(tindakan_deleted.alasan_batal, batal_order.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
                peg_deleted.pegawai_id AS pegawai_hapus_id,
                peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                COALESCE(batal_order.tgl_batal, tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal,
                COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa
               FROM (((((((((((((instruksi_t
                 JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
                 LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.daftartindakan_id,
                        tindakanpelayanan_t.deleted_date AS tgl_batal,
                        tindakanpelayanan_t.alasan_batal,
                        tindakanpelayanan_t.deleted_by,
                        tindakanpelayanan_t.is_penatajasa
                       FROM tindakanpelayanan_t
                      WHERE (tindakanpelayanan_t.is_deleted IS TRUE)) tindakan_deleted ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id) AND (permintaankepenunjang_t.daftartindakan_id = tindakan_deleted.daftartindakan_id))))
                 LEFT JOIN ( SELECT batalorderpenunjang_t.pasienkirimkeunitlain_id,
                        batalorderpenunjang_t.tgl_batalorder AS tgl_batal,
                        batalorderpenunjang_t.alasan AS alasan_batal,
                        batalorderpenunjang_t.created_by,
                        batalorderpenunjang_t.is_active,
                        batalorderpenunjang_t.peg_menyetujui_id
                       FROM batalorderpenunjang_t) batal_order ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id)))
                 LEFT JOIN loginpemakai_k leg_deleted ON ((COALESCE(batal_order.created_by, tindakan_deleted.deleted_by, batal_order.created_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_deleted ON ((COALESCE(batal_order.peg_menyetujui_id, leg_deleted.pegawai_id) = peg_deleted.pegawai_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 5)
            UNION ALL
             SELECT \'RAD_PAKET\'::text AS tipe_instruksi,
                instruksi_t.instruksi_id,
                instruksi_t.cppt_id,
                instruksi_t.catatan_instruksi,
                permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
                permintaankepenunjang_t.tipepaket_id AS daftartindakan_id,
                concat(tipepaket_m.tipepaket_nama, \'-\',
                    CASE
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto - \'::text
                        ELSE NULL::text
                    END, permintaankepenunjang_t.qtypermintaan) AS instruksi,
                tipepaket_m.tipepaket_nama AS paket,
                NULL::text AS tindakan,
                permintaankepenunjang_t.qtypermintaan AS qty,
                permintaankepenunjang_t.is_cyto,
                NULL::integer AS qty_sisa,
                pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                    CASE
                        WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
                        ELSE pasienmasukpenunjang_t.status_periksa
                    END AS status_implementasi,
                    CASE
                        WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer)
                        ELSE fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer)
                    END AS status,
                instruksi_t.is_deleted AS instruksi_deleted,
                permintaankepenunjang_t.is_deleted AS tindakan_deleted,
                tipepaket_m.tipepaket_nama AS tindakaninstruksi_nama,
                    CASE
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END AS ket_cyto,
                NULL::character varying AS ket_racik_nama,
                NULL::character varying AS ket_racik,
                cppt_t.pegawai_id AS cpptpegawai_id,
                cppt_t.is_verifikasi AS is_verifikasi_dpjp,
                array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
                       FROM (paketpelayanan_mp
                         LEFT JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                      WHERE (paketpelayanan_mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id))) AS daftar_paket,
                instruksi_t.tgl_instruksi AS tanggal_terapi,
                \'PENUNJANG\'::text AS grouping_tipe,
                    CASE
                        WHEN ((pasienkirimkeunitlain_t.status_penunjang)::text = (\'471\'::character varying)::text) THEN true
                        ELSE false
                    END AS is_telah_implementasi,
                ruangan_m.ruangan_nama AS ruangan_pertindakan,
                NULL::json AS bmhp_tindakandetail,
                instruksi_t.created_date AS tanggal_input,
                cppt_t.pendaftaran_id,
                cppt_t.pasienadmisi_id,
                NULL::integer AS kelompoktindakan_id,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_bayar,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                COALESCE(tindakan_deleted.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
                peg_deleted.pegawai_id AS pegawai_hapus_id,
                peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                COALESCE(tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal,
                COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa
               FROM ((((((((((((instruksi_t
                 JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
                 LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.tipepaket_id,
                        tindakanpelayanan_t.deleted_date AS tgl_batal,
                        tindakanpelayanan_t.alasan_batal,
                        tindakanpelayanan_t.deleted_by,
                        tindakanpelayanan_t.is_penatajasa
                       FROM tindakanpelayanan_t
                      WHERE (tindakanpelayanan_t.is_deleted IS TRUE)) tindakan_deleted ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id) AND (permintaankepenunjang_t.tipepaket_id = tindakan_deleted.tipepaket_id))))
                 LEFT JOIN loginpemakai_k leg_deleted ON ((COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 5)
            UNION ALL
             SELECT \'BED_TINDAKAN\'::text AS tipe_instruksi,
                instruksi_t.instruksi_id,
                instruksi_t.cppt_id,
                instruksi_t.catatan_instruksi,
                permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
                COALESCE(permintaankepenunjang_t.tglpermintaankepenunjang, pasienkirimkeunitlain_t.tgl_kirimpasien) AS tgl_instruksi,
                permintaankepenunjang_t.daftartindakan_id,
                concat(daftartindakan_m.daftartindakan_nama, \'-\',
                    CASE
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto - \'::text
                        ELSE NULL::text
                    END, permintaankepenunjang_t.qtypermintaan) AS instruksi,
                NULL::character varying AS paket,
                NULL::text AS tindakan,
                permintaankepenunjang_t.qtypermintaan AS qty,
                permintaankepenunjang_t.is_cyto,
                NULL::integer AS qty_sisa,
                pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                    CASE
                        WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
                        ELSE pasienmasukpenunjang_t.status_periksa
                    END AS status_implementasi,
                    CASE
                        WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer)
                        ELSE fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer)
                    END AS status,
                instruksi_t.is_deleted AS instruksi_deleted,
                permintaankepenunjang_t.is_deleted AS tindakan_deleted,
                daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
                    CASE
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END AS ket_cyto,
                NULL::character varying AS ket_racik_nama,
                NULL::character varying AS ket_racik,
                cppt_t.pegawai_id AS cpptpegawai_id,
                cppt_t.is_verifikasi AS is_verifikasi_dpjp,
                array_to_json(NULL::character varying[]) AS daftar_paket,
                instruksi_t.tgl_instruksi AS tanggal_terapi,
                \'PENUNJANG\'::text AS grouping_tipe,
                    CASE
                        WHEN ((pasienkirimkeunitlain_t.status_penunjang)::text = (\'471\'::character varying)::text) THEN true
                        ELSE false
                    END AS is_telah_implementasi,
                ruangan_m.ruangan_nama AS ruangan_pertindakan,
                NULL::json AS bmhp_tindakandetail,
                instruksi_t.created_date AS tanggal_input,
                cppt_t.pendaftaran_id,
                cppt_t.pasienadmisi_id,
                daftartindakan_m.kelompoktindakan_id,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                    CASE pendaftaran_t.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_bayar,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                COALESCE(tindakan_deleted.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
                peg_deleted.pegawai_id AS pegawai_hapus_id,
                peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                COALESCE(tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal,
                COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa
               FROM ((((((((((((instruksi_t
                 JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
                 LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        tindakanpelayanan_t.daftartindakan_id,
                        tindakanpelayanan_t.deleted_date AS tgl_batal,
                        tindakanpelayanan_t.alasan_batal,
                        tindakanpelayanan_t.deleted_by,
                        tindakanpelayanan_t.is_penatajasa
                       FROM tindakanpelayanan_t
                      WHERE (tindakanpelayanan_t.is_deleted IS TRUE)) tindakan_deleted ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id) AND (permintaankepenunjang_t.daftartindakan_id = tindakan_deleted.daftartindakan_id))))
                 LEFT JOIN loginpemakai_k leg_deleted ON ((COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 12)
            UNION ALL
             SELECT \'TINDAKAN\'::text AS tipe_instruksi,
                NULL::integer AS instruksi_id,
                NULL::integer AS cppt_id,
                tindakanpelayanan_t.keterangantindakan AS catatan_instruksi,
                tindakanpelayanan_t.tindakanpelayanan_id AS instruksitindakan_id,
                tindakanpelayanan_t.tgl_tindakan AS tgl_instruksi,
                tindakanpelayanan_t.daftartindakan_id,
                concat(daftartindakan_m.daftartindakan_nama, \' - \',
                    CASE
                        WHEN (tindakanpelayanan_t.cyto_tindakan = true) THEN \'Cyto - \'::text
                        ELSE NULL::text
                    END, tindakanpelayanan_t.qty_tindakan) AS instruksi,
                NULL::character varying AS paket,
                NULL::text AS tindakan,
                tindakanpelayanan_t.qty_tindakan AS qty,
                tindakanpelayanan_t.cyto_tindakan AS is_cyto,
                NULL::double precision AS qty_sisa,
                tindakanpelayanan_t.dokterpenanggungjawab_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                NULL::character varying AS status_implementasi,
                NULL::character varying AS status,
                tindakanpelayanan_t.is_deleted AS instruksi_deleted,
                tindakanpelayanan_t.is_deleted AS tindakan_deleted,
                daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
                    CASE
                        WHEN (tindakanpelayanan_t.cyto_tindakan = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END AS ket_cyto,
                NULL::character varying AS ket_racik_nama,
                NULL::character varying AS ket_racik,
                NULL::integer AS cpptpegawai_id,
                NULL::boolean AS is_verifikasi_dpjp,
                array_to_json(NULL::character varying[]) AS daftar_paket,
                tindakanpelayanan_t.tgl_tindakan AS tanggal_terapi,
                \'TINDAKANBMHP\'::text AS grouping_tipe,
                true AS is_telah_implementasi,
                ruangan_m.ruangan_nama AS ruangan_pertindakan,
                NULL::json AS bmhp_tindakandetail,
                tindakanpelayanan_t.created_date AS tanggal_input,
                tindakanpelayanan_t.pendaftaran_id,
                tindakanpelayanan_t.pasienadmisi_id,
                daftartindakan_m.kelompoktindakan_id,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                    CASE pendaftaran.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_bayar,
                NULL::integer AS pasienkirimkeunitlain_id,
                COALESCE(tindakanpelayanan_t.alasan_batal) AS alasan_batal,
                COALESCE(pegawai_hapus.pegawai_id) AS pegawai_hapus_id,
                COALESCE(pegawai_hapus.nama_pegawai) AS pegawai_hapus_nama,
                COALESCE(tindakanpelayanan_t.deleted_date) AS tgl_batal,
                tindakanpelayanan_t.is_penatajasa
               FROM ((((((tindakanpelayanan_t
                 JOIN ( SELECT pendaftaran_t.status_bayar,
                        pendaftaran_t.pendaftaran_id
                       FROM pendaftaran_t) pendaftaran ON ((tindakanpelayanan_t.pendaftaran_id = pendaftaran.pendaftaran_id)))
                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pegawai_m dokter ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id)))
                 JOIN ruangan_m ON ((tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
                        pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai
                       FROM (loginpemakai_k
                         JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))) pegawai_hapus ON ((tindakanpelayanan_t.deleted_by = pegawai_hapus.loginpemakai_id)))
              WHERE (tindakanpelayanan_t.is_penatajasa IS TRUE)
            UNION ALL
             SELECT \'BMHP\'::text AS tipe_instruksi,
                NULL::integer AS instruksi_id,
                NULL::integer AS cppt_id,
                obatalkespasien_t.keterangan AS catatan_instruksi,
                obatalkespasien_t.obatalkespasien_id AS instruksitindakan_id,
                obatalkespasien_t.tglpelayanan AS tgl_instruksi,
                obatalkespasien_t.obatalkes_id AS daftartindakan_id,
                concat(obatalkes_m.obatalkes_nama, (\'-\'::text || obatalkespasien_t.qty_oa)) AS instruksi,
                NULL::character varying AS paket,
                obatalkes_m.obatalkes_nama AS tindakan,
                obatalkespasien_t.qty_oa AS qty,
                NULL::boolean AS is_cyto,
                NULL::double precision AS qty_sisa,
                obatalkespasien_t.pegawai_id AS dokter_id,
                pegawai_m.nama_pegawai AS dokter,
                NULL::character varying AS status_implementasi,
                NULL::character varying AS status,
                obatalkespasien_t.is_deleted AS instruksi_deleted,
                obatalkespasien_t.is_deleted AS tindakan_deleted,
                obatalkes_m.obatalkes_nama AS tindakaninstruksi_nama,
                NULL::text AS ket_cyto,
                NULL::character varying AS ket_racik_nama,
                NULL::character varying AS ket_racik,
                obatalkespasien_t.pegawai_id AS cpptpegawai_id,
                NULL::boolean AS is_verifikasi_dpjp,
                array_to_json(NULL::character varying[]) AS daftar_paket,
                obatalkespasien_t.tglpelayanan AS tanggal_terapi,
                \'TINDAKANBMHP\'::text AS grouping_tipe,
                true AS is_telah_implementasi,
                ruangan_m.ruangan_nama AS ruangan_pertindakan,
                NULL::json AS bmhp_tindakandetail,
                obatalkespasien_t.created_date AS tanggal_input,
                obatalkespasien_t.pendaftaran_id,
                obatalkespasien_t.pasienadmisi_id,
                NULL::integer AS kelompoktindakan_id,
                instalasi_m.instalasi_id,
                instalasi_m.instalasi_nama,
                    CASE pendaftaran.status_bayar
                        WHEN 348 THEN true
                        ELSE false
                    END AS is_bayar,
                NULL::integer AS pasienkirimkeunitlain_id,
                obatalkespasien_t.alasan_batal,
                peg_deleted.pegawai_id AS pegawai_hapus_id,
                peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                obatalkespasien_t.deleted_date AS tgl_batal,
                obatalkespasien_t.is_penatajasa
               FROM (((((((obatalkespasien_t
                 JOIN ( SELECT pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.status_bayar
                       FROM pendaftaran_t) pendaftaran ON ((obatalkespasien_t.pendaftaran_id = pendaftaran.pendaftaran_id)))
                 JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                 JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 LEFT JOIN pegawai_m ON ((obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN loginpemakai_k leg_deleted ON ((obatalkespasien_t.deleted_by = leg_deleted.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
              WHERE (obatalkespasien_t.is_penatajasa IS TRUE);
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
                t1.spesialis_nama,
                t1.is_cyto,
                t1.is_concern,
                t1.is_deleted,
                t1.grouping_tipe_key,
                t1.pemberi_instruksi_nama
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
                        instruksi.nama_pegawai AS pemberi_instruksi_nama
                       FROM (((((((((((((((pendaftaran_t
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
                        instruksi.nama_pegawai AS pemberi_instruksi_nama
                       FROM (((((((((((((((pendaftaran_t
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
                        instruksi.nama_pegawai AS pemberi_instruksi_nama
                       FROM ((((((((((((((pendaftaran_t
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
                        instruksi.nama_pegawai AS pemberi_instruksi_nama
                       FROM (((((((((((((((((reseptur_t
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
                        instruksi.nama_pegawai AS pemberi_instruksi_nama
                       FROM ((((((((((((((((reseptur_t
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
                        instruksi.nama_pegawai AS pemberi_instruksi_nama
                       FROM ((((((((((((((((((pasienkirimkeunitlain_t
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
                        instruksi.nama_pegawai AS pemberi_instruksi_nama
                       FROM ((((((((((((((((((pasienkirimkeunitlain_t
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
                        instruksi.nama_pegawai AS pemberi_instruksi_nama
                       FROM (((((((((pendaftaran_t
                         JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                         LEFT JOIN pegawai_m ON ((COALESCE(soaprj_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id)))
                         JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                         LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
                         LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
                         LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
                         LEFT JOIN pegawai_m instruksi ON ((soaprj_t.pemberi_instruksi_id = instruksi.pegawai_id)))
                      WHERE ((soaprj_t.instruksi IS NOT NULL) AND (soaprj_t.pemberi_instruksi_id IS NOT NULL))) t1
              ORDER BY t1.pendaftaran_id DESC;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220216_105151_migrate_DHC58_improvment_racikan_freetext cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220216_105151_migrate_DHC58_improvment_racikan_freetext cannot be reverted.\n";

        return false;
    }
    */
}
