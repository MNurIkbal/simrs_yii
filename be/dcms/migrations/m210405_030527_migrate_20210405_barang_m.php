<?php

use yii\db\Migration;

/**
 * Class m210405_030527_migrate_20210405_barang_m
 */
class m210405_030527_migrate_20210405_barang_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('ALTER TABLE "public"."barang_m" ADD COLUMN if not exists "manufaktur_id" int4;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210405_030527_migrate_20210405_barang_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210405_030527_migrate_20210405_barang_m cannot be reverted.\n";

        return false;
    }
    */
}
