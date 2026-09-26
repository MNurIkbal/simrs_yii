<?php

use yii\db\Migration;

/**
 * Class m250320_061226_migrate_improve_stokobatlkes_r_fn
 */
class m250320_061226_migrate_improve_stokobatlkes_r_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->execute("DROP FUNCTION IF EXISTS stokobatalkes_r()");
        $query = file_get_contents(__DIR__ . '/definitions/stokobatalkes_r_fn.sql');
        $this->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250320_061226_migrate_improve_stokobatlkes_r_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250320_061226_migrate_improve_stokobatlkes_r_fn cannot be reverted.\n";

        return false;
    }
    */
}
