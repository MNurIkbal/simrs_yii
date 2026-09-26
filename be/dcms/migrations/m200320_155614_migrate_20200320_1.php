<?php

use yii\db\Migration;

/**
 * Class m200320_155614_migrate_20200320_1
 */
class m200320_155614_migrate_20200320_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TABLE if exists public.kategoritransaksi_m;');
        $this->execute('CREATE TABLE public.kategoritransaksi_m
(
    kategoritransaksi_id serial8 NOT NULL,
    kategoritransaksi_kode character varying(50) COLLATE pg_catalog."default",
    kategoritransaksi_nama character varying(100) COLLATE pg_catalog."default",
    additional_data text COLLATE pg_catalog."default",
    created_date timestamp(6) without time zone NOT NULL DEFAULT (\'now\'::text)::date,
    created_by integer,
    modified_count integer,
    last_modified_date timestamp(6) without time zone,
    last_modified_by integer,
    is_deleted boolean DEFAULT false,
    is_active boolean DEFAULT true,
    deleted_date timestamp(6) without time zone,
    deleted_by integer,
    CONSTRAINT kategoritransaksi_m_pkey PRIMARY KEY (kategoritransaksi_id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;');

        $this->execute('ALTER TABLE public.kategoritransaksi_m
    OWNER to postgres;');

        $this->execute('DROP TRIGGER "tigger_update_pesanbarangdetail_t" ON "public"."pesanbarangdetail_t";');
        $this->execute('CREATE TRIGGER "tigger_update_pesanbarangdetail_t" AFTER UPDATE OF "qty_pesan", "barang_id" ON "public"."pesanbarangdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pesanbarangdetail_t_update"();');

         $this->execute('DROP TRIGGER "trigger_update_kamartempattidur_m" ON "public"."kamartempattidur_m";');
         $this->execute('CREATE TRIGGER "trigger_update_kamartempattidur_m" AFTER UPDATE OF "kamarruangan_id", "is_active", "is_deleted" ON "public"."kamartempattidur_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."kamartempattidur_m_update"();');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200320_155614_migrate_20200320_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200320_155614_migrate_20200320_1 cannot be reverted.\n";

        return false;
    }
    */
}
