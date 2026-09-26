<?php

use yii\db\Migration;

/**
 * Class m220613_092027_migrate_MHG2523_table_instrumenoperasi_t
 */
class m220613_092027_migrate_MHG2523_table_instrumenoperasi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE instrumenoperasi_t ADD IF NOT EXISTS daftartindakan_id int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220613_092027_migrate_MHG2523_table_instrumenoperasi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220613_092027_migrate_MHG2523_table_instrumenoperasi_t cannot be reverted.\n";

        return false;
    }
    */
}
