<?php

use yii\db\Migration;

/**
 * Class m201216_121339_migrate_mhkn_20201216_tabel_konfigsystem_k
 */
class m201216_121339_migrate_mhkn_20201216_tabel_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
  ADD COLUMN IF NOT EXISTS "is_nourut" bool NOT NULL DEFAULT false;');

        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
  ADD COLUMN IF NOT EXISTS "jenis_label_pendaftaran" text COLLATE "pg_catalog"."default";
            ');

        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
  ADD COLUMN IF NOT EXISTS "jumlah_label_pendaftaran" int2;
            ');

        $this->execute('ALTER TABLE "public"."pendaftaran_t" 
  ADD COLUMN IF NOT EXISTS "limit_tagihan" float8 DEFAULT 0;
            ');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201216_121339_migrate_mhkn_20201216_tabel_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201216_121339_migrate_mhkn_20201216_tabel_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
