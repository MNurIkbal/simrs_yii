<?php

use yii\db\Migration;

/**
 * Class m250423_023219_migrate_dsv_1691_update_fn_hapus_kuota
 */
class m250423_023219_migrate_dsv_1691_update_fn_hapus_kuota extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = file_get_contents(__DIR__ . '/definitions/pendaftaranol_hapuskuota.fn.sql');
        $this->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250423_023219_migrate_dsv_1691_update_fn_hapus_kuota cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250423_023219_migrate_dsv_1691_update_fn_hapus_kuota cannot be reverted.\n";

        return false;
    }
    */
}
