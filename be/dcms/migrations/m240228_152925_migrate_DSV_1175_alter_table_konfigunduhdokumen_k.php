<?php

use yii\db\Migration;

/**
 * Class m240228_152925_migrate_DSV_1175_alter_table_konfigunduhdokumen_k
 */
class m240228_152925_migrate_DSV_1175_alter_table_konfigunduhdokumen_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigunduhdokumen_k" 
            ADD COLUMN IF NOT EXISTS "is_eclaim" bool NOT NULL DEFAULT false,
            ADD COLUMN IF NOT EXISTS "is_esign" bool NOT NULL DEFAULT false,
            ADD COLUMN IF NOT EXISTS "additional_data" text;
        ');

        $this->execute('UPDATE "public"."konfigunduhdokumen_k" 
            SET "is_eclaim" = true;
        ');

        $this->execute('ALTER TABLE "public"."pegawai_m"
            ADD COLUMN IF NOT EXISTS "useresign_id" varchar(50),
            ADD COLUMN IF NOT EXISTS "additional_esign_data" text;
            ;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240228_152925_migrate_DSV_1175_alter_table_konfigunduhdokumen_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240228_152925_migrate_DSV_1175_alter_table_konfigunduhdokumen_k cannot be reverted.\n";

        return false;
    }
    */
}
