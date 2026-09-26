<?php

use yii\db\Migration;

/**
 * Class m241029_024653_migrate_RPP1901_bpjs_infoantrean_v
 */
class m241029_024653_migrate_RPP1901_bpjs_infoantrean_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $bpjs_infoantrean_v = file_get_contents(__DIR__ . '/definitions/bpjs_infoantrean_v.view.sql');
        $this->execute($bpjs_infoantrean_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241029_024653_migrate_RPP1901_bpjs_infoantrean_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241029_024653_migrate_RPP1901_bpjs_infoantrean_v cannot be reverted.\n";

        return false;
    }
    */
}
