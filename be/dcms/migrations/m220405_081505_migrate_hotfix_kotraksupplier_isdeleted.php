<?php

use yii\db\Migration;

/**
 * Class m220405_081505_migrate_hotfix_kotraksupplier_isdeleted
 */
class m220405_081505_migrate_hotfix_kotraksupplier_isdeleted extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."kontraksupplierheader_v";');
        $this->execute("CREATE VIEW \"public\".\"kontraksupplierheader_v\" AS   SELECT kontraksupplier_m.kontraksupplier_id,
    kontraksupplier_m.supplier_id,
    supplier_m.supplier_nama,
    kontraksupplier_m.payterm_id,
    kontraksupplier_m.jumlah_hari,
    kontraksupplier_m.pajak_id,
    kontraksupplier_m.persen_ppn,
    supplier_m.supplier_kode,
    kontraksupplier_m.kontraksupplier_no,
    kontraksupplier_m.tgl_berlaku,
    kontraksupplier_m.metode_bayar,
    kontraksupplier_m.dikirim_ke,
    kontraksupplier_m.contact_person,
    kontraksupplier_m.catatan,
    kontraksupplier_m.is_active,
    payterm_m.payterm_nama,
    pajak_m.pajak_name AS pajak_nama
   FROM kontraksupplier_m
     LEFT JOIN supplier_m ON kontraksupplier_m.supplier_id = supplier_m.supplier_id
     LEFT JOIN payterm_m ON kontraksupplier_m.payterm_id = payterm_m.payterm_id
     LEFT JOIN pajak_m ON kontraksupplier_m.pajak_id = pajak_m.pajak_id
  WHERE kontraksupplier_m.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220405_081505_migrate_hotfix_kotraksupplier_isdeleted cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220405_081505_migrate_hotfix_kotraksupplier_isdeleted cannot be reverted.\n";

        return false;
    }
    */
}
