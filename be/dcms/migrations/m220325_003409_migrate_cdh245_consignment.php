<?php

use yii\db\Migration;

/**
 * Class m220325_003409_migrate_cdh245_consignment
 */
class m220325_003409_migrate_cdh245_consignment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('ALTER TABLE "public"."jenisobatalkes_m" 
					ADD COLUMN if not exists "is_consignment" bool NOT NULL DEFAULT false;');
		
		        $this->execute('ALTER TABLE "public"."purchasereq_t" 
					ADD COLUMN if not exists "is_consignment" bool NOT NULL DEFAULT false;');
		

		        $this->execute('DROP VIEW if exists "public"."stokobatalkes_rs_v";');

		        $this->execute("
		         CREATE VIEW \"public\".\"stokobatalkes_rs_v\" AS   SELECT stokobatalkes_t.obatalkes_id,
		    obatalkes_m.obatalkes_kode,
		    obatalkes_m.obatalkes_nama,
		    jenisobatalkes_m.jenisobatalkes_nama,
		    servicecategory_m.servicecategory_nama,
		    servicegroup_m.servicegroup_nama,
		    sum(stokobatalkes_t.qtystok_in) / 2::double precision AS stok_in,
		    sum(stokobatalkes_t.qtystok_out) / 2::double precision AS stok_out,
		    COALESCE(COALESCE(sum(stokobatalkes_t.qtystok_in), 0::double precision) - COALESCE(sum(stokobatalkes_t.qtystok_out), 0::double precision), 0::double precision) / 2::double precision AS aset_rs,
		    jenisobatalkes_m.jenisobatalkes_id,
		    obatalkes_m.satuankecil_id,
		    obatalkes_m.satuanbesar_id
		   FROM stokobatalkes_t
		     LEFT JOIN obatalkes_m ON stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id
		     LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
		     LEFT JOIN servicecategory_m ON jenisobatalkes_m.servicecategory_id = servicecategory_m.servicecategory_id
		     LEFT JOIN servicegroup_m ON jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id
		     LEFT JOIN ( SELECT st_1.obatalkes_id,
		            om.obatalkes_nama,
		            sum(st_1.qtystok_in) AS aset_rs
		           FROM stokobatalkes_t st_1
		             LEFT JOIN obatalkes_m om ON om.obatalkes_id = st_1.obatalkes_id AND st_1.penerimaansuppdetail_id IS NOT NULL
		          GROUP BY st_1.obatalkes_id, om.obatalkes_nama) st ON stokobatalkes_t.obatalkes_id = st.obatalkes_id
		  WHERE stokobatalkes_t.is_deleted = false AND stokobatalkes_t.is_active = true AND jenisobatalkes_m.is_consignment = true AND obatalkes_m.is_consigment = true AND stokobatalkes_t.obatalkespasien_id IS NULL AND stokobatalkes_t.mutasiobatdetail_id IS NULL AND stokobatalkes_t.terimamutasidetail_id IS NULL AND stokobatalkes_t.penerimaanobatdetail_id IS NULL
		  GROUP BY stokobatalkes_t.obatalkes_id, obatalkes_m.obatalkes_kode, obatalkes_m.obatalkes_nama, jenisobatalkes_m.jenisobatalkes_nama, servicecategory_m.servicecategory_nama, servicegroup_m.servicegroup_nama, jenisobatalkes_m.jenisobatalkes_id, obatalkes_m.satuankecil_id, obatalkes_m.satuanbesar_id
		  ORDER BY obatalkes_m.obatalkes_nama;");
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220325_003409_migrate_cdh245_consignment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220325_003409_migrate_cdh245_consignment cannot be reverted.\n";

        return false;
    }
    */
}
