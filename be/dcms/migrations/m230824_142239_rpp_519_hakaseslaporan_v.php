<?php

use yii\db\Migration;

/**
 * Class m230824_142239_rpp_519_hakaseslaporan_v
 */
class m230824_142239_rpp_519_hakaseslaporan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS hakakseslaporan_v");
        $hakakseslaporan_v = file_get_contents(__DIR__ . '/definitions/hakakseslaporan_v.view.sql');
        $this->execute($hakakseslaporan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230824_142239_rpp_519_hakaseslaporan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230824_142239_rpp_519_hakaseslaporan_v cannot be reverted.\n";

        return false;
    }
    */
}
