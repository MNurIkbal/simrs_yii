<?php

use yii\db\Migration;

/**
 * Class m231114_080906_migrate_RPP849_laporankunjunganrj_v
 */
class m231114_080906_migrate_RPP849_laporankunjunganrj_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporankunjunganrj_v");
        $laporankunjunganrj_v = file_get_contents(__DIR__ . '/definitions/laporankunjunganrj_v.sql');
        $this->execute($laporankunjunganrj_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231114_080906_migrate_RPP849_laporankunjunganrj_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231114_080906_migrate_RPP849_laporankunjunganrj_v cannot be reverted.\n";

        return false;
    }
    */
}
