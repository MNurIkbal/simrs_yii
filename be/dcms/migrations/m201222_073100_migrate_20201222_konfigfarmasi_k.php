<?php

use yii\db\Migration;

/**
 * Class m201222_073100_migrate_20201222_konfigfarmasi_k
 */
class m201222_073100_migrate_20201222_konfigfarmasi_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
                $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD IF NOT EXISTS "is_verifstokopname" bool DEFAULT false;');
                $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD IF NOT EXISTS "po_expired" int4;');
                $this->execute('COMMENT ON COLUMN "public"."konfigfarmasi_k"."po_expired" IS \'default jumlah hari po expired setelah verifikasi\';');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201222_073100_migrate_20201222_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201222_073100_migrate_20201222_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }
    */
}
