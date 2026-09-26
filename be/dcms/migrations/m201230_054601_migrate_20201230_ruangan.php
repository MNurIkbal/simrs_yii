<?php

use yii\db\Migration;

/**
 * Class m201230_054601_migrate_20201230_ruangan
 */
class m201230_054601_migrate_20201230_ruangan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('ALTER TABLE "public"."ruangan_m"  ADD COLUMN IF not exists "jenis_ruangan" varchar(30) COLLATE "pg_catalog"."default";');
    $this->execute('ALTER TABLE "public"."ruangan_m"  ADD COLUMN IF not exists "is_store" bool DEFAULT false;');
    $this->execute('ALTER TABLE "public"."ruangan_m"  ADD COLUMN IF not exists "is_mainstore" bool DEFAULT false;');
    $this->execute('ALTER TABLE "public"."ruangan_m"  ADD COLUMN IF not exists "is_substore" bool DEFAULT false;');
    $this->execute('ALTER TABLE "public"."ruangan_m"  ADD COLUMN IF not exists "is_cartstore" bool DEFAULT false;');
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201230_054601_migrate_20201230_ruangan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201230_054601_migrate_20201230_ruangan cannot be reverted.\n";

        return false;
    }
    */
}
