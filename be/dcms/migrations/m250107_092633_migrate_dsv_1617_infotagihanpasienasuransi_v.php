<?php

use yii\db\Migration;

/**
 * Class m250107_092633_migrate_dsv_1617_infotagihanpasienasuransi_v
 */
class m250107_092633_migrate_dsv_1617_infotagihanpasienasuransi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infotagihanpasienasuransi_v");
        $infotagihanpasienasuransi_v = file_get_contents(__DIR__ . '/definitions/infotagihanpasienasuransi_v.sql');
        $this->execute($infotagihanpasienasuransi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250107_092633_migrate_dsv_1617_infotagihanpasienasuransi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250107_092633_migrate_dsv_1617_infotagihanpasienasuransi_v cannot be reverted.\n";

        return false;
    }
    */
}
