<?php

use yii\db\Migration;

/**
 * Class m211103_085456_improvment_konfig_pelayanan_US1959
 */
class m211103_085456_improvment_konfig_pelayanan_US1959 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigpelayanan_k ADD IF NOT EXISTS additional_condition TEXT;
        ');

        $this->execute('
            UPDATE "public"."konfigpelayanan_k" SET "additional_condition" = \'{"data_pasien":{"status_periksa_id":{"attr":"disabled","attr_value":true,"values":[4,433]}}}\' WHERE "konfigpelayanan_id" = 8;
        ');

        $this->execute('
           UPDATE "public"."konfigpelayanan_k" SET "additional_condition" = \'{"data_pasien":{"status_periksa_id":{"attr":"disabled","attr_value":true,"values":[4,433]}}}\' WHERE "konfigpelayanan_id" = 12;
        ');


        $this->execute('
            UPDATE "public"."konfigpelayanan_k" SET "additional_condition" = \'{"data_pasien":{"status_periksa_id":{"attr":"disabled","attr_value":true,"values":[4,433]}}}\' WHERE "konfigpelayanan_id" = 22;
        ');

        $this->execute('
            UPDATE "public"."konfigpelayanan_k" SET "additional_condition" = \'{"data_pasien":{"status_periksa_id":{"attr":"disabled","attr_value":true,"values":[4,433]}}}\' WHERE "konfigpelayanan_id" = 23;
        ');

        $this->execute('
            UPDATE "public"."konfigpelayanan_k" SET "additional_condition" = \'{"data_pasien":{"status_periksa_id":{"attr":"disabled","attr_value":true,"values":[4,433]}}}\' WHERE "konfigpelayanan_id" = 13;
        ');

        $this->execute('
            UPDATE "public"."konfigpelayanan_k" SET "additional_condition" = \'{"data_pasien":{"status_periksa_id":{"attr":"disabled","attr_value":true,"values":[4,433]}}}\' WHERE "konfigpelayanan_id" = 14;
        ');

        $this->execute('
            UPDATE "public"."konfigpelayanan_k" SET "additional_condition" = \'{"data_pasien":{"status_periksa_id":{"attr":"disabled","attr_value":true,"values":[4,433]}}}\' WHERE "konfigpelayanan_id" = 11;
        ');

        $this->execute('
            UPDATE "public"."konfigpelayanan_k" SET "additional_condition" = \'{"data_pasien":{"status_periksa_id":{"attr":"disabled","attr_value":true,"values":[4,433]}}}\' WHERE "konfigpelayanan_id" = 10;
        ');

        $this->execute('
            UPDATE "public"."konfigpelayanan_k" SET "additional_condition" = \'{"data_pasien":{"status_periksa_id":{"attr":"disabled","attr_value":true,"values":[4,433]}}}\' WHERE "konfigpelayanan_id" = 9;    
        ');

        $this->execute('
           DELETE FROM konfigpelayanan_k
           WHERE konfigpelayanan_id = 28;
        ');

        $this->execute('
           INSERT INTO "public"."konfigpelayanan_k"("konfigpelayanan_id", "instalasi_id", "nama_fitur", "is_dokter", "is_perawat", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by", "ruanganproses_id", "title", "additional_condition") VALUES (28, 3, \'fisioterapi\', \'t\', \'t\', \'{"url":"/ranap/pemeriksaan-rawat-inap/form-modal?id=#pendaftaran_id#&type=fisioterapi","wrapper":"#modal-lab .modal-content","icon":"fa-stethoscope"}\', \'2021-10-29 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, \'Fisioterapi\', \'{"akses":{"/ranap/worklist":"penunjang-fisio"}}\');
        ');
        
        $this->execute('
           UPDATE konfigpelayanan_k
           SET  is_deleted = \'f\',
                is_active = \'t\';
        ');
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211103_085456_improvment_konfig_pelayanan_US1959 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211103_085456_improvment_konfig_pelayanan_US1959 cannot be reverted.\n";

        return false;
    }
    */
}
