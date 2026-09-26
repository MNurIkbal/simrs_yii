<?php

use yii\db\Migration;

/**
 * Class m220118_235753_migrate_US2613_spesialisasidokter
 */
class m220118_235753_migrate_US2613_spesialisasidokter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."spesialis_m" 
          ADD COLUMN IF NOT EXISTS "spesialis_namalainnya" varchar(255) COLLATE "pg_catalog"."default";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220118_235753_migrate_US2613_spesialisasidokter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220118_235753_migrate_US2613_spesialisasidokter cannot be reverted.\n";

        return false;
    }
    */
}
