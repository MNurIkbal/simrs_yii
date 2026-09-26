<?php

use yii\db\Migration;

/**
 * Class m231115_070905_migrate_dsv827_resepturdetail_t
 */
class m231115_070905_migrate_dsv827_resepturdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE resepturdetail_t 
            ADD IF NOT EXISTS is_retur bool DEFAULT false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231115_070905_migrate_dsv827_resepturdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231115_070905_migrate_dsv827_resepturdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
