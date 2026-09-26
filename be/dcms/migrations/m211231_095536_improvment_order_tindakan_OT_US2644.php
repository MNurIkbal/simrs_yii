<?php

use yii\db\Migration;

/**
 * Class m211231_095536_improvment_order_tindakan_OT_US2644
 */
class m211231_095536_improvment_order_tindakan_OT_US2644 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->execute('
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = \'groupingtindakan_penunjang\';
        ');

        $this->execute('
            INSERT INTO "public"."lookuptransaksi_m"("kode_transaksi", "kode_id", "kode_fungsi", "additional_value", "kode_nama", "kode_singkatan") VALUES (\'groupingtindakan_penunjang\', 0, \'penanda view list tindakan untuk kramat\', \'false\', NULL, NULL);

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211231_095536_improvment_order_tindakan_OT_US2644 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211231_095536_improvment_order_tindakan_OT_US2644 cannot be reverted.\n";

        return false;
    }
    */
}
