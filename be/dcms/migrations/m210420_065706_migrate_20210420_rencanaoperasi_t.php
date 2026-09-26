<?php

use yii\db\Migration;

/**
 * Class m210420_065706_migrate_20210420_rencanaoperasi_t
 */
class m210420_065706_migrate_20210420_rencanaoperasi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
  $this->execute('ALTER TABLE "public"."rencanaoperasi_t" ADD COLUMN IF NOT exists "pemakaian_implant" varchar(255) COLLATE "pg_catalog"."default";');

$this->execute('ALTER TABLE "public"."rencanaoperasi_t" ADD COLUMN IF NOT exists "sewa_alat_rs" varchar(255) COLLATE "pg_catalog"."default";');

$this->execute('ALTER TABLE "public"."rencanaoperasi_t" ADD COLUMN IF NOT exists "jenis_operasi_cyto" bool;');

$this->execute('ALTER TABLE "public"."rencanaoperasi_t" ADD COLUMN IF NOT exists "jenis_operasi_elektif" bool;');

$this->execute('ALTER TABLE "public"."rencanaoperasi_t" ADD COLUMN IF NOT exists "jenis_operasi_odc" bool;');

$this->execute('ALTER TABLE "public"."rencanaoperasi_t" ADD COLUMN IF NOT exists "sewa_vendor" varchar(255) COLLATE "pg_catalog"."default";');
$this->execute('ALTER TABLE "public"."rencanaoperasi_t" ADD COLUMN IF NOT exists "catatan_klinis" text COLLATE "pg_catalog"."default";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210420_065706_migrate_20210420_rencanaoperasi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210420_065706_migrate_20210420_rencanaoperasi_t cannot be reverted.\n";

        return false;
    }
    */
}
