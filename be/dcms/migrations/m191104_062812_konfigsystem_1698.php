<?php

use yii\db\Migration;

/**
 * Class m191104_062812_konfigsystem_1698
 */
class m191104_062812_konfigsystem_1698 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
                      ADD COLUMN "kelas_pelayanan" varchar(255) COLLATE "pg_catalog"."default",
                      ADD COLUMN "pembayaran_langsung" bool;');

        $this->execute('COMMENT ON COLUMN "public"."konfigsystem_k"."kelas_pelayanan" IS \'Kebutuhan Untuk Pendaftaran RJ,RD dan Penunjang\';');

        $this->execute('COMMENT ON COLUMN "public"."konfigsystem_k"."pembayaran_langsung" IS \'Kebutuhan Untuk Pembayaran Langsung di pendaftaran\';');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191104_062812_konfigsystem_1698 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191104_062812_konfigsystem_1698 cannot be reverted.\n";

        return false;
    }
    */
}
