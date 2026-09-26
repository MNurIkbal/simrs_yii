<?php

use yii\db\Migration;

/**
 * Class m240214_183645_pcp10_migrate_informasiresep_v
 */
class m240214_183645_pcp10_migrate_informasiresep_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS informasiresep_v");
        $sql = file_get_contents(__DIR__ . '/definitions/informasiresep_v.view.sql');
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240214_183645_pcp10_migrate_informasiresep_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240214_183645_pcp10_migrate_informasiresep_v cannot be reverted.\n";

        return false;
    }
    */
}
