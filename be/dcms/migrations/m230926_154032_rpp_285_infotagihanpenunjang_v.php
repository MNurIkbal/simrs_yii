<?php

use yii\db\Migration;

/**
 * Class m230926_154032_rpp_285_infotagihanpenunjang_v
 */
class m230926_154032_rpp_285_infotagihanpenunjang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infotagihanpenunjang_v");   
        $infotagihanpenunjang_v = file_get_contents(__DIR__ . '/definitions/infotagihanpenunjang_v.view.sql');
        $this->execute($infotagihanpenunjang_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230926_154032_rpp_285_infotagihanpenunjang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230926_154032_rpp_285_infotagihanpenunjang_v cannot be reverted.\n";

        return false;
    }
    */
}
