<?php

use yii\db\Migration;

/**
 * Class m220620_062004_migrate_invoiceobatdetail_v
 */
class m220620_062004_migrate_invoiceobatdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."invoiceobatdetail_v";');

         $this->execute("
            CREATE VIEW \"public\".\"invoiceobatdetail_v\" AS  SELECT pembayaranpelayanan_t.pembayaran_id,
    pembayaranpelayanan_t.no_pembayaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    penjualanresep_t.noresep AS no_resep,
    penjualanresep_t.tglresep AS tgl_resep,
    detail_obat.obatalkes_nama,
    detail_obat.qty,
    detail_obat.uom,
    detail_obat.harga_satuan,
    detail_obat.tarif,
    detail_obat.tarif_dijamin,
    detail_obat.tarif_dibayarkan,
    detail_obat.tarif_diskon,
    detail_obat.penjamin_id AS penjamin_pelayanan_id,
    detail_obat.penjamin_nama
   FROM pembayaranpelayanan_t
     JOIN ( SELECT a.pembayaran_id
           FROM pembayaran_t a) pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id
     JOIN ( SELECT a.pembayaranpelayanan_id,
            a.obatsudahbayar_id,
            a.obatalkespasien_id
           FROM obatsudahbayar_t a) obatsudahbayar_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = obatsudahbayar_t.pembayaranpelayanan_id
     JOIN ( SELECT obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.penjualanresep_id,
            obatalkespasien_t.obatalkespasien_id,
            obatalkes_m.obatalkes_nama,
                CASE
                    WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.det
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.hargasatuan_oa AS harga_satuan,
            obatalkespasien_t.hargajual_oa AS tarif,
            sat_kecil.satuanunit_nama AS uom,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan,
            obatalkespasien_t.tarif_diskon,
            obatalkespasien_t.penjamin_id,
            penjamin_m.penjamin_nama
           FROM obatalkespasien_t
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_nama
                   FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN ( SELECT a.satuanunit_id,
                    a.satuanunit_nama
                   FROM satuanunit_m a) sat_kecil ON obatalkespasien_t.satuankecil_id = sat_kecil.satuanunit_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON obatalkespasien_t.penjamin_id = penjamin_m.penjamin_id
          WHERE obatalkespasien_t.is_deleted = false) detail_obat ON obatsudahbayar_t.obatsudahbayar_id = detail_obat.obatsudahbayar_id AND obatsudahbayar_t.obatalkespasien_id = detail_obat.obatalkespasien_id
     JOIN ( SELECT a.penjualanresep_id,
            a.noresep,
            a.tglresep
           FROM penjualanresep_t a) penjualanresep_t ON detail_obat.penjualanresep_id = penjualanresep_t.penjualanresep_id;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220620_062004_migrate_invoiceobatdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220620_062004_migrate_invoiceobatdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
