<?php

use yii\db\Migration;

/**
 * Class m211115_060007_migrate_US2115_reservasipasienbpjs
 */
class m211115_060007_migrate_US2115_reservasipasienbpjs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pendaftaranol_t" 
          ADD COLUMN IF NOT EXISTS "no_bpjs" varchar(255) COLLATE "pg_catalog"."default";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211115_060007_migrate_US2115_reservasipasienbpjs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211115_060007_migrate_US2115_reservasipasienbpjs cannot be reverted.\n";

        return false;
    }
    */
}
