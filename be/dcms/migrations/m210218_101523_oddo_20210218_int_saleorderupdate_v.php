<?php

use yii\db\Migration;

/**
 * Class m210218_101523_oddo_20210218_int_saleorderupdate_v
 */
class m210218_101523_oddo_20210218_int_saleorderupdate_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    
    $this->execute('DROP VIEW if exists "public"."int_saleorderupdate_v";');

    $this->execute("
        CREATE VIEW \"public\".\"int_saleorderupdate_v\" AS  SELECT pembayaran_r.id,
    pendaftaran_t.pendaftaran_id::character varying AS sync_id_api,
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
            ELSE COALESCE(pasienadmisi_t.penjamin_id, 0)
        END AS payer_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(p1.s_kode, '-'::character varying)
            ELSE COALESCE(p2.penjamin_kode, '-'::character varying)
        END AS payer_code,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying)
            ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), '-'::character varying)
        END AS payer_type,
    6 AS sync_type,
    'done'::text AS state,
    pembayaran.total_tunai + pembayaran.total_nontunai - pembayaran.total_kembalian AS personal_amount,
    pembayaran.total_dijamin AS payer_amount,
    pembayaran.total_tagihan AS total_amount,
    pembayaran_r.is_update
   FROM pembayaran_r
     JOIN pendaftaran_t ON pembayaran_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN penjamin_m p1 ON pendaftaran_t.penjamin_id = p1.penjamin_id
     LEFT JOIN penjamin_m p2 ON pasienadmisi_t.penjamin_id = p2.penjamin_id
     LEFT JOIN carabayar_m cb1 ON p1.carabayar_id = cb1.carabayar_id
     LEFT JOIN carabayar_m cb2 ON p2.carabayar_id = cb2.carabayar_id
     JOIN pembayaranpelayanan_t ON pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id AND pembayaranpelayanan_t.is_deleted = false
     LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
            pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount AS total_tagihan,
            pembayaran_t.total_tunai,
            pembayaran_t.total_nontunai,
            pembayaran_t.total_kembalian,
            pembayaran_t.total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pembayaran_id, pembayaran_t.total_tagihan, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_kembalian, pembayaran_t.total_dijamin) pembayaran ON pembayaran_r.pembayaran_id = pembayaran.pembayaran_id
  WHERE pembayaran_r.is_update = false
UNION ALL
 SELECT pembayaran_r.id,
    concat('RSPB', penjualanresep_t.penjualanresep_id) AS sync_id_api,
    penjualanresep_t.noresep AS name,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS confirmation_date,
    0 AS partner_id,
    penjualanresep_t.tglresep AS date_order,
    1 AS patient_type,
    COALESCE(penjualanresep_t.penjamin_id, 0) AS payer_id,
    COALESCE(penjamin_m.penjamin_kode, '-'::character varying) AS payer_code,
    COALESCE(fgetnamalookup(carabayar_m.groupcarabayar_id), '-'::character varying) AS payer_type,
    6 AS sync_type,
    'done'::text AS state,
    pembayaran.total_tunai + pembayaran.total_nontunai - pembayaran.total_kembalian AS personal_amount,
    pembayaran.total_dijamin AS payer_amount,
    pembayaran.total_tagihan AS total_amount,
    pembayaran_r.is_update
   FROM pembayaran_r
     JOIN pembayaranpelayanan_t ON pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id AND pembayaranpelayanan_t.is_deleted = false
     JOIN penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id AND penjualanresep_t.jenispenjualan::text = '343'::text
     LEFT JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
            pembayaran_t.total_tagihan + pembayaran_t.total_administrasi + pembayaran_t.total_pembulatan - pembayaran_t.total_discountpembayaran - pembayaran_t.total_discount AS total_tagihan,
            pembayaran_t.total_tunai,
            pembayaran_t.total_nontunai,
            pembayaran_t.total_kembalian,
            pembayaran_t.total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pembayaran_id, pembayaran_t.total_tagihan, pembayaran_t.total_tunai, pembayaran_t.total_nontunai, pembayaran_t.total_kembalian, pembayaran_t.total_dijamin) pembayaran ON pembayaran_r.pembayaran_id = pembayaran.pembayaran_id
  WHERE pembayaran_r.is_update = false;");

    $this->execute('ALTER TABLE "public"."int_saleorderupdate_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210218_101523_oddo_20210218_int_saleorderupdate_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210218_101523_oddo_20210218_int_saleorderupdate_v cannot be reverted.\n";

        return false;
    }
    */
}
