<?php

use yii\db\Migration;

/**
 * Class m201110_030330_migrate_2975_view_soap_rj
 */
class m201110_030330_migrate_2975_view_soap_rj extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE soaprj_t ADD IF NOT EXISTS instruksi text ;'); 
        $this->execute('ALTER TABLE soaprj_t ADD IF NOT EXISTS pemberi_instruksi_id int4 ;'); 
        $this->execute('ALTER TABLE soaprj_t ADD IF NOT EXISTS is_verifikasi_verbal boolean ;'); 
        $this->execute('ALTER TABLE soaprj_t ADD IF NOT EXISTS pegawai_verbal_id int4 ;'); 
        $this->execute('ALTER TABLE soaprj_t ADD IF NOT EXISTS tgl_verif_verbal timestamp(6) ;'); 
        
        
        $this->execute('
            DROP VIEW IF EXISTS "public"."soaprj_v";
        '); 
        
        $this->execute('
            CREATE VIEW "public"."soaprj_v" AS  
            SELECT t1.jenis,
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
                t1.pegawai_verbal_id
               FROM ( SELECT \'TINDAKAN\'::text AS jenis,
                        \'Tindakan & BMHP\'::text AS grouping_tipe,
                        2 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        soaprj_t.ruangan_id,
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
                        soaprj_t.pegawai_verbal_id
                       FROM (((((((pendaftaran_t
                         JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         JOIN ruangan_m ON ((soaprj_t.ruangan_id = ruangan_m.ruangan_id)))
                         JOIN pegawai_m ON ((soaprj_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                         LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                      WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL)
                    UNION ALL
                     SELECT \'PAKET\'::text AS jenis,
                        \'Tindakan & BMHP\'::text AS grouping_tipe,
                        2 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        soaprj_t.ruangan_id,
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
                        soaprj_t.pegawai_verbal_id
                       FROM (((((((pendaftaran_t
                         JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         JOIN ruangan_m ON ((soaprj_t.ruangan_id = ruangan_m.ruangan_id)))
                         JOIN pegawai_m ON ((soaprj_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
                         JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                      WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL)
                    UNION ALL
                     SELECT \'BMHP\'::text AS jenis,
                        \'Tindakan & BMHP\'::text AS grouping_tipe,
                        2 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        soaprj_t.ruangan_id,
                        ruangan_m.ruangan_nama,
                        soaprj_t.pegawai_id,
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
                        soaprj_t.pegawai_verbal_id
                       FROM (((((((pendaftaran_t
                         JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         JOIN ruangan_m ON ((soaprj_t.ruangan_id = ruangan_m.ruangan_id)))
                         JOIN pegawai_m ON ((soaprj_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         LEFT JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
                         LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                      WHERE (obatalkespasien_t.daftartindakan_id IS NOT NULL)
                    UNION ALL
                     SELECT
                            CASE
                                WHEN ((racikan_m.racikan_singkatan)::text = \'OR\'::text) THEN \'Racikan\'::text
                                ELSE \'Non Racikan\'::text
                            END AS jenis,
                        \'Reseptur\'::text AS grouping_tipe,
                        1 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        soaprj_t.ruangan_id,
                        ruangan_m.ruangan_nama,
                        soaprj_t.pegawai_id,
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
                        soaprj_t.pegawai_verbal_id
                       FROM (((((((((reseptur_t
                         JOIN resepturdetail_t ON ((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id)))
                         LEFT JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                         LEFT JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
                         JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = reseptur_t.pendaftaran_id)))
                         JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         JOIN ruangan_m ON ((soaprj_t.ruangan_id = ruangan_m.ruangan_id)))
                         JOIN pegawai_m ON ((soaprj_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                    UNION ALL
                     SELECT \'TINDAKAN\'::text AS jenis,
                        \'Penunjang\'::text AS grouping_tipe,
                        3 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        soaprj_t.ruangan_id,
                        ruangan_m.ruangan_nama,
                        soaprj_t.pegawai_id,
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
                        soaprj_t.pegawai_verbal_id
                       FROM (((((((((pasienkirimkeunitlain_t
                         JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                         LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                         JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = pasienkirimkeunitlain_t.pendaftaran_id)))
                         JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         JOIN ruangan_m ON ((soaprj_t.ruangan_id = ruangan_m.ruangan_id)))
                         JOIN pegawai_m ON ((soaprj_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         LEFT JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                    UNION ALL
                     SELECT \'PAKET\'::text AS jenis,
                        \'Penunjang\'::text AS grouping_tipe,
                        3 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        soaprj_t.ruangan_id,
                        ruangan_m.ruangan_nama,
                        soaprj_t.pegawai_id,
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
                        soaprj_t.pegawai_verbal_id
                       FROM (((((((((pasienkirimkeunitlain_t
                         JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
                         LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
                         JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = pasienkirimkeunitlain_t.pendaftaran_id)))
                         JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         JOIN ruangan_m ON ((soaprj_t.ruangan_id = ruangan_m.ruangan_id)))
                         JOIN pegawai_m ON ((soaprj_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                         LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
                         JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                    UNION ALL
                     SELECT \'Verbal Order\'::text AS jenis,
                        \'Verbal Order\'::text AS grouping_tipe,
                        5 AS jenis_urutan,
                        pendaftaran_t.pendaftaran_id,
                        soaprj_t.ruangan_id,
                        ruangan_m.ruangan_nama,
                        soaprj_t.pegawai_id,
                        pegawai_m.nama_pegawai,
                        kelompokpegawai_m.kelompokpegawai_nama,
                        \'-\'::text AS nama_profesi,
                        soaprj_t.subject,
                        soaprj_t.object,
                        array_to_json(NULL::character varying[]) AS a_diag_utama,
                        array_to_json(NULL::character varying[]) AS a_diag_penyerta,
                        soaprj_t.tgl_soaprj AS tgl_tindakan,
                        \'-\'::text AS instruksi,
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
                        soaprj_t.pegawai_verbal_id
                       FROM ((((pendaftaran_t
                         JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
                         JOIN ruangan_m ON ((soaprj_t.ruangan_id = ruangan_m.ruangan_id)))
                         JOIN pegawai_m ON ((soaprj_t.pegawai_id = pegawai_m.pegawai_id)))
                         JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
                      WHERE (soaprj_t.instruksi IS NOT NULL)) t1
              ORDER BY t1.pendaftaran_id DESC;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201110_030330_migrate_2975_view_soap_rj cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201110_030330_migrate_2975_view_soap_rj cannot be reverted.\n";

        return false;
    }
    */
}
