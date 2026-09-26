<?php

use yii\db\Migration;

/**
 * Class m190507_023717_infopasienkarcisdetail_v_update
 */
class m190507_023717_infopasienkarcisdetail_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
       DROP VIEW infopasienkarcisdetail_v;
        ');

        $this->execute('
     CREATE OR REPLACE VIEW infopasienkarcisdetail_v AS 
 SELECT tindakanpelayanan_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    tindakanpelayanan_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    tindakanpelayanan_t.instalasi_id,
    instalasi_m.instalasi_nama,
    tindakanpelayanan_t.carabayar_id,
    carabayar_m.carabayar_nama,
    tindakanpelayanan_t.penjamin_id,
    penjamin_m.penjamin_nama,
    tindakanpelayanan_t.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_t.tgl_tindakan,
    tindakanpelayanan_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarif_tindakan::integer AS tarif_tindakan
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN instalasi_m ON tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
  WHERE (daftartindakan_m.kelompoktindakan_id = ANY (ARRAY[17, 19])) AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.is_active = true;


        ');

        $this->execute('
 ALTER TABLE infopasienkarcisdetail_v
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190507_023717_infopasienkarcisdetail_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190507_023717_infopasienkarcisdetail_v_update cannot be reverted.\n";

        return false;
    }
    */
}
