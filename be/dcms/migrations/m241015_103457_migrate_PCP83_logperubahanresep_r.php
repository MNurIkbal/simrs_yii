<?php

use yii\db\Migration;

/**
 * Class m241015_103457_migrate_PCP83_logperubahanresep_r
 */
class m241015_103457_migrate_PCP83_logperubahanresep_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.logperubahanresep_r (
	id serial4 NOT NULL,
	reseptur_id int4 NULL,
	resepturdetail_id int4 NULL,
	penjualanresep_id int4 NULL,
	obatalkespasien_id int4 NULL,
	tanggal_perubahan timestamp NOT NULL,
	jenis_perubahan varchar NULL,
	obatalkes_id int4 NOT NULL,
	obatalkes_nama varchar NOT NULL,
	perubahan_sebelum varchar NULL,
	perubahan_setelah varchar NULL,
	pegawai_id int4 NOT NULL,
	pegawai_nama varchar NOT NULL,
	created_date timestamp DEFAULT 'now'::text::date NOT NULL,
	created_by int4 NULL,
	modified_count int4 NULL,
	last_modified_date timestamp NULL,
	last_modified_by int4 NULL,
	is_deleted bool DEFAULT false NOT NULL,
	is_active bool DEFAULT true NOT NULL,
	deleted_date timestamp NULL,
	deleted_by int4 NULL,
	CONSTRAINT logperubahanresep_r_pk PRIMARY KEY (id)
);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241015_103457_migrate_PCP83_logperubahanresep_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241015_103457_migrate_PCP83_logperubahanresep_r cannot be reverted.\n";

        return false;
    }
    */
}
