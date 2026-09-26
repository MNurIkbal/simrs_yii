<?php

use yii\db\Migration;

/**
 * Class m191212_103309_table_1691_1714_1711
 */
class m191212_103309_table_1691_1714_1711 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" 
                        ADD COLUMN "is_valid" bool DEFAULT true;');

        $this->execute('ALTER TABLE "public"."pembayaranpelayanan_t" 
                        ADD COLUMN "pembayaran_id" int4;');

        $this->execute('DROP TABLE if exists public.pembayaran_t;');

        $this->execute('CREATE TABLE public.pembayaran_t
                    (
                      pembayaran_id serial8,
                      pendaftaran_id integer,
                      pasienadmisi_id integer,
                      total_tagihan double precision DEFAULT 0,
                      total_dibayar double precision DEFAULT 0,
                      total_dijamin double precision DEFAULT 0,
                      total_sisatagihan double precision DEFAULT 0,
                      total_kembalian double precision,
                      total_administrasi double precision DEFAULT 0,
                      total_pembulatan double precision DEFAULT 0,
                      total_pembebasan double precision DEFAULT 0,
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
                      CONSTRAINT pembayaran_t_pkey PRIMARY KEY (pembayaran_id)
                    )
                    WITH (
                      OIDS=FALSE
                    );');


        $this->execute('ALTER TABLE public.pembayaran_t
                        OWNER TO postgres;');

        $this->execute('DROP TABLE if exists public.pembayaranpenjamin_t;');

        $this->execute('CREATE TABLE public.pembayaranpenjamin_t
                    (
                      pembayaranpenjamin_id serial8,
                      pembayaran_id integer NOT NULL,
                      penjamin_id integer,
                      penjamin_nama character varying(255),
                      no_kartu character varying(255),
                      total_dijamin double precision,
                      additional_data text,
                      created_date timestamp(6) without time zone DEFAULT now(),
                      created_by integer,
                      modified_count integer,
                      last_modified_date timestamp(6) without time zone,
                      last_modified_by integer,
                      is_deleted boolean DEFAULT false,
                      is_active boolean DEFAULT true,
                      deleted_date timestamp(6) without time zone,
                      deleted_by integer,
                      CONSTRAINT pembayaranpenjamin_t_pkey PRIMARY KEY (pembayaranpenjamin_id)
                    )
                    WITH (
                      OIDS=FALSE
                    );');

        $this->execute('ALTER TABLE public.pembayaranpenjamin_t
                        OWNER TO postgres;');

        $this->execute('DROP TABLE if exists public.pembayaranmetode_t;');

        $this->execute('CREATE TABLE public.pembayaranmetode_t
                    (
                      pembayaranmetode_id serial8,
                      pembayaran_id integer NOT NULL,
                      metode_bayar character varying(255),
                      no_kartu character varying(255),
                      total_dibayar double precision,
                      additional_data text,
                      created_date timestamp(6) without time zone DEFAULT now(),
                      created_by integer,
                      modified_count integer,
                      last_modified_date timestamp(6) without time zone,
                      last_modified_by integer,
                      is_deleted boolean DEFAULT false,
                      is_active boolean DEFAULT true,
                      deleted_date timestamp(6) without time zone,
                      deleted_by integer,
                      CONSTRAINT pembayaranmetode_t_pkey PRIMARY KEY (pembayaranmetode_id)
                    )
                    WITH (
                      OIDS=FALSE
                    );');

        $this->execute('ALTER TABLE public.pembayaranmetode_t
                    OWNER TO postgres;');
   
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191212_103309_table_1691_1714_1711 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191212_103309_table_1691_1714_1711 cannot be reverted.\n";

        return false;
    }
    */
}
