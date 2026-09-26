<?php

use yii\db\Migration;

/**
 * Class m210409_032618_migrate_20210409_validasipobarang_t
 */
class m210409_032618_migrate_20210409_validasipobarang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."validasipobarang_t" ADD COLUMN IF NOT exists "catatan" text COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210409_032618_migrate_20210409_validasipobarang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210409_032618_migrate_20210409_validasipobarang_t cannot be reverted.\n";

        return false;
    }
    */
}
