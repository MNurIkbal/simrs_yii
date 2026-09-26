<?php

use yii\db\Migration;

/**
 * Class m201124_100428_migrate_20201124_detailformulirstokopname_v
 */
class m201124_100428_migrate_20201124_detailformulirstokopname_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."detailformulirstokopname_v";');

        $this->execute("
            CREATE VIEW \"public\".\"detailformulirstokopname_v\" AS  SELECT formstokopname_t.formstokopname_id,
    formstokopname_t.formulirstokopname_id,
    formstokopname_t.volume_stok AS stok_sistem,
    formstokopname_t.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.obatalkes_nama,
    formstokopname_t.nobatch,
        CASE
            WHEN stokobatalkes_t.tglkadaluarsa IS NULL THEN formstokopname_t.tglkadaluarsa
            ELSE stokobatalkes_t.tglkadaluarsa
        END AS tglkadaluarsa,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS hargajual,
    obatalkes_m.harganetto,
    formstokopname_t.stokobatalkes_id,
    obatalkes_m.satuankecil_id,
    sat_kecil.satuanunit_nama
   FROM formstokopname_t
     JOIN obatalkes_m ON formstokopname_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN stokobatalkes_t ON formstokopname_t.stokobatalkes_id = stokobatalkes_t.stokobatalkes_id
     LEFT JOIN satuanunit_m sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
  WHERE formstokopname_t.is_active = true AND formstokopname_t.is_deleted = false;");
        
        $this->execute('ALTER TABLE "public"."detailformulirstokopname_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201124_100428_migrate_20201124_detailformulirstokopname_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201124_100428_migrate_20201124_detailformulirstokopname_v cannot be reverted.\n";

        return false;
    }
    */
}
