<?php

use yii\db\Migration;

/**
 * Class m250828_094059_add_new_columns_soaprs_v
 */
class m250828_094059_add_new_columns_soaprs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS soaprs_v");
        $soaprs_v = file_get_contents(__DIR__ . '/definitions/add_new_columns_on_soaprs_v.sql');
        $this->execute($soaprs_v);
        $this->execute('ALTER TABLE "public"."soaprs_v" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250828_094059_add_new_columns_soaprs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250828_094059_add_new_columns_soaprs_v cannot be reverted.\n";

        return false;
    }
    */
}
