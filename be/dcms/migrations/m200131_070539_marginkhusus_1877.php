<?php

use yii\db\Migration;

/**
 * Class m200131_070539_marginkhusus_1877
 */
class m200131_070539_marginkhusus_1877 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TABLE if exists public.marginkhusus_k;');

        $this->execute('CREATE TABLE public.marginkhusus_k
                        (
                          marginkhusus_id serial8,
                          nama character varying(255),
                          perda character varying(255),
                          mulai_berlaku date,
                          additional_data text,
                          created_date timestamp(6) without time zone NOT NULL DEFAULT now(),
                          created_by integer,
                          modified_count integer,
                          last_modified_date timestamp(6) without time zone,
                          last_modified_by integer,
                          is_deleted boolean NOT NULL DEFAULT false,
                          is_active boolean NOT NULL DEFAULT true,
                          deleted_date timestamp(6) without time zone,
                          deleted_by integer,
                          CONSTRAINT marginkhusus_k_pkey PRIMARY KEY (marginkhusus_id)
                        )
                        WITH (
                          OIDS=FALSE
                        );');

        $this->execute('ALTER TABLE public.marginkhusus_k
                        OWNER TO postgres;');

        $this->execute('DROP TABLE if exists public.marginkhususdetail_k;');

        $this->execute('CREATE TABLE public.marginkhususdetail_k
                    (
                      marginkhususdetail_id serial8,
                      marginkhusus_id integer NOT NULL,
                      jenisobat_id integer,
                      margin real,
                      additional_data text,
                      created_date timestamp(6) without time zone NOT NULL DEFAULT now(),
                      created_by integer,
                      modified_count integer,
                      last_modified_date timestamp(6) without time zone,
                      last_modified_by integer,
                      is_deleted boolean NOT NULL DEFAULT false,
                      is_active boolean NOT NULL DEFAULT true,
                      deleted_date timestamp(6) without time zone,
                      deleted_by integer,
                      CONSTRAINT marginkhususdetail_k_pkey PRIMARY KEY (marginkhususdetail_id)
                    )
                    WITH (
                      OIDS=FALSE
                    );');

        $this->execute('ALTER TABLE public.marginkhususdetail_k
                    OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.marginkhusus_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.marginkhusus_v AS 
 SELECT marginkhusus_k.marginkhusus_id,
    marginkhusus_k.nama,
    marginkhusus_k.perda,
    marginkhusus_k.mulai_berlaku,
    marginkhusus_k.is_active,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT marginkhususdetail_k.marginkhususdetail_id,
                    marginkhususdetail_k.marginkhusus_id,
                    marginkhususdetail_k.jenisobat_id,
                    jenisobatalkes_m.jenisobatalkes_nama,
                    marginkhususdetail_k.margin,
                    marginkhususdetail_k.is_active
                   FROM marginkhususdetail_k
                     JOIN jenisobatalkes_m ON marginkhususdetail_k.jenisobat_id = jenisobatalkes_m.jenisobatalkes_id
                  WHERE marginkhususdetail_k.is_deleted = false AND marginkhususdetail_k.marginkhusus_id = marginkhusus_k.marginkhusus_id) d2) AS detail
   FROM marginkhusus_k
  WHERE marginkhusus_k.is_deleted IS FALSE;");

        $this->execute('ALTER TABLE public.marginkhusus_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200131_070539_marginkhusus_1877 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200131_070539_marginkhusus_1877 cannot be reverted.\n";

        return false;
    }
    */
}
