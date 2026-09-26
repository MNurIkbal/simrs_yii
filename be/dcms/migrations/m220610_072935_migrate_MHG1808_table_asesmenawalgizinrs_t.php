<?php

use yii\db\Migration;

/**
 * Class m220610_072935_migrate_MHG1808_table_asesmenawalgizinrs_t
 */
class m220610_072935_migrate_MHG1808_table_asesmenawalgizinrs_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS public.asesmenawalgizinrs_t (
                asesmenawalgizinrs_id serial8 NOT NULL PRIMARY KEY,
                pendaftaran_id int4,
                pasienadmisi_id int4,
                tgl_asesmen timestamp(6),
                is_imt boolean DEFAULT FALSE,   
                is_berat_badan boolean DEFAULT FALSE,   
                is_asupan_makan boolean DEFAULT FALSE,  
                is_penyakit_berat boolean DEFAULT FALSE,    
                additional_data text COLLATE pg_catalog.default,
                created_date timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                created_by int4,
                modified_count int4,
                last_modified_date timestamp(6),
                last_modified_by int4,
                is_deleted bool NOT NULL DEFAULT false,
                is_active bool NOT NULL DEFAULT true,
                deleted_date timestamp(6),
                deleted_by int4
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220610_072935_migrate_MHG1808_table_asesmenawalgizinrs_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220610_072935_migrate_MHG1808_table_asesmenawalgizinrs_t cannot be reverted.\n";

        return false;
    }
    */
}
