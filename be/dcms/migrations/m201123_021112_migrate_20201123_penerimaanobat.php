<?php

use yii\db\Migration;

/**
 * Class m201123_021112_migrate_20201123_penerimaanobat
 */
class m201123_021112_migrate_20201123_penerimaanobat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."penerimaanobat_t" 
                        ALTER COLUMN "no_suratjalan" DROP NOT NULL;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201123_021112_migrate_20201123_penerimaanobat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201123_021112_migrate_20201123_penerimaanobat cannot be reverted.\n";

        return false;
    }
    */
}
