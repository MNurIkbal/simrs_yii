<?php

use yii\db\Migration;

/**
 * Class m250422_064559_migrate_dsv_1691_update_view
 */
class m250422_064559_migrate_dsv_1691_update_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopendaftaranol_v");
        $infopendaftaranol_v = file_get_contents(__DIR__ . '/definitions/infopendaftaranol_v.view.sql');
        $this->execute($infopendaftaranol_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250422_064559_migrate_dsv_1691_update_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250422_064559_migrate_dsv_1691_update_view cannot be reverted.\n";

        return false;
    }
    */
}
