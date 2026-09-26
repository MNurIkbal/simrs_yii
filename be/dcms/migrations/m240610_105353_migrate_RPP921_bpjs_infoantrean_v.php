<?php

use yii\db\Migration;

/**
 * Class m240610_105353_migrate_RPP921_bpjs_infoantrean_v
 */
class m240610_105353_migrate_RPP921_bpjs_infoantrean_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS bpjs_infoantrean_v");
        $bpjs_infoantrean_v = file_get_contents(__DIR__ . '/definitions/bpjs_infoantrean_v.view.sql');
        $this->execute($bpjs_infoantrean_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240610_105353_migrate_RPP921_bpjs_infoantrean_v cannot be reverted.\n";

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
