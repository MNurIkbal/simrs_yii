<?php

use yii\db\Migration;

/**
 * Class m210226_082043_migrate_20210226_lookuptransaksi_m
 */
class m210226_082043_migrate_20210226_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."lookuptransaksi_m" ADD COLUMN IF NOT EXISTS "additional_value" text COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_082043_migrate_20210226_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_082043_migrate_20210226_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
