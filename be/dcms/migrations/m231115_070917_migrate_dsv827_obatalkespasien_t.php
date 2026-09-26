<?php

use yii\db\Migration;

/**
 * Class m231115_070917_migrate_dsv827_obatalkespasien_t
 */
class m231115_070917_migrate_dsv827_obatalkespasien_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE obatalkespasien_t 
            ADD IF NOT EXISTS is_retur bool DEFAULT false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231115_070917_migrate_dsv827_obatalkespasien_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231115_070917_migrate_dsv827_obatalkespasien_t cannot be reverted.\n";

        return false;
    }
    */
}
