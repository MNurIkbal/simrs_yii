<?php

use yii\db\Migration;

/**
 * Class m190723_032917_monitorbpjs_m
 */
class m190723_032917_monitorbpjs_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
         DROP SEQUENCE IF exists public.monitorbpjs_m_monitorbpjs_id_seq;
        ');

        $this->execute('
        CREATE SEQUENCE public.monitorbpjs_m_monitorbpjs_id_seq
          INCREMENT 1
          MINVALUE 1
          MAXVALUE 9223372036854775807
          START 1
          CACHE 1;
        ');

        $this->execute('
        ALTER TABLE public.monitorbpjs_m_monitorbpjs_id_seq
            OWNER TO postgres;
        ');


        $this->execute('
        DROP TABLE if exists public.monitorbpjs_m;
        ');

        $this->execute('
        CREATE TABLE public.monitorbpjs_m
            (
              monitorbpjs_id integer NOT NULL DEFAULT nextval(\'monitorbpjs_m_monitorbpjs_id_seq\'::regclass),
              kelompoktindakan_nama character varying(100) NOT NULL,
              catatan text,
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
              CONSTRAINT monitorbpjs_m_pkey PRIMARY KEY (monitorbpjs_id)
            )
            WITH (
              OIDS=FALSE
            );
        ');

         $this->execute('
        ALTER TABLE public.monitorbpjs_m
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190723_032917_monitorbpjs_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190723_032917_monitorbpjs_m cannot be reverted.\n";

        return false;
    }
    */
}
