<?php

use yii\db\Migration;

/**
 * Class m240418_160055_hotfix_pcp82_konfigsystem
 */
class m240418_160055_hotfix_pcp82_konfigsystem extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN if not exists "is_kasir_validasi_stop_akomodasi" boolean DEFAULT false;');

        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN if not exists "is_kasir_validasi_status_pulang" boolean DEFAULT false;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240418_160055_hotfix_pcp82_konfigsystem cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240418_160055_hotfix_pcp82_konfigsystem cannot be reverted.\n";

        return false;
    }
    */
}
