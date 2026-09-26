<?php

use yii\db\Migration;

/**
 * Class m231127_070227_migrate_klasifikasikamar_v_applicares
 */
class m231127_070227_migrate_klasifikasikamar_v_applicares extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS klasifikasikamar_v");
        $klasifikasikamar_v = file_get_contents(__DIR__ . '/definitions/klasifikasikamar_v.sql');
        $this->execute($klasifikasikamar_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231127_070227_migrate_klasifikasikamar_v_applicares cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231127_070227_migrate_klasifikasikamar_v_applicares cannot be reverted.\n";

        return false;
    }
    */
}
