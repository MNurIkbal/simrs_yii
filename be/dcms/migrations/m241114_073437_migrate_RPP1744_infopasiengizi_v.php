<?php

use yii\db\Migration;

/**
 * Class m241114_073437_migrate_RPP1744_infopasiengizi_v
 */
class m241114_073437_migrate_RPP1744_infopasiengizi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasiengizi_v");
        $infopasiengizi_v = file_get_contents(__DIR__ . '/definitions/infopasiengizi_v.sql');
        $this->execute($infopasiengizi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241114_073437_migrate_RPP1744_infopasiengizi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241114_073437_migrate_RPP1744_infopasiengizi_v cannot be reverted.\n";

        return false;
    }
    */
}
