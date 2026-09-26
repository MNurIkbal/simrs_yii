<?php

use yii\db\Migration;

/**
 * Class m241105_084550_migrate_GLBJ669_infopasienlab_v
 */
class m241105_084550_migrate_GLBJ669_infopasienlab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienlab_v");
        $infopasienlab_v = file_get_contents(__DIR__ . '/definitions/infopasienlab_v.sql');
        $this->execute($infopasienlab_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241105_084550_migrate_GLBJ669_infopasienlab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241105_084550_migrate_GLBJ669_infopasienlab_v cannot be reverted.\n";

        return false;
    }
    */
}
