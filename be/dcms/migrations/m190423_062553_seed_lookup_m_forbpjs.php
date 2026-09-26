<?php

use yii\db\Migration;

/**
 * Class m190423_062553_seed_lookup_m_forbpjs
 */
class m190423_062553_seed_lookup_m_forbpjs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m WHERE lookup_id IN (631,632,633,634,635,636,637,638);
        ');
        $this->execute('
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (631, \'bpjs_asal_rujukan\', \'Faskes Tingkat 1\', \'1\', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (632, \'bpjs_asal_rujukan\', \'Faskes Tingkat 2\', \'2\', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (633, \'bpjs_pelayanan\', \'Rawat Jalan\', \'2\', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (634, \'bpjs_pelayanan\', \'Rawat Inap\', \'1\', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (635, \'bpjs_kasus_kecelakaan\', \'Bukan Kecelakaan\', \'1\', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (636, \'bpjs_kasus_kecelakaan\', \'Kecelakaan Lalulintas dan bukan kecelakaan kerja\', \'2\', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (637, \'bpjs_kasus_kecelakaan\', \'Kecelakaan Lalulintas dan kecelakaan kerja\', \'3\', 3, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (638, \'bpjs_kasus_kecelakaan\', \'Kecelakaan kerja\', \'4\', 4, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
            DELETE FROM lookup_m WHERE lookup_id IN (631,632,633,634,635,636,637,638);
        ");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190423_062553_seed_lookup_m_forbpjs cannot be reverted.\n";

        return false;
    }
    */
}
