<?php

use yii\db\Migration;

/**
 * Class m220624_055338_migrate_cssd_penomoran_k
 */
class m220624_055338_migrate_cssd_penomoran_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from penomoran_k where penomoran_id IN (196,198,199,201,202);
        ");

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh, konfig_penomoran) VALUES 
            (202, 'No Penerimaan CSSD Barang', 'PCB', NULL, NULL, '0', NULL),
            (201, 'No Terima Sterilisasi CSSD', 'PMK', NULL, NULL, '0', NULL),
            (199, 'No Sterilisasi CSSD', 'PS', NULL, NULL, '0', NULL),
            (198, 'No Proses CSSD', 'PRO', NULL, NULL, '0', NULL),
            (196, 'No Pengajuan CSSD', 'PM', NULL, NULL, '0', NULL);
            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_055338_migrate_cssd_penomoran_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_055338_migrate_cssd_penomoran_k cannot be reverted.\n";

        return false;
    }
    */
}
