<?php

use yii\db\Migration;

/**
 * Class m250828_094047_add_new_columns_soapfisioterapi_v
 */
class m250828_094047_add_new_columns_soapfisioterapi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS soapfisioterapi_v");
        $soapfisioterapi_v = file_get_contents(__DIR__ . '/definitions/add_new_columns_on_soapfisioterapi_v.sql');
        $this->execute($soapfisioterapi_v);
        $this->execute('ALTER TABLE "public"."soapfisioterapi_v" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250828_094047_add_new_columns_soapfisioterapi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250828_094047_add_new_columns_soapfisioterapi_v cannot be reverted.\n";

        return false;
    }
    */
}
