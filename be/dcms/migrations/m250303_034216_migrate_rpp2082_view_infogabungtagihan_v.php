<?php

use yii\db\Migration;

/**
 * Class m250303_034216_migrate_rpp2082_view_infogabungtagihan_v
 */
class m250303_034216_migrate_rpp2082_view_infogabungtagihan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infogabungtagihan_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infogabungtagihan_v" AS  SELECT gabung_tagihan.gabungpelayanandetail_id,
    gabung_tagihan.pendaftaran_id,
    gabung_tagihan.ref_no_pendaftaran,
    gabung_tagihan.no_pendaftaran,
    gabung_tagihan.tgl_pendaftaran,
    gabung_tagihan.tgl_pulang,
    gabung_tagihan.nama_pasien,
    gabung_tagihan.no_rekam_medik,
    gabung_tagihan.jenis_kelamin_kode,
    gabung_tagihan.jenis_kelamin,
    gabung_tagihan.ruangan,
    gabung_tagihan.instalasi_id,
    gabung_tagihan.instalasi,
    gabung_tagihan.ref_penjamin, 
    gabung_tagihan.cara_bayar,
    gabung_tagihan.penjamin,
    gabung_tagihan.no_sep,
    gabung_tagihan.status_bayar,
    gabung_tagihan.status_bayar_id,
    gabung_tagihan.tgl_gabung,
    gabung_tagihan.ref_pendaftaran_id,
    gabung_tagihan.pembayaran_id,
    gabung_tagihan.total_tindakan + gabung_tagihan.total_obat + gabung_tagihan.ref_total_tindakan + gabung_tagihan.ref_total_obat AS total_tagihan,
    gabung_tagihan.pembayaran_deleted,
    gabung_tagihan.is_deleted
   FROM ( SELECT gabungpelayanandetail_t.gabungpelayanandetail_id,
            gabungpelayanandetail_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran AS ref_no_pendaftaran,
            pendaftaran_nontujuan.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            COALESCE(pulang_ri.tglpasienpulang, pendaftaran_t.tgl_stopakomodasi, pulang_rjrd.tglpasienpulang) AS tgl_pulang,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            gender.lookup_kode AS jenis_kelamin_kode,
            gender.lookup_name AS jenis_kelamin,
            ruangan_m.ruangan_nama AS ruangan,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama AS instalasi,
            ref_pendaftaran.ref_penjamin,
            carabayar_m.carabayar_nama AS cara_bayar,
            penjamin_m.penjamin_nama AS penjamin,
            bpjs_t.nosep AS no_sep,
            lkp_statusbayar.lookup_name AS status_bayar,
            pendaftaran_t.status_bayar AS status_bayar_id,
            gabungpelayanandetail_t.created_date AS tgl_gabung,
            gabungpelayanandetail_t.ref_pendaftaran_id,
            pembayaran_t.pembayaran_id,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM tindakanpelayanan_t
                      WHERE tindakanpelayanan_t.is_deleted IS FALSE AND gabungpelayanandetail_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                     LIMIT 1)) THEN ( SELECT COALESCE(round(sum(a.tarif_tindakan)::numeric, 2), 0::numeric) AS tarif_tindakan
                       FROM tindakanpelayanan_t a
                      WHERE a.is_deleted IS FALSE AND a.pendaftaran_id IS NOT NULL AND gabungpelayanandetail_t.pendaftaran_id = a.pendaftaran_id)
                    ELSE 0::numeric
                END AS total_tindakan,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM obatalkespasien_t
                      WHERE obatalkespasien_t.is_deleted IS FALSE AND gabungpelayanandetail_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
                     LIMIT 1)) THEN ( SELECT COALESCE(round(sum(a.hargajual_oa)::numeric, 2), 0::numeric) AS "coalesce"
                       FROM obatalkespasien_t a
                      WHERE a.is_deleted IS FALSE AND a.pendaftaran_id IS NOT NULL AND gabungpelayanandetail_t.pendaftaran_id = a.pendaftaran_id)
                    ELSE 0::numeric
                END AS total_obat,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM tindakanpelayanan_t
                      WHERE tindakanpelayanan_t.is_deleted IS FALSE AND gabungpelayanandetail_t.ref_pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                     LIMIT 1)) THEN ( SELECT COALESCE(round(sum(a.tarif_tindakan)::numeric, 2), 0::numeric) AS tarif_tindakan
                       FROM tindakanpelayanan_t a
                      WHERE a.is_deleted IS FALSE AND a.pendaftaran_id IS NOT NULL AND gabungpelayanandetail_t.ref_pendaftaran_id = a.pendaftaran_id)
                    ELSE 0::numeric
                END AS ref_total_tindakan,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM obatalkespasien_t
                      WHERE obatalkespasien_t.is_deleted IS FALSE AND gabungpelayanandetail_t.ref_pendaftaran_id = obatalkespasien_t.pendaftaran_id
                     LIMIT 1)) THEN ( SELECT COALESCE(round(sum(a.hargajual_oa)::numeric, 2), 0::numeric) AS "coalesce"
                       FROM obatalkespasien_t a
                      WHERE a.is_deleted IS FALSE AND a.pendaftaran_id IS NOT NULL AND gabungpelayanandetail_t.ref_pendaftaran_id = a.pendaftaran_id)
                    ELSE 0::numeric
                END AS ref_total_obat,
            pembayaran_t.is_deleted AS pembayaran_deleted,
            gabungpelayanandetail_t.is_deleted
           FROM gabungpelayanandetail_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran,
                    a.tgl_pendaftaran,
                    a.status_bayar,
                    a.tgl_stopakomodasi,
                    a.pasien_id,
                    a.pasienpulang_id,
                    a.pasienadmisi_id,
                    a.ruangan_id,
                    a.penjamin_id
                   FROM pendaftaran_t a) pendaftaran_t ON gabungpelayanandetail_t.ref_pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran,
                    a.penjamin_id
                   FROM pendaftaran_t a) pendaftaran_nontujuan ON gabungpelayanandetail_t.pendaftaran_id = pendaftaran_nontujuan.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.jeniskelamin
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.pasienpulang_id,
                    a.bpjs_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang
                   FROM pasienpulang_t a) pulang_rjrd ON pendaftaran_t.pasienpulang_id = pulang_rjrd.pasienpulang_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang
                   FROM pasienpulang_t a) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_m ON pendaftaran_nontujuan.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_tujuan ON pendaftaran_t.penjamin_id = penjamin_tujuan.penjamin_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON penjamin_tujuan.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
             LEFT JOIN ( SELECT max(a.pembayaran_id) AS pembayaran_id,
                    a.pendaftaran_id
                   FROM pembayaran_t a
                  GROUP BY a.pendaftaran_id) max_pembayaran_t ON gabungpelayanandetail_t.ref_pendaftaran_id = max_pembayaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pembayaran_id,
                    a.is_deleted,
                    a.total_tagihan
                   FROM pembayaran_t a) pembayaran_t ON max_pembayaran_t.pembayaran_id = pembayaran_t.pembayaran_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name,
                    a.lookup_kode
                   FROM lookup_m a) gender ON gender.lookup_id = pasien_m.jeniskelamin::integer
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.no_pendaftaran,
                    ref_penjamin.penjamin_nama AS ref_penjamin
                   FROM pendaftaran_t a
                     LEFT JOIN ( SELECT a_1.penjamin_id,
                            a_1.penjamin_nama
                           FROM penjamin_m a_1) ref_penjamin ON a.penjamin_id = ref_penjamin.penjamin_id) ref_pendaftaran ON gabungpelayanandetail_t.ref_pendaftaran_id = ref_pendaftaran.pendaftaran_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) lkp_statusbayar ON pendaftaran_t.status_bayar = lkp_statusbayar.lookup_id
          WHERE gabungpelayanandetail_t.is_deleted IS FALSE) gabung_tagihan;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250303_034216_migrate_rpp2082_view_infogabungtagihan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250303_034216_migrate_rpp2082_view_infogabungtagihan_v cannot be reverted.\n";

        return false;
    }
    */
}
