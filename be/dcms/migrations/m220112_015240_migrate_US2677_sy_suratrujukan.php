<?php

use yii\db\Migration;

/**
 * Class m220112_015240_migrate_US2677_sy_suratrujukan
 */
class m220112_015240_migrate_US2677_sy_suratrujukan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."rujukanbpjs_t" 
          ADD COLUMN IF NOT EXISTS "tanggal_rencana_kunjungan" timestamp(6),
          ADD COLUMN IF NOT EXISTS  "kode_spesialis" varchar(50) COLLATE "pg_catalog"."default";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220112_015240_migrate_US2677_sy_suratrujukan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220112_015240_migrate_US2677_sy_suratrujukan cannot be reverted.\n";

        return false;
    }
    */
}
