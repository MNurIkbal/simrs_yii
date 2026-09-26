<?php

use yii\db\Migration;

/**
 * Class m240213_074812_migrate_RPP1144_upd_kuotadokter_r_try
 */
class m240213_074812_migrate_RPP1144_upd_kuotadokter_r_try extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        - $upd_kuotadokter_r_try = file_get_contents(__DIR__ . '/definitions/upd_kuotadokter_r_try.sql');
        - $this->execute($upd_kuotadokter_r_try);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240213_074812_migrate_RPP1144_upd_kuotadokter_r_try cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240213_074812_migrate_RPP1144_upd_kuotadokter_r_try cannot be reverted.\n";

        return false;
    }
    */
}
