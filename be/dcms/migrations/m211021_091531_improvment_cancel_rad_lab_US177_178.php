<?php

use yii\db\Migration;

/**
 * Class m211021_091531_improvment_cancel_rad_lab_US177_178
 */
class m211021_091531_improvment_cancel_rad_lab_US177_178 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('
            DROP VIEW IF EXISTS "public"."infoorderanraddetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoorderanraddetail_v" AS  SELECT permintaankepenunjang_t.permintaankepenunjang_id,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                daftartindakan_m.daftartindakan_nama, 
                NULL::character varying AS tipepaket_nama,
                permintaankepenunjang_t.qtypermintaan,
                permintaankepenunjang_t.is_cyto,
                permintaankepenunjang_t.tarif_pelayanan,
                permintaankepenunjang_t.daftartindakan_id,
                permintaankepenunjang_t.tipepaket_id,
                permintaankepenunjang_t.tarif_cytotindakan,
                permintaankepenunjang_t.satuan_tindakan,
                daftartindakan_m.daftartindakan_kode,
                permintaankepenunjang_t.dokter_id,
                permintaankepenunjang_t.is_deleted,
                permintaankepenunjang_t.alasan_batal
               FROM ((((pasienkirimkeunitlain_t
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN pemeriksaanrad_m ON ((permintaankepenunjang_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
                 LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
                 JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 5)
            UNION ALL
             SELECT permintaankepenunjang_t.permintaankepenunjang_id,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                concat(tipepaket_m.tipepaket_nama, \'-\', daftartindakan_m.daftartindakan_nama) AS daftartindakan_nama,
                tipepaket_m.tipepaket_nama,
                permintaankepenunjang_t.qtypermintaan,
                permintaankepenunjang_t.is_cyto,
                permintaankepenunjang_t.tarif_pelayanan,
                paketpelayanan_mp.daftartindakan_id,
                permintaankepenunjang_t.tipepaket_id,
                permintaankepenunjang_t.tarif_cytotindakan,
                permintaankepenunjang_t.satuan_tindakan,
                daftartindakan_m.daftartindakan_kode,
                permintaankepenunjang_t.dokter_id,
                permintaankepenunjang_t.is_deleted,
                permintaankepenunjang_t.alasan_batal
               FROM ((((((pasienkirimkeunitlain_t
                 JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                 JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN paketpelayanan_mp ON ((permintaankepenunjang_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                 JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pemeriksaanrad_m ON ((paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
                 JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 5);
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
                COALESCE(instruksitindakan_t.deleted_date, tindakan_deleted.tgl_batal) AS tgl_batal
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
                        tindakanpelayanan_t.alasan_batal
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
                COALESCE(instruksitindakan_t.deleted_date, tindakan_deleted.tgl_batal) AS tgl_batal
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
                        tindakanpelayanan_t.alasan_batal
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
                COALESCE(instruksitindakanbmhp_t.deleted_date, obat_deleted.tgl_batal) AS tgl_batal
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
                        obatalkespasien_t.alasan_batal
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
                obat_deleted.tgl_batal
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
                        obatalkespasien_t.alasan_batal
                       FROM ((obatalkespasien_t
                         LEFT JOIN loginpemakai_k leg_deleted ON ((obatalkespasien_t.deleted_by = leg_deleted.loginpemakai_id)))
                         LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
                      WHERE (obatalkespasien_t.is_deleted IS TRUE)) obat_deleted ON ((resepturdetail_t.resepturdetail_id = obat_deleted.resepturdetail_id)))
            UNION ALL
             SELECT \'RACIKAN\'::text AS tipe_instruksi,
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
                \'Racikan\'::character varying AS ket_racik_nama,
                \'OR\'::character varying AS ket_racik,
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
                NULL::timestamp without time zone AS tgl_batal
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
                COALESCE(tindakan_deleted.alasan_batal, batal_order.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
                peg_deleted.pegawai_id AS pegawai_hapus_id,
                peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                COALESCE(batal_order.tgl_batal, tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal
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
                        tindakanpelayanan_t.deleted_by
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
                COALESCE(tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal
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
                        tindakanpelayanan_t.deleted_by
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
                COALESCE(batal_order.tgl_batal, tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal
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
                        tindakanpelayanan_t.deleted_by
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
                COALESCE(tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal
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
                        tindakanpelayanan_t.deleted_by
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
                COALESCE(tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal
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
                        tindakanpelayanan_t.deleted_by
                       FROM tindakanpelayanan_t
                      WHERE (tindakanpelayanan_t.is_deleted IS TRUE)) tindakan_deleted ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id) AND (permintaankepenunjang_t.daftartindakan_id = tindakan_deleted.daftartindakan_id))))
                 LEFT JOIN loginpemakai_k leg_deleted ON ((COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id)))
                 LEFT JOIN pegawai_m peg_deleted ON ((leg_deleted.pegawai_id = peg_deleted.pegawai_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 12);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211021_091531_improvment_cancel_rad_lab_US177_178 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211021_091531_improvment_cancel_rad_lab_US177_178 cannot be reverted.\n";

        return false;
    }
    */
}
