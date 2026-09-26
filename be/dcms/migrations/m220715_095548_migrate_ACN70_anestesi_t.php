<?php

use yii\db\Migration;

/**
 * Class m220715_095548_migrate_ACN70_anestesi_t
 */
class m220715_095548_migrate_ACN70_anestesi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.anestesi_t (
            anestesi_id serial8 NOT NULL,
            pasienmasukpenunjang_id int4 NOT NULL,
            pendaftaran_id int4 NULL,
            anestesi_result int2 NULL,
            anestesi_regional int2 NULL,
            anestesi_regional_other text NULL,
            type_needle_size varchar(100) NULL,
            lenght_catheter_isertion varchar(100) NULL,
            anestesi_general int2 NULL,
            patient_position varchar(100) NULL,
            preinduction text NULL,
            induction text NULL,
            maintenance text NULL,
            \"recovery\" text NULL,
            additional_data text NULL,
            created_date timestamp(6) NOT NULL DEFAULT 'now'::text::date,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            is_deleted bool NOT NULL DEFAULT false,
            is_active bool NOT NULL DEFAULT true,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            CONSTRAINT anestesi_t_pkey PRIMARY KEY (anestesi_id)
        );");
        $this->execute("COMMENT ON COLUMN anestesi_t.anestesi_result IS 'lookup_keperawatan = anestesi_result';");
        $this->execute("COMMENT ON COLUMN anestesi_t.anestesi_regional IS 'lookup_keperawatan = anestesi_regional';");
        $this->execute("COMMENT ON COLUMN anestesi_t.anestesi_regional_other IS 'diisi jika anestesi_regional = other';");
        $this->execute("COMMENT ON COLUMN anestesi_t.anestesi_general IS 'lookup_keperwatan = anestesi_general';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220715_095548_migrate_ACN70_anestesi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220715_095548_migrate_ACN70_anestesi_t cannot be reverted.\n";

        return false;
    }
    */
}
