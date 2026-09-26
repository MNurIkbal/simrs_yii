<?php

use yii\db\Migration;

/**
 * Class m220511_060227_migrate_reseptur_t
 */
class m220511_060227_migrate_reseptur_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."reseptur_t" 
                            ADD COLUMN if not exists "alasan_batal" text COLLATE "pg_catalog"."default";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220511_060227_migrate_reseptur_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220511_060227_migrate_reseptur_t cannot be reverted.\n";

        return false;
    }
    */
}
