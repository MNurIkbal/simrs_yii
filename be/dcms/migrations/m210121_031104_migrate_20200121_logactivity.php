<?php

use yii\db\Migration;

/**
 * Class m210121_031104_migrate_20200121_logactivity
 */
class m210121_031104_migrate_20200121_logactivity extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."logactivity_r" ALTER IF NOT EXISTS "tgl" TYPE timestamp(0) USING "tgl"::timestamp(0);');

        $this->execute('ALTER TABLE "public"."logactivity_r" ALTER IF NOT EXISTS "tgl" SET DEFAULT (\'now\'::text)::date;');

        $this->execute('ALTER TABLE "public"."logactivity_r" ADD IF NOT EXISTS "transaksi_id" int4;');

        $this->execute('ALTER TABLE "public"."logactivity_r" ADD IF NOT EXISTS "additional_detail" json;');
       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210121_031104_migrate_20200121_logactivity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210121_031104_migrate_20200121_logactivity cannot be reverted.\n";

        return false;
    }
    */
}
