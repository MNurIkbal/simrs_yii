<?php

use yii\db\Migration;

/**
 * Class m231227_150752_migrate_GLS247_infopasienradiologi_v
 */
class m231227_150752_migrate_GLS247_infopasienradiologi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienradiologi_v");
        $infopasienradiologi_v = file_get_contents(__DIR__ . '/definitions/infopasienradiologi_v.sql');
        $this->execute($infopasienradiologi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231227_150752_migrate_GLS247_infopasienradiologi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231227_150752_migrate_GLS247_infopasienradiologi_v cannot be reverted.\n";

        return false;
    }
    */
}
