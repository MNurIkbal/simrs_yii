<?php

use yii\db\Migration;

/**
 * Class m210428_075503_migrate_20210428_returpenerimaanbarang_t
 */
class m210428_075503_migrate_20210428_returpenerimaanbarang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."returpenerimaanbarang_t" ADD COLUMN IF NOT exists "penerimaansupp_id" int4;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210428_075503_migrate_20210428_returpenerimaanbarang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210428_075503_migrate_20210428_returpenerimaanbarang_t cannot be reverted.\n";

        return false;
    }
    */
}
