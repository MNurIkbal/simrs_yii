<?php

use yii\db\Migration;

/**
 * Class m210803_045038_migrate_zataktifobat_v
 */
class m210803_045038_migrate_zataktifobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
 
        $this->execute('DROP VIEW if exists "public"."zataktifobat_v";');

        $this->execute("
            CREATE VIEW \"public\".\"zataktifobat_v\" AS  SELECT zataktifobat_mp.obatalkes_id,
    obat.obatalkes_nama AS obat,
    zataktifobat_mp.zataktif_id,
    zataktif.zataktif_nama
   FROM zataktifobat_mp
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama
           FROM obatalkes_m a) obat ON zataktifobat_mp.obatalkes_id = obat.obatalkes_id
     JOIN ( SELECT a.zataktif_id,
            a.zataktif_nama
           FROM zataktif_m a) zataktif ON zataktifobat_mp.zataktif_id = zataktif.zataktif_id
  WHERE zataktifobat_mp.is_deleted = false;");

                $this->execute('ALTER TABLE "public"."zataktifobat_v" OWNER TO "postgres";');




    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210803_045038_migrate_zataktifobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210803_045038_migrate_zataktifobat_v cannot be reverted.\n";

        return false;
    }
    */
}
