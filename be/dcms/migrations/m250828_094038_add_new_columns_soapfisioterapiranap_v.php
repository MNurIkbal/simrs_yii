<?php

use yii\db\Migration;

/**
 * Class m250828_094038_add_new_columns_soapfisioterapiranap_v
 */
class m250828_094038_add_new_columns_soapfisioterapiranap_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS soapfisioterapiranap_v");
        $soapfisioterapiranap_v = file_get_contents(__DIR__ . '/definitions/add_new_columns_on_soapfisioterapiranap_v.sql');
        $this->execute($soapfisioterapiranap_v);
        $this->execute('ALTER TABLE "public"."soapfisioterapiranap_v" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250828_094038_add_new_columns_soapfisioterapiranap_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250828_094038_add_new_columns_soapfisioterapiranap_v cannot be reverted.\n";

        return false;
    }
    */
}
