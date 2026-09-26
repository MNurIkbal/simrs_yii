<?php

use yii\db\Migration;

/**
 * Class m221212_075343_migrate_GB247_table_bayaruangmuka_t
 */
class m221212_075343_migrate_GB247_table_bayaruangmuka_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE bayaruangmuka_t ADD IF NOT EXISTS is_tunai BOOLEAN DEFAULT TRUE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221212_075343_migrate_GB247_table_bayaruangmuka_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221212_075343_migrate_GB247_table_bayaruangmuka_t cannot be reverted.\n";

        return false;
    }
    */
}
