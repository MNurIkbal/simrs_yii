<?php

use yii\db\Migration;

/**
 * Class m251020_123158_lookuptransaksi_m_penambahan_ruangan_fisioterapi
 */
class m251020_123158_lookuptransaksi_m_penambahan_ruangan_fisioterapi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'ruangan_fisioterapi';");
        $this->execute("
            INSERT INTO public.lookuptransaksi_m 
            (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) 
            VALUES('ruangan_fisioterapi', 0, 'Ruangan Fisioterapi', '[935]', NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251020_123158_lookuptransaksi_m_penambahan_ruangan_fisioterapi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251020_123158_lookuptransaksi_m_penambahan_ruangan_fisioterapi cannot be reverted.\n";

        return false;
    }
    */
}
