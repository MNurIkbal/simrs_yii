<?php

use yii\db\Migration;

/**
 * Class m240109_065050_migrate_dsv1037_surat_keterangan_m
 */
class m240109_065050_migrate_dsv1037_surat_keterangan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."surat_keterangan_m" ADD IF NOT EXISTS "code_report" varchar(75);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240109_065050_migrate_dsv1037_surat_keterangan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240109_065050_migrate_dsv1037_surat_keterangan_m cannot be reverted.\n";

        return false;
    }
    */
}
