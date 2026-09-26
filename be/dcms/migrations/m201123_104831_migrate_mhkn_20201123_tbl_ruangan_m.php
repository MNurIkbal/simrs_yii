<?php

use yii\db\Migration;

/**
 * Class m201123_104831_migrate_mhkn_20201123_tbl_ruangan_m
 */
class m201123_104831_migrate_mhkn_20201123_tbl_ruangan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->execute('ALTER TABLE "public"."ruangan_m" ADD COLUMN IF NOT EXISTS"jenis_ruangan" varchar(30) COLLATE "pg_catalog"."default";
                ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201123_104831_migrate_mhkn_20201123_tbl_ruangan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201123_104831_migrate_mhkn_20201123_tbl_ruangan_m cannot be reverted.\n";

        return false;
    }
    */
}
