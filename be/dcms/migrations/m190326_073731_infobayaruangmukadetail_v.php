<?php

use yii\db\Migration;

/**
 * Class m190326_073731_infobayaruangmukadetail_v
 */
class m190326_073731_infobayaruangmukadetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP VIEW infobayaruangmukadetail_v;
        ");
        $this->execute("
            CREATE OR REPLACE VIEW infobayaruangmukadetail_v AS 
             SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.pasien_id,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                kelaspelayanan_m.kelaspelayanan_nama,
                bayaruangmuka_t.no_uangmuka,
                bayaruangmuka_t.tgl_uangmuka,
                bayaruangmuka_t.jumlah_uangmuka,
                bayaruangmuka_t.bayaruangmuka_id
               FROM pendaftaran_t
                 JOIN bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
                 JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                 JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                 JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
              WHERE bayaruangmuka_t.is_active = true AND bayaruangmuka_t.is_deleted = false;
        ");
        $this->execute("
            ALTER TABLE infobayaruangmukadetail_v
              OWNER TO postgres;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190326_073731_infobayaruangmukadetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190326_073731_infobayaruangmukadetail_v cannot be reverted.\n";

        return false;
    }
    */
}
