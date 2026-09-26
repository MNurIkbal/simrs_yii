<?php

use yii\db\Migration;

/**
 * Class m190723_041126_monitorbpjsdetail_m
 */
class m190723_041126_monitorbpjsdetail_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
         DROP SEQUENCE if exists public.monitorbpjsdetail_m_monitorbpjsdetail_id_seq;
        ');

        $this->execute('
        CREATE SEQUENCE public.monitorbpjsdetail_m_monitorbpjsdetail_id_seq
          INCREMENT 1
          MINVALUE 1
          MAXVALUE 9223372036854775807
          START 1
          CACHE 1;
        ');

        $this->execute('
         ALTER TABLE public.monitorbpjsdetail_m_monitorbpjsdetail_id_seq
  OWNER TO postgres;
        ');

        $this->execute('
         DROP TABLE if exists public.monitorbpjsdetail_m;
        ');

        $this->execute('
         CREATE TABLE public.monitorbpjsdetail_m
(
  monitorbpjsdetail_id integer NOT NULL DEFAULT nextval(\'monitorbpjsdetail_m_monitorbpjsdetail_id_seq\'::regclass),
  monitorbpjs_id integer NOT NULL,
  groupinacbg_id integer NOT NULL,
  additional_data text,
  created_date timestamp(6) without time zone NOT NULL DEFAULT now (),
  created_by integer,
  modified_count integer,
  last_modified_date timestamp(6) without time zone,
  last_modified_by integer,
  is_deleted boolean NOT NULL DEFAULT false,
  is_active boolean NOT NULL DEFAULT true,
  deleted_date timestamp(6) without time zone,
  deleted_by integer,
  CONSTRAINT monitorbpjsdetail_m_pkey PRIMARY KEY (monitorbpjsdetail_id)
)
WITH (
  OIDS=FALSE
);
        ');

        $this->execute('
         ALTER TABLE public.monitorbpjsdetail_m
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190723_041126_monitorbpjsdetail_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190723_041126_monitorbpjsdetail_m cannot be reverted.\n";

        return false;
    }
    */
}
