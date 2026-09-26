<?php

use yii\db\Migration;

/**
 * Class m241121_073855_migrate_rpp19233_infokonsulpoli_v
 */
class m241121_073855_migrate_rpp19233_infokonsulpoli_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infokonsulpoli_v");
        $infokonsulpoli_v = file_get_contents(__DIR__ . '/definitions/infokonsulpoli_v.view.sql');
        $this->execute($infokonsulpoli_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241121_073855_migrate_rpp19233_infokonsulpoli_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241121_073855_migrate_rpp19233_infokonsulpoli_v cannot be reverted.\n";

        return false;
    }
    */
}
