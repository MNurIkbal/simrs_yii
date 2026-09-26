<?php

use yii\db\Migration;

/**
 * Class m250812_063247_migrate_hotfix_function_new_basecalrofn
 */
class m250812_063247_migrate_hotfix_function_new_basecalrofn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS new_basecalrofn");
        $infopasienri_v = file_get_contents(__DIR__ . '/definitions/new_basecalrofn.sql');
        $this->execute($infopasienri_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250812_063247_migrate_hotfix_function_new_basecalrofn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250812_063247_migrate_hotfix_function_new_basecalrofn cannot be reverted.\n";

        return false;
    }
    */
}
