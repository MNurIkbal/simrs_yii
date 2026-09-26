<?php

use yii\db\Migration;

/**
 * Class m190820_045545_profilrumahsakit_m
 */
class m190820_045545_profilrumahsakit_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(639, 'kode_tarifbpjs', 'Tarif RS Kelas A Pemerintah', 'AP', NULL, 'AP', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(640, 'kode_tarifbpjs', 'Tarif RS Kelas A Swasta', 'AS', NULL, 'AS', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(641, 'kode_tarifbpjs', 'Tarif RS Kelas B Pemerintah', 'BP', NULL, 'BP', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(642, 'kode_tarifbpjs', 'Tarif RS Kelas B Swasta', 'BS', NULL, 'BS', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(643, 'kode_tarifbpjs', 'Tarif RS Kelas C Pemerintah', 'CP', NULL, 'CP', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(644, 'kode_tarifbpjs', 'Tarif RS Kelas C Swasta', 'CS', NULL, 'CS', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(645, 'kode_tarifbpjs', 'Tarif RS Kelas D Pemerintah', 'DP', NULL, 'DP', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(646, 'kode_tarifbpjs', 'Tarif RS Kelas D Swasta', 'DS', NULL, 'DS', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");

        $this->execute('ALTER TABLE "public"."profilrumahsakit_m" 
                        ADD COLUMN "kodetarifbpjs_id" int4;');

        $this->execute('UPDATE profilrumahsakit_m set kodetarifbpjs_id=639 where profilrs_id=1');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190820_045545_profilrumahsakit_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190820_045545_profilrumahsakit_m cannot be reverted.\n";

        return false;
    }
    */
}
