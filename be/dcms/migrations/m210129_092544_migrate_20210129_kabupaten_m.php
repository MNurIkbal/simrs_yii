<?php

use yii\db\Migration;

/**
 * Class m210129_092544_migrate_20210129_kabupaten_m
 */
class m210129_092544_migrate_20210129_kabupaten_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."kabupaten_m" DROP CONSTRAINT IF EXISTS "fk_kabupaten_propinsi";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210129_092544_migrate_20210129_kabupaten_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210129_092544_migrate_20210129_kabupaten_m cannot be reverted.\n";

        return false;
    }
    */
}
