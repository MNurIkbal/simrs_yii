<?php

use yii\db\Migration;

/**
 * Class m240430_024539_migrate_dsv1191_konfigunduhdokumen_k
 */
class m240430_024539_migrate_dsv1191_konfigunduhdokumen_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigunduhdokumen_k" ADD COLUMN if not exists "is_eclaim" boolean DEFAULT false;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240430_024539_migrate_dsv1191_konfigunduhdokumen_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240430_024539_migrate_dsv1191_konfigunduhdokumen_k cannot be reverted.\n";

        return false;
    }
    */
}
