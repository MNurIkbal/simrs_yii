<?php

use yii\db\Migration;

/**
 * Class m240322_235817_hotfix_akunting_viewsaleorderupdate
 */
class m240322_235817_hotfix_akunting_viewsaleorderupdate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS newodoo_saleorderupdate_v");
        $newodoo_saleorderupdate_v = file_get_contents(__DIR__ . '/definitions/newodoo_saleorderupdate_v.sql');
        $this->execute($newodoo_saleorderupdate_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240322_235817_hotfix_akunting_viewsaleorderupdate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240322_235817_hotfix_akunting_viewsaleorderupdate cannot be reverted.\n";

        return false;
    }
    */
}
