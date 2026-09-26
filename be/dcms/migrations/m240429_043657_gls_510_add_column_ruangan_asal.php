<?php

use yii\db\Migration;

/**
 * Class m240429_043657_gls_510_add_column_ruangan_asal
 */
class m240429_043657_gls_510_add_column_ruangan_asal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasienkirimkeunitlain_t" ADD COLUMN if not exists "ruangan_asal" int4 null;');

        $this->execute('ALTER TABLE "public"."permintaanmakan_t" ADD COLUMN if not exists "ruangan_asal" int4 null;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240429_043657_gls_510_add_column_ruangan_asal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240429_043657_gls_510_add_column_ruangan_asal cannot be reverted.\n";

        return false;
    }
    */
}
