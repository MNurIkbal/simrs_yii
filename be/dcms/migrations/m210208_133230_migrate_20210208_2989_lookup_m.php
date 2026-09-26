<?php

use yii\db\Migration;

/**
 * Class m210208_133230_migrate_20210208_2989_lookup_m
 */
class m210208_133230_migrate_20210208_2989_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_type='jenis_pendaftaran';
        ");
        $this->execute("
            DELETE from lookup_m where lookup_type='jenis_ruangan';
        ");
        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (727, 'jenis_pendaftaran', 'Pendaftaran Online', 'Pendaftaran Online', 2, NULL, NULL, '2020-11-23 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (726, 'jenis_pendaftaran', 'Pendaftaran Langsung', 'Pendaftaran Langsung', 1, NULL, NULL, '2020-11-23 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (725, 'jenis_ruangan', 'PENUNJANG MEDIS', 'PENUNJANG MEDIS', 4, NULL, NULL, '2020-11-23 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (724, 'jenis_ruangan', 'IGD/UMUM', 'IGD/UMUM', 3, NULL, NULL, '2020-11-23 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (723, 'jenis_ruangan', 'MEDICAL CHECK UP', 'MEDICAL CHECK UP', 2, NULL, NULL, '2020-11-23 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (722, 'jenis_ruangan', 'POLIKLINIK', 'POLIKLINIK', 1, NULL, NULL, '2020-11-23 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210208_133230_migrate_20210208_2989_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210208_133230_migrate_20210208_2989_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
