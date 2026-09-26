<?php

use yii\db\Migration;

/**
 * Class m220627_101246_migrate_skema_fisioterapi_lookuptransaksi_m_konfignonpaket
 */
class m220627_101246_migrate_skema_fisioterapi_lookuptransaksi_m_konfignonpaket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'konfig_fisio_maks_frekuensi_non_paket';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES ('konfig_fisio_maks_frekuensi_non_paket', 0, 'Maks Non Paket Frekuensi', 10, NULL, NULL);
            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220627_101246_migrate_skema_fisioterapi_lookuptransaksi_m_konfignonpaket cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220627_101246_migrate_skema_fisioterapi_lookuptransaksi_m_konfignonpaket cannot be reverted.\n";

        return false;
    }
    */
}
