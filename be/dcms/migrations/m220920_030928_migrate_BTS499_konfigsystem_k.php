<?php

use yii\db\Migration;

/**
 * Class m220920_030928_migrate_BTS499_konfigsystem_k
 */
class m220920_030928_migrate_BTS499_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN IF NOT EXISTS "is_validasi_pembayaran" bool DEFAULT false;');
        $this->execute('COMMENT ON COLUMN "public"."konfigsystem_k"."is_validasi_pembayaran" IS \'Kebutuhan untuk validasi no pendaftaran yang sudah melakukan pembayaran\';');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220920_030928_migrate_BTS499_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220920_030928_migrate_BTS499_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
