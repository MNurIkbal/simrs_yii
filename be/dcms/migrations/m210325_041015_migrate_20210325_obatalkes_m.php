<?php

use yii\db\Migration;

/**
 * Class m210325_041015_migrate_20210325_obatalkes_m
 */
class m210325_041015_migrate_20210325_obatalkes_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD COLUMN IF NOT exists "zataktif_id" int4;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210325_041015_migrate_20210325_obatalkes_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210325_041015_migrate_20210325_obatalkes_m cannot be reverted.\n";

        return false;
    }
    */
}
