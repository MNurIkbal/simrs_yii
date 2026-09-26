<?php

use yii\db\Migration;

/**
 * Class m231206_040138_migrate_DSV_1010_infopasienpulangrjrd_v
 */
class m231206_040138_migrate_DSV_1010_infopasienpulangrjrd_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienpulangrjrd_v");
        $infopasienpulangrjrd_v = file_get_contents(__DIR__ . '/definitions/infopasienpulangrjrd_v.sql');
        $this->execute($infopasienpulangrjrd_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231206_040138_migrate_DSV_1010_infopasienpulangrjrd_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231206_040138_migrate_DSV_1010_infopasienpulangrjrd_v cannot be reverted.\n";

        return false;
    }
    */
}
