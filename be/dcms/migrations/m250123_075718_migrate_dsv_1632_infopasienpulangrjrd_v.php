<?php

use yii\db\Migration;

/**
 * Class m250123_075718_migrate_dsv_1632_infopasienpulangrjrd_v
 */
class m250123_075718_migrate_dsv_1632_infopasienpulangrjrd_v extends Migration
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
        echo "m250123_075718_migrate_dsv_1632_infopasienpulangrjrd_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250123_075718_migrate_dsv_1632_infopasienpulangrjrd_v cannot be reverted.\n";

        return false;
    }
    */
}
