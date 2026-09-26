<?php

use yii\db\Migration;

/**
 * Class m220727_062209_hotfix_table_tindakanpelayanan_t
 */
class m220727_062209_hotfix_table_tindakanpelayanan_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP INDEX IF EXISTS "public"."tindakanpelayanan_tindakansudahbayar";
        ');

        $this->execute('
            CREATE INDEX "tindakanpelayanan_tindakansudahbayar" ON "public"."tindakanpelayanan_t" USING btree (
              "tindakansudahbayar_id"
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220727_062209_hotfix_table_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220727_062209_hotfix_table_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }
    */
}
