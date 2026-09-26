<?php

use yii\db\Migration;

/**
 * Class m220331_021040_migrate_skema_fisio_konfigpelayanan_k
 */
class m220331_021040_migrate_skema_fisio_konfigpelayanan_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            UPDATE "public"."konfigpelayanan_k" SET "instalasi_id" = 1, "nama_fitur" = \'fisioterapi\', "is_dokter" = \'t\', "is_perawat" = \'t\', "additional_data" = \'{
  "url": "/rajal/pemeriksaan/form-modal-fisio?id=#pendaftaran_id#&type=fisioterapi",
  "wrapper": "#modal-lab .modal-content",
  "icon": "fa-stethoscope"
}
\', "created_date" = \'2021-10-05 00:00:00\', "created_by" = NULL, "modified_count" = NULL, "last_modified_date" = NULL, "last_modified_by" = NULL, "is_deleted" = \'f\', "is_active" = \'t\', "deleted_date" = NULL, "deleted_by" = NULL, "ruanganproses_id" = NULL, "title" = \'Fisioterapi\', "additional_condition" = NULL WHERE "konfigpelayanan_id" = 25;
        ');

        $this->execute('
            UPDATE "public"."konfigpelayanan_k" SET "instalasi_id" = 3, "nama_fitur" = \'fisioterapi\', "is_dokter" = \'t\', "is_perawat" = \'t\', "additional_data" = \'{
  "url": "/ranap/pemeriksaan-rawat-inap/form-modal-fisio?id=#pendaftaran_id#&type=fisioterapi",
  "wrapper": "#modal-lab .modal-content",
  "icon": "fa-stethoscope"
}
\', "created_date" = \'2021-10-29 00:00:00\', "created_by" = NULL, "modified_count" = NULL, "last_modified_date" = NULL, "last_modified_by" = NULL, "is_deleted" = \'f\', "is_active" = \'t\', "deleted_date" = NULL, "deleted_by" = NULL, "ruanganproses_id" = NULL, "title" = \'Fisioterapi\', "additional_condition" = \'{"akses":{"/ranap/worklist":"penunjang-fisio"}}\' WHERE "konfigpelayanan_id" = 28;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220331_021040_migrate_skema_fisio_konfigpelayanan_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220331_021040_migrate_skema_fisio_konfigpelayanan_k cannot be reverted.\n";

        return false;
    }
    */
}
