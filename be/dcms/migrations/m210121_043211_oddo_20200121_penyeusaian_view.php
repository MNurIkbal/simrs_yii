<?php

use yii\db\Migration;

/**
 * Class m210121_043211_oddo_20200121_penyeusaian_view
 */
class m210121_043211_oddo_20200121_penyeusaian_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."int_saleorderupdate_v";');

        $this->execute("
            CREATE VIEW \"public\".\"int_saleorderupdate_v\" AS  SELECT pembayaran_r.id,
    pendaftaran_t.pendaftaran_id AS sync_id_api,
    pendaftaran_t.no_pendaftaran AS name,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS confirmation_date,
    pendaftaran_t.pasien_id AS partner_id,
    pendaftaran_t.tgl_pendaftaran AS date_order,
        CASE
            WHEN pendaftaran_t.instalasi_id = 1 THEN 1
            WHEN pendaftaran_t.instalasi_id = 3 THEN 2
            WHEN pendaftaran_t.instalasi_id = 2 THEN 3
            WHEN pendaftaran_t.instalasi_id = 6 THEN 6
            WHEN pendaftaran_t.instalasi_id = 21 THEN 5
            ELSE 4
        END AS patient_type,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(pendaftaran_t.penjamin_id, 0)
            ELSE COALESCE(pasienadmisi_r.penjamin_id, 0)
        END AS payer_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(p1.s_kode, '-'::character varying)
            ELSE COALESCE(p2.s_kode, '-'::character varying)
        END AS payer_code,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying)
            ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), '-'::character varying)
        END AS payer_type,
    6 AS sync_type,
    'done'::text AS state,
    pembayaran_r.total_tunai + pembayaran_r.total_nontunai - pembayaran_r.total_kembalian AS personal_amount,
    pembayaran_r.total_dijamin AS payer_amount,
    total_ditagihkan.total_tagihan AS total_amount,
    pembayaran_r.is_update
   FROM pembayaran_r
     LEFT JOIN pendaftaran_t ON pembayaran_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_r ON pendaftaran_t.pasienadmisi_id = pasienadmisi_r.pasienadmisi_id
     LEFT JOIN penjamin_m p1 ON pendaftaran_t.penjamin_id = p1.penjamin_id
     LEFT JOIN penjamin_m p2 ON pasienadmisi_r.penjamin_id = p2.penjamin_id
     LEFT JOIN carabayar_m cb1 ON p1.carabayar_id = cb1.carabayar_id
     LEFT JOIN carabayar_m cb2 ON p2.carabayar_id = cb2.carabayar_id
     JOIN pembayaranpelayanan_t ON pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id AND pembayaranpelayanan_t.is_deleted = false
     LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
            pembayaran_t.total_tagihan
           FROM pembayaran_t
          GROUP BY pembayaran_t.pembayaran_id, pembayaran_t.total_tagihan) total_ditagihkan ON pembayaran_r.pembayaran_id = total_ditagihkan.pembayaran_id
  WHERE pembayaran_r.is_update = false;");
        
        $this->execute('ALTER TABLE "public"."int_saleorderupdate_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210121_043211_oddo_20200121_penyeusaian_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210121_043211_oddo_20200121_penyeusaian_view cannot be reverted.\n";

        return false;
    }
    */
}
