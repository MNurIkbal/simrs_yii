<?php

use yii\db\Migration;

/**
 * Class m200915_050109_migrate_20200915_kontraksuppheader
 */
class m200915_050109_migrate_20200915_kontraksuppheader extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('DROP VIEW if exists "public"."kontraksupplierheader_v";');

    $this->execute("
        CREATE VIEW \"public\".\"kontraksupplierheader_v\" AS  SELECT kontraksupplier_m.kontraksupplier_id,
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
    kontraksupplier_m.is_active
   FROM (kontraksupplier_m
     JOIN supplier_m ON ((kontraksupplier_m.supplier_id = supplier_m.supplier_id)));");
    
    $this->execute('ALTER TABLE "public"."kontraksupplierheader_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200915_050109_migrate_20200915_kontraksuppheader cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200915_050109_migrate_20200915_kontraksuppheader cannot be reverted.\n";

        return false;
    }
    */
}
