<?php

use yii\db\Migration;

/**
 * Class m241112_033519_migrate_PCP41_laporankonsultindakan_v
 */
class m241112_033519_migrate_PCP41_laporankonsultindakan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporankonsultindakan_v");
        $laporankonsultindakan_v = file_get_contents(__DIR__ . '/definitions/laporankonsultindakan_v.sql');
        $this->execute($laporankonsultindakan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241112_033519_migrate_PCP41_laporankonsultindakan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241112_033519_migrate_PCP41_laporankonsultindakan_v cannot be reverted.\n";

        return false;
    }
    */
}
