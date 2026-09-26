<?php

use yii\db\Migration;

/**
 * Class m240614_112500_migrate_RPP1432_antrianjkn_v
 */
class m240614_112500_migrate_RPP1432_antrianjkn_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS antrianjkn_v");
        $antrianjkn_v = file_get_contents(__DIR__ . '/definitions/antrianjkn_v.sql');
        $this->execute($antrianjkn_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240614_112500_migrate_RPP1432_antrianjkn_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240610_105353_migrate_RPP921_bpjs_infoantrean_v cannot be reverted.\n";

        return false;
    }
    */
}
