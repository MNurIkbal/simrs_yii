<?php

use yii\db\Migration;

/**
 * Class m231102_095101_migrate_RPP853_infopendaftaranol_v
 */
class m231102_095101_migrate_RPP853_infopendaftaranol_v extends Migration
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
        echo "m231102_095101_migrate_RPP853_infopendaftaranol_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231102_095101_migrate_RPP853_infopendaftaranol_v cannot be reverted.\n";

        return false;
    }
    */
}
