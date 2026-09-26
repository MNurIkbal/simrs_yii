<?php

use yii\db\Migration;

/**
 * Class m220727_062232_hotfix_table_obatalkespasien_r
 */
class m220727_062232_hotfix_table_obatalkespasien_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP INDEX IF EXISTS "public"."obatalkespasien_r_obatsudahbayar";
        ');

        $this->execute('
            CREATE INDEX "obatalkespasien_r_obatsudahbayar" ON "public"."obatalkespasien_r" USING btree (
              "obatsudahbayar_id"
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220727_062232_hotfix_table_obatalkespasien_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220727_062232_hotfix_table_obatalkespasien_r cannot be reverted.\n";

        return false;
    }
    */
}
