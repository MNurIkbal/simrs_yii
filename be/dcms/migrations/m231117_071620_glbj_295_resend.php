<?php

use yii\db\Migration;

/**
 * Class m231117_071620_glbj_295_resend
 */
class m231117_071620_glbj_295_resend extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        -- public.bpjs_antrian_tanggal_t definition

        -- Drop table

        -- DROP TABLE public.bpjs_antrian_tanggal_t;

        CREATE TABLE IF NOT EXISTS public.bpjs_antrian_tanggal_t (
            id serial4 NOT NULL,
            tanggal date NULL,
            kodebooking varchar(30) NULL,
            kodepoli varchar(20) NULL,
            kodedokter varchar(20) NULL,
            jampraktek varchar(200) NULL,
            nik varchar(50) NULL,
            nokapst varchar(50) NULL,
            nohp varchar(20) NULL,
            norekammedis varchar(50) NULL,
            jeniskunjungan int4 NULL,
            nomorreferensi varchar(50) NULL,
            sumberdata varchar(200) NULL,
            ispeserta bool NULL,
            noantrean varchar(200) NULL,
            estimasidilayani int8 NULL,
            createdtime int8 NULL,
            status varchar(200) NULL,
            created_date timestamp NOT NULL DEFAULT now(),
            created_by int4 NULL,
            is_deleted bool NOT NULL DEFAULT false,
            last_sync timestamp NULL,
            CONSTRAINT bpjs_antrian_tanggal_t_pkey PRIMARY KEY (id)
        );
        ");

        $this->execute("
        -- public.bpjs_list_task_t definition

        -- Drop table

        -- DROP TABLE public.bpjs_list_task_t;

        CREATE TABLE IF NOT EXISTS public.bpjs_list_task_t (
            id serial4 NOT NULL,
            wakturs varchar(100) NULL,
            waktu varchar(100) NULL,
            taskname varchar(200) NULL,
            taskid int4 NULL,
            kodebooking varchar(100) NULL,
            wakturs_time int8 NULL,
            waktu_time int8 NULL,
            created_date timestamp NOT NULL DEFAULT now(),
            created_by int4 NULL,
            is_deleted bool NOT NULL DEFAULT false,
            last_sync timestamp NULL,
            CONSTRAINT bpjs_list_task_t_pkey PRIMARY KEY (id)
        );
        ");

        $this->execute("
        -- public.bpjs_referensipoli definition

        -- Drop table

        -- DROP TABLE public.bpjs_referensipoli;

        CREATE TABLE IF NOT EXISTS public.bpjs_referensipoli (
            id serial4 NOT NULL,
            kdpoli varchar(100) NULL,
            nmpoli varchar(255) NULL,
            kdsubspesialis varchar(255) NULL,
            nmsubspesialis varchar(255) NULL,
            created_date timestamp NULL,
            is_deleted bool NULL DEFAULT false,
            CONSTRAINT bpjs_referensipoli_pkey PRIMARY KEY (id)
        );
        ");

        $this->execute("DROP VIEW IF EXISTS bpjs_infoantrean_v");
        $bpjs_infoantrean_v = file_get_contents(__DIR__ . '/definitions/bpjs_infoantrean_v.view.sql');
        $this->execute($bpjs_infoantrean_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231117_071620_glbj_295_resend cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231117_071620_glbj_295_resend cannot be reverted.\n";

        return false;
    }
    */
}
