<?php

use yii\db\Migration;

/**
 * Class m240603_113024_migrate_hotfix_ketersediaanobat_v_non_aproval
 */
class m240603_113024_migrate_hotfix_ketersediaanobat_v_non_aproval extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS ketersediaanobat_v");
        $ketersediaanobat_v = file_get_contents(__DIR__ . '/definitions/ketersediaanobat_v.sql');
        $this->execute($ketersediaanobat_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240603_113024_migrate_hotfix_ketersediaanobat_v_non_aproval cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240603_113024_migrate_hotfix_ketersediaanobat_v_non_aproval cannot be reverted.\n";

        return false;
    }
    */
}
