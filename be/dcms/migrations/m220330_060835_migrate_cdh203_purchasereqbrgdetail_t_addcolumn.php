<?php

use yii\db\Migration;

/**
 * Class m220330_060835_migrate_cdh203_purchasereqbrgdetail_t_addcolumn
 */
class m220330_060835_migrate_cdh203_purchasereqbrgdetail_t_addcolumn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE purchasereqbrgdetail_t ADD IF NOT EXISTS stok_gudang numeric;
        ');
        $this->execute('
            ALTER TABLE purchasereqbrgdetail_t ADD IF NOT EXISTS stok_ruanganlain numeric;
        ');
        $this->execute('
            ALTER TABLE purchasereqbrgdetail_t ADD IF NOT EXISTS last_7 numeric;
        ');
        $this->execute('
            ALTER TABLE purchasereqbrgdetail_t ADD IF NOT EXISTS last_14 numeric;
        ');
        $this->execute('
            ALTER TABLE purchasereqbrgdetail_t ADD IF NOT EXISTS last_30 numeric;
        ');
        $this->execute('
            ALTER TABLE purchasereqbrgdetail_t ADD IF NOT EXISTS qty_outstanding numeric;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220330_060835_migrate_cdh203_purchasereqbrgdetail_t_addcolumn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220330_060835_migrate_cdh203_purchasereqbrgdetail_t_addcolumn cannot be reverted.\n";

        return false;
    }
    */
}
