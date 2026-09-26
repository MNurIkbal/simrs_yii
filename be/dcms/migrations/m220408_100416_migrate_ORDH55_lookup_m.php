<?php

use yii\db\Migration;

/**
 * Class m220408_100416_migrate_ORDH55_lookup_m
 */
class m220408_100416_migrate_ORDH55_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE FROM lookup_m WHERE lookup_id = 1148;');
        $this->execute("INSERT INTO lookup_m (lookup_id, lookup_type, lookup_name, lookup_value) VALUES (1148, 'status_periksa', 'Selesai', 'Selesai');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_100416_migrate_ORDH55_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_100416_migrate_ORDH55_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
