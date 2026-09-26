<?php

use yii\db\Migration;

/**
 * Class m210626_075003_migrate_infodokter_v
 */
class m210626_075003_migrate_infodokter_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infodokter_v";');
     
        $this->execute("
            CREATE VIEW \"public\".\"infodokter_v\" AS  SELECT pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai
   FROM pegawai_m
  WHERE pegawai_m.kelompokpegawai_id = 1 AND pegawai_m.is_deleted = false AND pegawai_m.is_active = true;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210626_075003_migrate_infodokter_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210626_075003_migrate_infodokter_v cannot be reverted.\n";

        return false;
    }
    */
}
