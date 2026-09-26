<?php

use yii\db\Migration;

/**
 * Class m210319_013006_migrate_20200319_konfigrak_v
 */
class m210319_013006_migrate_20200319_konfigrak_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.konfigrak_v;');

        $this->execute("
            CREATE VIEW \"public\".\"konfigrak_v\" AS  SELECT konfigrak_m.konfigrak_id,
    konfigrak_m.ruangan_id,
    ruangan_m.ruangan_nama,
    konfigrak_m.rakobat_id,
    rakobat_m.rakobat_nama,
    obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.satuankecil_id,
    sat_kecil.satuanunit_nama AS satuan_kecil,
    konfigrak_m.min_stok,
    konfigrak_m.max_stok
   FROM konfigrak_m
     JOIN ruangan_m ON konfigrak_m.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN rakobat_m ON konfigrak_m.rakobat_id = rakobat_m.rakobat_id
     JOIN obatalkes_m ON konfigrak_m.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
  WHERE konfigrak_m.is_deleted = false;");
        
        $this->execute('ALTER TABLE "public"."konfigrak_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210319_013006_migrate_20200319_konfigrak_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210319_013006_migrate_20200319_konfigrak_v cannot be reverted.\n";

        return false;
    }
    */
}
