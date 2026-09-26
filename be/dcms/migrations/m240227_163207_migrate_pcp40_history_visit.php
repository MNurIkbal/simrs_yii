<?php

use yii\db\Migration;

/**
 * Class m240227_163207_migrate_pcp40_history_visit
 */
class m240227_163207_migrate_pcp40_history_visit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienrs_v");
        $infopasienrs_v = file_get_contents(__DIR__ . '/definitions/infopasienrs_v.sql');
        $this->execute($infopasienrs_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240227_163207_migrate_pcp40_history_visit cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240227_163207_migrate_pcp40_history_visit cannot be reverted.\n";

        return false;
    }
    */
}
