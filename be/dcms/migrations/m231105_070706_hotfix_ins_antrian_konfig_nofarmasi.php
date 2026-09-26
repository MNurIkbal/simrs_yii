<?php

use yii\db\Migration;

/**
 * Class m231105_070706_hotfix_ins_antrian_konfig_nofarmasi
 */
class m231105_070706_hotfix_ins_antrian_konfig_nofarmasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $ins_noantrian_konfig = file_get_contents(__DIR__ . '/definitions/ins_noantrian_konfig.fn.sql');
        $this->execute($ins_noantrian_konfig);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231105_070706_hotfix_ins_antrian_konfig_nofarmasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231105_070706_hotfix_ins_antrian_konfig_nofarmasi cannot be reverted.\n";

        return false;
    }
    */
}
