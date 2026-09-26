<?php

use yii\db\Migration;

/**
 * Class m210129_093536_migrate_20210129_stokopname_t
 */
class m210129_093536_migrate_20210129_stokopname_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."stokopname_t" ADD COLUMN IF NOT EXISTS "is_verifikasi" bool;');

         $this->execute('ALTER TABLE "public"."stokopname_t" ADD COLUMN IF NOT EXISTS "tglverifikasi" timestamp(0) DEFAULT (\'now\'::text)::date;');

         $this->execute('ALTER TABLE "public"."stokopname_t" ADD COLUMN IF NOT EXISTS "pegawaiverifikasi_id" int4;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210129_093536_migrate_20210129_stokopname_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210129_093536_migrate_20210129_stokopname_t cannot be reverted.\n";

        return false;
    }
    */
}
