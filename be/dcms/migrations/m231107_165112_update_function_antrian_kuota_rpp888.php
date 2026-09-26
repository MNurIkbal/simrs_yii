<?php

use yii\db\Migration;

/**
 * Class m231107_165112_update_function_antrian_kuota_rpp888
 */
class m231107_165112_update_function_antrian_kuota_rpp888 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $ins_noantrian_konfig = file_get_contents(__DIR__ . '/definitions/ins_noantrian_konfig.fn.sql');
        $this->execute($ins_noantrian_konfig);
        
        $upd_stokkuotadokter_from_antrian_try = file_get_contents(__DIR__ . '/definitions/upd_stokkuotadokter_from_antrian_try.fn.sql');
        $this->execute($upd_stokkuotadokter_from_antrian_try);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231107_165112_update_function_antrian_kuota_rpp888 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231107_165112_update_function_antrian_kuota_rpp888 cannot be reverted.\n";

        return false;
    }
    */
}
