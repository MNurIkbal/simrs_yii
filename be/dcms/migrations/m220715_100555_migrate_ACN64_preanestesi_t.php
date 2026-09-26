<?php

use yii\db\Migration;

/**
 * Class m220715_100555_migrate_ACN64_preanestesi_t
 */
class m220715_100555_migrate_ACN64_preanestesi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TABLE IF EXISTS "public"."preanestesi_t";');
        $this->execute("CREATE TABLE public.preanestesi_t (
            preanestesi_id serial8 NOT NULL,
            pasienmasukpenunjang_id int4 NULL,
            ruangan_id int4 NULL,
            surgical_procedure varchar(100) NULL,
            surgical_status int4 NULL,
            tinggi_badan int4 NULL,
            berat_badan int4 NULL,
            suhu_tubuh int4 NULL,
            pernafasan int4 NULL,
            tekanan_darah_systolic int4 NULL,
            tekanan_darah_diastolic int4 NULL,
            nevous_system text NULL,
            urinary_tracking_system text NULL,
            cardiovascular_system text NULL,
            gastrointestinal_system text NULL,
            metabolic_system text NULL,
            coexist_condition text NULL,
            respitory_system text NULL,
            musculo_skeletal_system text NULL,
            status_asa int4 NULL,
            medication_taken text NULL,
            type_1 varchar(100) NULL,
            type_2 varchar(100) NULL,
            type_3 varchar(100) NULL,
            \"size\" varchar(100) NULL,
            fluite_1 varchar(100) NULL,
            fluite_2 varchar(100) NULL,
            fluite_3 varchar(100) NULL,
            rate_of_influsion_1 int4 NULL,
            rate_of_influsion_2 int4 NULL,
            rate_of_influsion_3 int4 NULL,
            fluid_left_1 int4 NULL,
            fluid_left_2 int4 NULL,
            fluid_left_3 int4 NULL,
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
            CONSTRAINT preanestesi_t_pkey PRIMARY KEY (preanestesi_id)
        );");
        $this->execute("COMMENT ON COLUMN preanestesi_t.surgical_status IS 'lookupkeperawatan_m -> lookup_type = surgical_status';");
        $this->execute("COMMENT ON COLUMN preanestesi_t.status_asa IS 'lookup_m -> lookup_type = status_asa';");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220715_100555_migrate_ACN64_preanestesi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220715_100555_migrate_ACN64_preanestesi_t cannot be reverted.\n";

        return false;
    }
    */
}
