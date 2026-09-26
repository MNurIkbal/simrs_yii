<?php

use yii\db\Migration;

/**
 * Class m200114_225222_groupmargin_1767
 */
class m200114_225222_groupmargin_1767 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TABLE if exists public.groupmargin_m;');

        $this->execute('CREATE TABLE public.groupmargin_m
                    (
                      groupmargin_id serial8,
                      groupmargin_kode character varying(50),
                      groupmargin_nama character varying(255),
                      additional_data text,
                      created_date timestamp(6) without time zone DEFAULT now(),
                      created_by integer,
                      modified_count integer,
                      last_modified_date timestamp(6) without time zone,
                      last_modified_by integer,
                      is_deleted boolean NOT NULL DEFAULT false,
                      is_active boolean NOT NULL DEFAULT true,
                      deleted_date timestamp(6) without time zone,
                      deleted_by integer,
                      CONSTRAINT groupmargin_m_pkey PRIMARY KEY (groupmargin_id)
                    )
                    WITH (
                      OIDS=FALSE
                    );');

        $this->execute('ALTER TABLE public.groupmargin_m
                      OWNER TO postgres;
                    ');

        $this->execute('ALTER TABLE "public"."konfigmargin_k" 
                        ADD COLUMN "groupmargin_id" int4,
                        ADD COLUMN "nama_margin" varchar(255);
                        ');

        $this->execute('DROP VIEW if exists public.konfigmargin_v;');

        $this->execute("
                        CREATE OR REPLACE VIEW public.konfigmargin_v AS 
 SELECT konfigmargin_k.konfigmargin_id,
    konfigmargindetail_k.konfigmargindetail_id,
    konfigmargin_k.nama_margin,
    konfigmargin_k.perda_margin,
    konfigmargin_k.tgl_berlaku,
    konfigmargin_k.groupmargin_id,
    groupmargin_m.groupmargin_nama,
    konfigmargin_k.is_active,
    konfigmargindetail_k.harga_min,
    konfigmargindetail_k.harga_max,
    konfigmargindetail_k.margin,
    konfigmargindetail_k.is_active AS is_activedetail,
    obatalkespasien_t.konfigmargindetail_id AS cekmargindetail_id
   FROM konfigmargin_k
     JOIN konfigmargindetail_k ON konfigmargin_k.konfigmargin_id = konfigmargindetail_k.konfigmargin_id AND konfigmargindetail_k.is_deleted = false
     LEFT JOIN obatalkespasien_t ON konfigmargindetail_k.konfigmargindetail_id = obatalkespasien_t.konfigmargindetail_id
     JOIN groupmargin_m ON konfigmargin_k.konfigmargin_id = groupmargin_m.groupmargin_id
  WHERE konfigmargin_k.is_deleted = false;
                        ");

        $this->execute('ALTER TABLE public.konfigmargin_v
  OWNER TO postgres;');

        $this->execute('ALTER TABLE "public"."penjamin_m" 
    ADD COLUMN "groupmargin_id" int4;');

    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200114_225222_groupmargin_1767 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200114_225222_groupmargin_1767 cannot be reverted.\n";

        return false;
    }
    */
}
