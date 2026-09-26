<?php

use yii\db\Migration;

/**
 * Class m230920_045715_glbj_247_soaprs_v
 */
class m230920_045715_glbj_247_soaprs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS soaprs_v");
        $soaprs_v = file_get_contents(__DIR__ . '/definitions/soaprs_v.view.sql');
        $this->execute($soaprs_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230920_045715_glbj_247_soaprs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230920_045715_glbj_247_soaprs_v cannot be reverted.\n";

        return false;
    }
    */
}
