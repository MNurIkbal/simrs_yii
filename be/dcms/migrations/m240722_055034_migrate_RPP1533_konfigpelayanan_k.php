<?php

use yii\db\Migration;

/**
 * Class m240722_055034_migrate_RPP1533_konfigpelayanan_k
 */
class m240722_055034_migrate_RPP1533_konfigpelayanan_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('UPDATE konfigpelayanan_k SET additional_data = \'{"url":"/rajal/pemeriksaan/reseptur-modal?id=#pendaftaran_id#&ruangan_id=#ruangan_id#&konsulpoli_id=#konsulpoli_id#","wrapper":"#modal-reseptur .modal-content","icon":"fa-medkit"}\' WHERE nama_fitur = \'reseptur\' AND instalasi_id = 1;');
        $this->execute('UPDATE konfigpelayanan_k SET additional_data = \'{"url":"/rajal/pemeriksaan/form-tindakan-bmhp?id=#pendaftaran_id#&konsulpoli_id=#konsulpoli_id#","icon":"fa-stethoscope","wrapper":"#modal-form .modal-content"}\' WHERE nama_fitur = \'tindakan\' AND instalasi_id = 1;');
        $this->execute('UPDATE konfigpelayanan_k SET additional_data = \'{"url":"/rajal/pemeriksaan/form-modal?id=#pendaftaran_id#&konsulpoli_id=#konsulpoli_id#&type=laboratorium","wrapper":"#modal-lab .modal-content","icon":"fa-plus"}\' WHERE nama_fitur = \'laboratorium\' AND instalasi_id = 1;');
        $this->execute('UPDATE konfigpelayanan_k SET additional_data = \'{"url":"/rajal/pemeriksaan/form-modal?id=#pendaftaran_id#&konsulpoli_id=#konsulpoli_id#&type=radiologi","wrapper":"#modal-lab .modal-content","icon":"fa-plus"}\' WHERE nama_fitur = \'radiologi\' AND instalasi_id = 1;');
        $this->execute('UPDATE konfigpelayanan_k SET additional_data = \'{"url":"/rajal/pemeriksaan/form-modal?id=#pendaftaran_id#&konsulpoli_id=#konsulpoli_id#&type=bedah","wrapper":"#modal-lab .modal-content","icon":"fa-plus"}\' WHERE nama_fitur = \'bedah\' AND instalasi_id = 1;');
        $this->execute('UPDATE konfigpelayanan_k SET additional_data = \'{"url":"/rajal/pemeriksaan/form-modal-diet-pasien?id=#pendaftaran_id#&konsulpoli_id=#konsulpoli_id#&type=gizi","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope","width":"65%"}\' WHERE nama_fitur = \'gizi\' AND instalasi_id = 1;');
        $this->execute('UPDATE konfigpelayanan_k SET additional_data = \'{"url":"/rajal/pemeriksaan/konsulpoli?id=#pendaftaran_id#&pasien_id=#pasien_id#&konsulpoli_id=#konsulpoli_id#&is_modal=true","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\' WHERE nama_fitur = \'konsul\' AND instalasi_id = 1;');
        $this->execute('UPDATE konfigpelayanan_k SET additional_data=\'{
  "url": "/rajal/pemeriksaan/form-modal-fisio?id=#pendaftaran_id#&konsulpoli_id=#konsulpoli_id#&type=fisioterapi",
  "wrapper": "#modal-lab .modal-content",
  "icon": "fa-plus"
}\' WHERE nama_fitur = \'fisioterapi\' AND instalasi_id = 1;
');
        $this->execute('UPDATE konfigpelayanan_k SET additional_data = \'{"icon":"fa-plus","url":"/rajal/pemeriksaan/create-verbal-order?id=#pendaftaran_id#&konsulpoli_id=#konsulpoli_id#","wrapper":"#modal-lab .modal-content","width":"50%"}\' WHERE nama_fitur = \'verbal-order\' AND instalasi_id = 1;');
        $this->execute('UPDATE konfigpelayanan_k SET additional_data = \'{"url":"/rajal/pemeriksaan/form-modal?id=#pendaftaran_id#&konsulpoli_id=#konsulpoli_id#&type=bedah","icon":"fa-stethoscope","disabled":true}\' WHERE nama_fitur = \'darah\' AND instalasi_id = 1;');
        $this->execute('UPDATE konfigpelayanan_k SET additional_data=\'{
                "url": "/api/usg/form-modal-usg?id=#pendaftaran_id#&ruangan_id=#ruangan_id#&konsulpoli_id=#konsulpoli_id#&type=usg",
                "wrapper": "#modal-lab .modal-content",
                "icon": "fa-plus"
                }\' WHERE nama_fitur = \'usg\' AND instalasi_id = 1;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240722_055034_migrate_RPP1533_konfigpelayanan_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240722_055034_migrate_RPP1533_konfigpelayanan_k cannot be reverted.\n";

        return false;
    }
    */
}
