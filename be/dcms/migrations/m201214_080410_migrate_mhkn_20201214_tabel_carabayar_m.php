<?php

use yii\db\Migration;

/**
 * Class m201214_080410_migrate_mhkn_20201214_tabel_carabayar_m
 */
class m201214_080410_migrate_mhkn_20201214_tabel_carabayar_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."carabayar_m" ADD COLUMN IF NOT EXISTS"carabayar_warna" varchar(255) COLLATE "pg_catalog"."default";');
        
        $this->execute('ALTER TABLE "public"."carabayar_m" ADD COLUMN IF NOT EXISTS"carabayar_kode_warna" varchar(255) COLLATE "pg_catalog"."default";
                ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201214_080410_migrate_mhkn_20201214_tabel_carabayar_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201214_080410_migrate_mhkn_20201214_tabel_carabayar_m cannot be reverted.\n";

        return false;
    }
    */
}
