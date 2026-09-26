<?php

use yii\db\Migration;

/**
 * Class m200826_051932_migrate_mhkn_20200826_notifikasi_r
 */
class m200826_051932_migrate_mhkn_20200826_notifikasi_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        

         $this->execute('ALTER TABLE "public"."notifikasi_r" ALTER COLUMN "judulnotifikasi" TYPE varchar(250) COLLATE "pg_catalog"."default";');
         

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200826_051932_migrate_mhkn_20200826_notifikasi_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200826_051932_migrate_mhkn_20200826_notifikasi_r cannot be reverted.\n";

        return false;
    }
    */
}
