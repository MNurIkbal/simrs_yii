<?php

use yii\db\Migration;

/**
 * Class m230914_135822_migrate_antrian_v
 */
class m230914_135822_migrate_antrian_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS antrian_v");
        $antrian_v = file_get_contents(__DIR__ . '/definitions/antrian_v.sql');
        $this->execute($antrian_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230914_135822_migrate_antrian_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230914_135822_migrate_antrian_v cannot be reverted.\n";

        return false;
    }
    */
}
