<?php

use yii\db\Migration;

/**
 * Class m220217_064608_migrate_cdh22_konfigurasipenomeran_k
 */
class m220217_064608_migrate_cdh22_konfigurasipenomeran_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('ALTER TABLE "public"."penomoran_k" ADD COLUMN if not exists "konfig_penomoran" int4;');
		
		$this->execute("COMMENT ON COLUMN public.penomoran_k.konfig_penomoran IS 'lookup_type=status_penerimaan_po';");
		
		$this->execute("
			DELETE FROM public.lookup_m WHERE lookup_id = 1151 ;");
		
		$this->execute("
						INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (1151, 'konfig_penomoran', 'Konfig Penomoran', 'Harian', NULL, NULL, NULL, '2022-02-07 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
		
		$this->execute("
			DELETE FROM public.lookup_m WHERE lookup_id = 1152 ;");
		
		$this->execute("
						INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (1152, 'konfig_penomoran', 'Konfig Penomoran', 'Bulanan', NULL, NULL, NULL, '2022-02-07 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
		
		$this->execute("
			DELETE FROM public.lookup_m WHERE lookup_id = 1153 ;");
		
		$this->execute("
						INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (1153, 'konfig_penomoran', 'Konfig_Penomoran', 'Tahunan', NULL, NULL, NULL, '2022-02-07 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220217_064608_migrate_cdh22_konfigurasipenomeran_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220217_064608_migrate_cdh22_konfigurasipenomeran_k cannot be reverted.\n";

        return false;
    }
    */
}
