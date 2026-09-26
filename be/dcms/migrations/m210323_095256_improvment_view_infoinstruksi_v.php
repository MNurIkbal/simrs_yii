<?php

use yii\db\Migration;

/**
 * Class m210323_095256_improvment_view_infoinstruksi_v
 */
class m210323_095256_improvment_view_infoinstruksi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS infoinstruksi_v;
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
                        WHEN (instruksitindakan_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END, (\' - \'::text || instruksitindakan_t.qty)) AS instruksi,
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
                instalasi_m.instalasi_nama
               FROM ((((((instruksi_t
                 JOIN instruksitindakan_t ON ((instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id)))
                 JOIN daftartindakan_m ON ((instruksitindakan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pegawai_m dokter ON ((instruksitindakan_t.dokterdpjp_id = dokter.pegawai_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN ruangan_m ON ((instruksitindakan_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
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
                        WHEN (instruksitindakan_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
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
                instalasi_m.instalasi_nama
               FROM ((((((instruksi_t
                 JOIN instruksitindakan_t ON ((instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id)))
                 JOIN tipepaket_m ON ((instruksitindakan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN pegawai_m dokter ON ((instruksitindakan_t.dokterdpjp_id = dokter.pegawai_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN ruangan_m ON ((instruksitindakan_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
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
                instalasi_m.instalasi_nama
               FROM (((((((((instruksi_t
                 JOIN instruksitindakanbmhp_t ON ((instruksi_t.instruksi_id = instruksitindakanbmhp_t.instruksi_id)))
                 LEFT JOIN instruksitindakan_t ON ((instruksitindakanbmhp_t.instruksitindakan_id = instruksitindakan_t.instruksitindakan_id)))
                 JOIN obatalkes_m ON ((instruksitindakanbmhp_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                 LEFT JOIN daftartindakan_m ON ((instruksitindakanbmhp_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN tipepaket_m ON ((instruksitindakanbmhp_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 LEFT JOIN pegawai_m dokter ON ((instruksitindakanbmhp_t.dokter_id = dokter.pegawai_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN ruangan_m ON ((instruksitindakanbmhp_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
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
                instalasi_m.instalasi_nama
               FROM ((((((((instruksi_t
                 JOIN reseptur_t ON ((instruksi_t.instruksi_id = reseptur_t.instruksi_id)))
                 JOIN resepturdetail_t ON ((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id)))
                 JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                 JOIN pegawai_m dokter ON ((reseptur_t.pegawai_id = dokter.pegawai_id)))
                 LEFT JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN ruangan_m ON ((reseptur_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
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
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END, (\'-\'::text || permintaankepenunjang_t.qtypermintaan)) AS instruksi,
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
                instalasi_m.instalasi_nama
               FROM ((((((((instruksi_t
                 JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
                 LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
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
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END, (\'-\'::text || permintaankepenunjang_t.qtypermintaan)) AS instruksi,
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
                instalasi_m.instalasi_nama
               FROM ((((((((instruksi_t
                 JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
                 LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
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
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END, (\'-\'::text || permintaankepenunjang_t.qtypermintaan)) AS instruksi,
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
                instalasi_m.instalasi_nama
               FROM ((((((((instruksi_t
                 JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
                 LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
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
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END, (\'-\'::text || permintaankepenunjang_t.qtypermintaan)) AS instruksi,
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
                instalasi_m.instalasi_nama
               FROM ((((((((instruksi_t
                 JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
                 LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
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
                        WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
                        ELSE \'Non Cyto\'::text
                    END, (\'-\'::text || permintaankepenunjang_t.qtypermintaan)) AS instruksi,
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
                instalasi_m.instalasi_nama
               FROM ((((((((instruksi_t
                 JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
                 LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
                 LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
                 LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 12);
        ');
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210323_095256_improvment_view_infoinstruksi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210323_095256_improvment_view_infoinstruksi_v cannot be reverted.\n";

        return false;
    }
    */
}
