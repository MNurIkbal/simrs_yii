<?php

use yii\db\Migration;

/**
 * Class m240528_021121_migrate_rpp1372_infopasienpulangri_v
 */
class m240528_021121_migrate_rpp1372_infopasienpulangri_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienpulangri_v");
        $infopasienpulangri_v = file_get_contents(__DIR__ . '/definitions/infopasienpulangri_v.sql');
        $this->execute($infopasienpulangri_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240528_021121_migrate_rpp1372_infopasienpulangri_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240528_021121_migrate_rpp1372_infopasienpulangri_v cannot be reverted.\n";

        return false;
    }
    */
}
