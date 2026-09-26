<?php

use yii\db\Migration;

/**
 * Class m220306_051907_migrate_BTS137_pasienpulang_t
 */
class m220306_051907_migrate_BTS137_pasienpulang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasienpulang_t" 
          ADD COLUMN IF NOT EXISTS "no_surat_kematian" varchar(100) COLLATE "pg_catalog"."default";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220306_051907_migrate_BTS137_pasienpulang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220306_051907_migrate_BTS137_pasienpulang_t cannot be reverted.\n";

        return false;
    }
    */
}
