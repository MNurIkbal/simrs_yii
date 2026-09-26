<?php

use yii\db\Migration;

/**
 * Class m220331_160905_migrate_DHC308_table_timoperasi_t
 */
class m220331_160905_migrate_DHC308_table_timoperasi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE timoperasi_t ADD IF NOT EXISTS kegiatanoperasi_id int4;
        ');

        $this->execute('
            ALTER TABLE timoperasi_t ADD IF NOT EXISTS golonganoperasi_id int4;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220331_160905_migrate_DHC308_table_timoperasi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220331_160905_migrate_DHC308_table_timoperasi_t cannot be reverted.\n";

        return false;
    }
    */
}
