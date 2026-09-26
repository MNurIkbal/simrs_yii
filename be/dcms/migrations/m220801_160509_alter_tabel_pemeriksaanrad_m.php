<?php

use yii\db\Migration;

/**
 * Class m220801_160509_alter_tabel_pemeriksaanrad_m
 */
class m220801_160509_alter_tabel_pemeriksaanrad_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."pemeriksaanrad_m"
            ADD COLUMN IF NOT EXISTS "modalitytype_id" int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_160509_alter_tabel_pemeriksaanrad_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_160509_alter_tabel_pemeriksaanrad_m cannot be reverted.\n";

        return false;
    }
    */
}
