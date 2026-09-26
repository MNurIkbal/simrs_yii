<?php

use yii\db\Migration;

/**
 * Class m220727_062226_hotfix_table_obatalkespasien_t
 */
class m220727_062226_hotfix_table_obatalkespasien_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP INDEX IF EXISTS "public"."obatalkespasien_obatsudahbayar";
        ');

        $this->execute('
            CREATE INDEX "obatalkespasien_obatsudahbayar" ON "public"."obatalkespasien_t" USING btree (
              "obatsudahbayar_id"
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220727_062226_hotfix_table_obatalkespasien_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220727_062226_hotfix_table_obatalkespasien_t cannot be reverted.\n";

        return false;
    }
    */
}
