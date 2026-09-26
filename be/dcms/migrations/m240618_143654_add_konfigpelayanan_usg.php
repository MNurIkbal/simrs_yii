<?php

use yii\db\Migration;

/**
 * Class m240618_143654_add_konfigpelayanan_usg
 */
class m240618_143654_add_konfigpelayanan_usg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM konfigpelayanan_k WHERE nama_fitur ='usg'");

        $this->execute("
            WITH lastrow AS (SELECT max(konfigpelayanan_id)+1 as id FROM konfigpelayanan_k)
            INSERT INTO konfigpelayanan_k (
                konfigpelayanan_id, instalasi_id,ruanganproses_id,title,nama_fitur,is_dokter,is_perawat,additional_data,created_date,additional_condition
            ) SELECT
                lastrow.id,1,NULL,'USG','usg',true,false,'".'{
                "url": "/api/usg/form-modal-usg?id=#pendaftaran_id#&type=usg",
                "wrapper": "#modal-lab .modal-content",
                "icon": "fa-plus"
                }'."',now(),NULL
            FROM lastrow
        ");

        $this->execute("
            WITH lastrow AS (SELECT max(konfigpelayanan_id)+1 as id FROM konfigpelayanan_k)
            INSERT INTO konfigpelayanan_k (
                konfigpelayanan_id, instalasi_id,ruanganproses_id,title,nama_fitur,is_dokter,is_perawat,additional_data,created_date,additional_condition
            ) SELECT
                lastrow.id,2,NULL,'USG','usg',true,false,'".'{
                "url": "/api/usg/form-modal-usg?id=#pendaftaran_id#&type=usg",
                "wrapper": "#modal-lab .modal-content",
                "icon": "fa-plus"
                }'."',now(),'".'{"data_pasien":{"status_periksa_id":{"attr":"disabled","attr_value":true,"values":[4,433]}}}'."'
            FROM lastrow

        ");

        $this->execute("
            WITH lastrow AS (SELECT max(konfigpelayanan_id)+1 as id FROM konfigpelayanan_k)
            INSERT INTO konfigpelayanan_k (
                konfigpelayanan_id, instalasi_id,ruanganproses_id,title,nama_fitur,is_dokter,is_perawat,additional_data,created_date,additional_condition
            ) SELECT
                lastrow.id,3,NULL,'USG','usg',true,false,'".'{
                "url": "/api/usg/form-modal-usg?id=#pendaftaran_id#&type=usg",
                "wrapper": "#modal-lab .modal-content",
                "icon": "fa-plus"
                }'."',now(),NULL
            FROM lastrow
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240618_143654_add_konfigpelayanan_usg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240618_143654_add_konfigpelayanan_usg cannot be reverted.\n";

        return false;
    }
    */
}
