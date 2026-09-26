<?php

use yii\db\Migration;

/**
 * Class m210129_093931_migrate_20210129_pemeriksaanpelengkap_t
 */
class m210129_093931_migrate_20210129_pemeriksaanpelengkap_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."pemeriksaanpelengkap_t" ADD COLUMN IF NOT EXISTS "ruangan_id" int4;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210129_093931_migrate_20210129_pemeriksaanpelengkap_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210129_093931_migrate_20210129_pemeriksaanpelengkap_t cannot be reverted.\n";

        return false;
    }
    */
}
