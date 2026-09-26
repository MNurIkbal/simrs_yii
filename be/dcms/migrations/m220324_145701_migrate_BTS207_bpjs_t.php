<?php

use yii\db\Migration;

/**
 * Class m220324_145701_migrate_BTS207_bpjs_t
 */
class m220324_145701_migrate_BTS207_bpjs_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."bpjs_t" 
            ADD COLUMN IF NOT EXISTS "no_surat_kontrol" varchar(100) COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "info_response" text COLLATE "pg_catalog"."default";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220324_145701_migrate_BTS207_bpjs_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220324_145701_migrate_BTS207_bpjs_t cannot be reverted.\n";

        return false;
    }
    */
}
