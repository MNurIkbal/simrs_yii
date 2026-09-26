<?php

use yii\db\Migration;

/**
 * Class m211130_023037_migrate_pembayaran_t
 */
class m211130_023037_migrate_pembayaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pembayaran_t" 
                            ADD COLUMN if not exists "alasan_batal" text COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211130_023037_migrate_pembayaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211130_023037_migrate_pembayaran_t cannot be reverted.\n";

        return false;
    }
    */
}
