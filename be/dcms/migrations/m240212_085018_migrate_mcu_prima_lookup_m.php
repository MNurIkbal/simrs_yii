<?php

use yii\db\Migration;

/**
 * Class m240212_085018_migrate_mcu_prima_lookup_m
 */
class m240212_085018_migrate_mcu_prima_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM public.lookup_m
        WHERE lookup_type='status_kesehatan' AND lookup_name='fit' AND lookup_value='Laik kerja (FIT)';");
        $this->execute("DELETE FROM public.lookup_m
        WHERE lookup_type='status_kesehatan' AND lookup_name='fit_with_note' AND lookup_value='Laik kerja dengan catatan (FIT with Note)';");
        $this->execute("DELETE FROM public.lookup_m
        WHERE lookup_type='status_kesehatan' AND lookup_name='temporary_unfit' AND lookup_value='Tidak laik kerja untuk sementara (Temporary UNFIT)';");
        $this->execute("DELETE FROM public.lookup_m
        WHERE lookup_type='status_kesehatan' AND lookup_name='unfit' AND lookup_value='Tidak laik kerja (UNFIT)';");

        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2165, 'status_kesehatan', 'fit', 'Laik kerja (FIT)', NULL, NULL, NULL, '2024-01-16 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2166, 'status_kesehatan', 'fit_with_note', 'Laik kerja dengan catatan (FIT with Note)', NULL, NULL, NULL, '2024-01-16 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2167, 'status_kesehatan', 'temporary_unfit', 'Tidak laik kerja untuk sementara (Temporary UNFIT)', NULL, NULL, NULL, '2024-01-16 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2168, 'status_kesehatan', 'unfit', 'Tidak laik kerja (UNFIT)', NULL, NULL, NULL, '2024-01-16 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240212_085018_migrate_mcu_prima_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240212_085018_migrate_mcu_prima_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
