<?php

use yii\db\Migration;

/**
 * Class m240402_095804_migrate_DSV_1222_infopenerimaansupp_v
 */
class m240402_095804_migrate_DSV_1222_infopenerimaansupp_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopenerimaansupp_v");
        $infopenerimaansupp_v = file_get_contents(__DIR__ . '/definitions/infopenerimaansupp_v.sql');
        $this->execute($infopenerimaansupp_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240402_095804_migrate_DSV_1222_infopenerimaansupp_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240402_095804_migrate_DSV_1222_infopenerimaansupp_v cannot be reverted.\n";

        return false;
    }
    */
}
