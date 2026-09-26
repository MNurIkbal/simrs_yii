<?php

use yii\db\Migration;

/**
 * Class m210127_101619_migrate_20200127_saleorder_v
 */
class m210127_101619_migrate_20200127_saleorder_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."saleorder_v";');

        $this->execute("
            CREATE VIEW \"public\".\"saleorder_v\" AS  SELECT pendaftaran_r.id,
    pendaftaran_r.pendaftaran_id::character varying AS sync_id_api,
    pendaftaran_r.no_pendaftaran AS name,
    COALESCE(pembayaranpelayanan_t.no_pembayaran, '-'::character varying) AS billno,
        CASE
            WHEN pembayaranpelayanan_t.pembayaranpelayanan_id IS NULL THEN pendaftaran_r.tgl_pendaftaran
            ELSE pembayaranpelayanan_t.tgl_pembayaran
        END AS confirmation_date,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.tgl_pendaftaran AS date_order,
        CASE
            WHEN pendaftaran_r.is_aps = true THEN 1
            WHEN pendaftaran_r.instalasi_id = 1 THEN 1
            WHEN pendaftaran_r.instalasi_id = 3 THEN 2
            WHEN pendaftaran_r.instalasi_id = 2 THEN 3
            WHEN pendaftaran_r.instalasi_id = 6 THEN 6
            WHEN pendaftaran_r.instalasi_id = 21 THEN 5
            ELSE 4
        END AS patient_type,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN concat('PEN', pendaftaran_r.penjamin_id)
            ELSE concat('PEN', pasienadmisi_r.penjamin_id)
        END AS payer_id,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN COALESCE(p1.penjamin_kode, '-'::character varying)
            ELSE COALESCE(p2.penjamin_kode, '-'::character varying)
        END AS payer_code,
        CASE
            WHEN pendaftaran_r.pasienadmisi_id IS NULL THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying)
            ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), '-'::character varying)
        END AS payer_type,
    6 AS sync_type,
        CASE
            WHEN pendaftaran_r.keterangan::text = 'UPDATE'::text THEN 'done'::text
            ELSE 'draft'::text
        END AS state,
    pendaftaran_r.keterangan,
    pendaftaran_r.is_sending,
    pendaftaran_r.is_sent,
        CASE
            WHEN pendaftaran_r.asuransipasien_id IS NULL THEN COALESCE(asuransipasien_m.namapemilikasuransi, '-'::character varying)
            ELSE COALESCE(asuransipasien_m.namapemilikasuransi, '-'::character varying)
        END AS nama_asuransi,
        CASE
            WHEN pendaftaran_r.asuransipasien_id IS NULL THEN COALESCE(asuransipasien_m.nokartuasuransi, '-'::character varying)
            ELSE COALESCE(asuransipasien_m.nokartuasuransi, '-'::character varying)
        END AS no_asuransi
   FROM pendaftaran_r
     LEFT JOIN pasienadmisi_r ON pendaftaran_r.pasienadmisi_id = pasienadmisi_r.pasienadmisi_id
     LEFT JOIN penjamin_m p1 ON pendaftaran_r.penjamin_id = p1.penjamin_id
     LEFT JOIN penjamin_m p2 ON pasienadmisi_r.penjamin_id = p2.penjamin_id
     LEFT JOIN carabayar_m cb1 ON p1.carabayar_id = cb1.carabayar_id
     LEFT JOIN carabayar_m cb2 ON p2.carabayar_id = cb2.carabayar_id
     LEFT JOIN asuransipasien_m ON pendaftaran_r.asuransipasien_id = asuransipasien_m.asuransipasien_id
     LEFT JOIN pembayaranpelayanan_t ON pendaftaran_r.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id AND pembayaranpelayanan_t.is_deleted = false
UNION ALL
 SELECT penjualanresep_r.id,
    concat('RSPB', penjualanresep_r.penjualanresep_id) AS sync_id_api,
    penjualanresep_r.noresep AS name,
    COALESCE(pembayaranpelayanan_t.no_pembayaran, '-'::character varying) AS billno,
        CASE
            WHEN pembayaranpelayanan_t.pembayaranpelayanan_id IS NULL THEN penjualanresep_r.tglresep
            ELSE pembayaranpelayanan_t.tgl_pembayaran
        END AS confirmation_date,
    0 AS partner_id,
    penjualanresep_r.tglresep AS date_order,
    1 AS patient_type,
    concat('PEN', penjualanresep_r.penjamin_id) AS payer_id,
    COALESCE(penjamin_m.penjamin_kode, '-'::character varying) AS payer_code,
    COALESCE(fgetnamalookup(carabayar_m.groupcarabayar_id), '-'::character varying) AS payer_type,
    6 AS sync_type,
        CASE
            WHEN penjualanresep_r.keterangan::text = 'UPDATE'::text THEN 'done'::text
            ELSE 'draft'::text
        END AS state,
    penjualanresep_r.keterangan,
    penjualanresep_r.is_sending,
    penjualanresep_r.is_sent,
    '-'::character varying AS nama_asuransi,
    '-'::character varying AS no_asuransi
   FROM penjualanresep_r
     LEFT JOIN penjamin_m ON penjualanresep_r.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pembayaranpelayanan_t ON penjualanresep_r.penjualanresep_id = pembayaranpelayanan_t.penjualanresep_id AND pembayaranpelayanan_t.is_deleted = false
  WHERE penjualanresep_r.jenispenjualan::text = '343'::text;");

        $this->execute('ALTER TABLE "public"."saleorder_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210127_101619_migrate_20200127_saleorder_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_101619_migrate_20200127_saleorder_v cannot be reverted.\n";

        return false;
    }
    */
}
