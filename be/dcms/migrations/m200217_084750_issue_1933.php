<?php

use yii\db\Migration;

/**
 * Class m200217_084750_issue_1933
 */
class m200217_084750_issue_1933 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION public.upd_no_pembatalan()
              RETURNS pg_catalog.trigger AS \$BODY\$

            DECLARE
              v_next_number INT;
              v_prefix VARCHAR(50); 
              v_no_pembatalan VARCHAR(50);
              
            BEGIN
              
              v_prefix =  CONCAT('PBR', date_part('YEAR',now()) , RIGHT(CONCAT('0', date_part('MONTH',now())),2) ,date_part('day',now()));

              
              IF NOT EXISTS(
                SELECT 
                  1
                FROM pembatalanresep_t
                WHERE LEFT(no_pembatalan, 11) = v_prefix
                LIMIT 1
              )
              THEN 
                v_next_number = 1;
              ELSE
                SELECT 
                  MAX(RIGHT(COALESCE(no_pembatalan,'0'),5)::int4) + 1 INTO v_next_number
                FROM pembatalanresep_t
                WHERE LEFT(no_pembatalan, 11) = v_prefix;
              END IF;
              
              v_no_pembatalan =  CONCAT(v_prefix, RIGHT(CONCAT('00000', v_next_number),5));
              
              NEW.no_pembatalan = v_no_pembatalan;
            --  NEW.no_pembatalan = v_prefix;

              RETURN NEW;
            END
            \$BODY\$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ");

        $this->execute('DROP TABLE IF EXISTS public.pembatalanresep_t;');
        $this->execute('DROP SEQUENCE IF EXISTS public.pembatalanresep_t_pembatalanresep_id_seq;');
        $this->execute('CREATE SEQUENCE public.pembatalanresep_t_pembatalanresep_id_seq 
            INCREMENT 1
            MINVALUE  1
            MAXVALUE 9223372036854775807
            START 1
            CACHE 1;');
        $this->execute("
            CREATE TABLE public.pembatalanresep_t (
              pembatalanresep_id int4 NOT NULL DEFAULT nextval('pembatalanresep_t_pembatalanresep_id_seq'::regclass) PRIMARY KEY,
              penjualanresep_id int4 NOT NULL,
              tgl_pembatalan timestamp(6),
              no_pembatalan varchar ,
              petugas_batal_id int4,
              alasan_batal TEXT,
              additional_data text COLLATE pg_catalog.default,
              created_date timestamp(6) NOT NULL DEFAULT ('now'::text)::date,
              created_by int4,
              modified_count int4,
              last_modified_date timestamp(6) ,
              last_modified_by int4,
              is_deleted bool NOT NULL DEFAULT false,
              is_active bool NOT NULL DEFAULT true,
              deleted_date timestamp(6) ,
              deleted_by int4
            );
        ");

        $this->execute("CREATE INDEX pembatalanresep_no_pembatalan_idx ON public.pembatalanresep_t USING gist (
              to_tsvector('simple'::regconfig, no_pembatalan::text) pg_catalog.tsvector_ops
            );
        ");
        $this->execute("
            CREATE INDEX pembatalanresep_tgl_pembatalan_idx ON public.pembatalanresep_t USING btree (
              tgl_pembatalan pg_catalog.timestamp_ops ASC NULLS LAST
            );
        ");

        $this->execute("
            CREATE TRIGGER pembatalanresep_no_pembatalan BEFORE INSERT ON public.pembatalanresep_t
            FOR EACH ROW
            EXECUTE PROCEDURE public.upd_no_pembatalan();
        ");
        $this->execute("
            ALTER TABLE public.pembatalanresep_t ADD CONSTRAINT no_pembatalan UNIQUE (no_pembatalan);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200217_084750_issue_1933 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200217_084750_issue_1933 cannot be reverted.\n";

        return false;
    }
    */
}
