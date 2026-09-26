<?php

use yii\db\Migration;

/**
 * Class m210127_060435_migrate_20200127_tindakanoperasi_v
 */
class m210127_060435_migrate_20200127_tindakanoperasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    $this->execute('DROP VIEW if exists "public"."tindakanoperasi_v";');

    $this->execute("
        CREATE VIEW \"public\".\"tindakanoperasi_v\" AS  SELECT tindakanoperasi_mp.timoperasi_id,
    lookup_m.lookup_name AS timoperasi_nama,
    tindakanoperasi_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanoperasi_mp.prosentase AS persentase,
    tindakanoperasi_mp.is_active,
    tindakanoperasi_mp.created_date
   FROM tindakanoperasi_mp
     JOIN daftartindakan_m ON tindakanoperasi_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN lookup_m ON tindakanoperasi_mp.timoperasi_id = lookup_m.lookup_id;");
    
    $this->execute('ALTER TABLE "public"."tindakanoperasi_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210127_060435_migrate_20200127_tindakanoperasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_060435_migrate_20200127_tindakanoperasi_v cannot be reverted.\n";

        return false;
    }
    */
}
