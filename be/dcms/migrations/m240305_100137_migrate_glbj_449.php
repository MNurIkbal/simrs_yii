<?php

use yii\db\Migration;

/**
 * Class m240305_100137_migrate_glbj_449
 */
class m240305_100137_migrate_glbj_449 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS soaprj_v");
        $soaprj_v = file_get_contents(__DIR__ . '/definitions/soaprj_v.sql');
        $this->execute($soaprj_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240305_100137_migrate_glbj_449 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240305_100137_migrate_glbj_449 cannot be reverted.\n";

        return false;
    }
    */
}
