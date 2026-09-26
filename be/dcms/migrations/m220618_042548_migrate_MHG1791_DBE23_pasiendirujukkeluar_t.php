<?php

use yii\db\Migration;

/**
 * Class m220618_042548_migrate_MHG1791_DBE23_pasiendirujukkeluar_t
 */
class m220618_042548_migrate_MHG1791_DBE23_pasiendirujukkeluar_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasiendirujukkeluar_t" 
          ADD COLUMN IF NOT EXISTS "permintaankepenunjang_id" int4,
          ADD COLUMN IF NOT EXISTS "diagnosa" text COLLATE "pg_catalog"."default";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220618_042548_migrate_MHG1791_DBE23_pasiendirujukkeluar_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220618_042548_migrate_MHG1791_DBE23_pasiendirujukkeluar_t cannot be reverted.\n";

        return false;
    }
    */
}
