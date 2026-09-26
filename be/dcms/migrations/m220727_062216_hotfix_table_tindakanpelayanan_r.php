<?php

use yii\db\Migration;

/**
 * Class m220727_062216_hotfix_table_tindakanpelayanan_r
 */
class m220727_062216_hotfix_table_tindakanpelayanan_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP INDEX IF EXISTS "public"."tindakanpelayanan_r_tindakansudahbayar";
        ');

        $this->execute('
            CREATE INDEX "tindakanpelayanan_r_tindakansudahbayar" ON "public"."tindakanpelayanan_r" USING btree (
              "tindakansudahbayar_id"
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220727_062216_hotfix_table_tindakanpelayanan_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220727_062216_hotfix_table_tindakanpelayanan_r cannot be reverted.\n";

        return false;
    }
    */
}
