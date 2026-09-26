<?php

use yii\db\Migration;

/**
 * Class m200918_064427_migrate_20200918_pemeriksaanfisikmcu
 */
class m200918_064427_migrate_20200918_pemeriksaanfisikmcu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('ALTER TABLE "public"."pemeriksaanfisikmcu_t" ADD IF NOT EXISTS "imt" float4;');
    $this->execute('ALTER TABLE "public"."pemeriksaanfisikmcu_t" ADD IF NOT EXISTS "kategori_bb" text COLLATE "pg_catalog"."default";');
    $this->execute('ALTER TABLE "public"."pemeriksaanfisikmcu_t" ADD IF NOT EXISTS "td_kategori" text COLLATE "pg_catalog"."default";');
    }
 
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200918_064427_migrate_20200918_pemeriksaanfisikmcu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200918_064427_migrate_20200918_pemeriksaanfisikmcu cannot be reverted.\n";

        return false;
    }
    */
}
