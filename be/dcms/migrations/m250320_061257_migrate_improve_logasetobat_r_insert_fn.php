<?php

use yii\db\Migration;

/**
 * Class m250320_061257_migrate_improve_logasetobat_r_insert_fn
 */
class m250320_061257_migrate_improve_logasetobat_r_insert_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->execute("DROP FUNCTION IF EXISTS logasetobat_r_insert()");
        $query = file_get_contents(__DIR__ . '/definitions/logasetobat_r_insert_fn.sql');
        $this->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250320_061257_migrate_improve_logasetobat_r_insert_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250320_061257_migrate_improve_logasetobat_r_insert_fn cannot be reverted.\n";

        return false;
    }
    */
}
