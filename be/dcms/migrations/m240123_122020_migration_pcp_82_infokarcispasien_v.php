<?php

use yii\db\Migration;

/**
 * Class m240123_122020_migration_pcp_82_infokarcispasien_v
 */
class m240123_122020_migration_pcp_82_infokarcispasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienkarcis_v");
        $infopasienkarcis_v = file_get_contents(__DIR__ . '/definitions/infopasienkarcis_v.sql');
        $this->execute($infopasienkarcis_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240123_122020_migration_pcp_82_infokarcispasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240123_122020_migration_pcp_82_infokarcispasien_v cannot be reverted.\n";

        return false;
    }
    */
}
