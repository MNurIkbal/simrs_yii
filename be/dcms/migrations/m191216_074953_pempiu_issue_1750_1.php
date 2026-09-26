<?php

use yii\db\Migration;

/**
 * Class m191216_074953_pempiu_issue_1750_1
 */
class m191216_074953_pempiu_issue_1750_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DELETE FROM penomoran_k WHERE penomoran_id=34;');

         $this->execute('DELETE FROM penomoran_k WHERE penomoran_id=35;');

         $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
            (34, 'Pemberian Piutang', 'PPU', 'PPU2019121201', '01', '0'),
            (35, 'Pembayaran Piutang', 'BPU', 'BPU2019121201', '01', '0');");

         $this->execute('CREATE TABLE public.pemberianpiutang_t
                (
                  pemberianpiutang_id serial8,
                  tgl_pemberianpiutang date DEFAULT now(),
                  no_pemberianpiutang character varying(100),
                  pendaftaran_id integer NOT NULL,
                  pegawai_id integer,
                  total_piutang double precision DEFAULT 0,
                  total_sisapiutang double precision DEFAULT 0,
                  total_bayarpiutang double precision DEFAULT 0,
                  catatan text,
                  status_piutang smallint DEFAULT 349, 
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
                  CONSTRAINT pemberianpiutang_t_pkey PRIMARY KEY (pemberianpiutang_id)
                )
                WITH (
                  OIDS=FALSE
                );
                ');

    $this->execute('ALTER TABLE public.pemberianpiutang_t
  OWNER TO postgres;');

    $this->execute("COMMENT ON COLUMN public.pemberianpiutang_t.status_piutang IS 'lookup_m.lookup_type=''status_bayar''';");

            $this->execute('CREATE TABLE public.pembayaranpiutang_t
                        (
                          pembayaranpiutang_id serial8,
                          pemberianpiutang_id integer NOT NULL,
                          tgl_pembayaranpiutang date DEFAULT now(),
                          no_pembayaranpiutang character varying(100),
                          pendaftaran_id integer,
                          pegawai_id integer,
                          total_bayarpiutang double precision,
                          catatan text,
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
                          CONSTRAINT pembayaranpiutang_t_pkey PRIMARY KEY (pembayaranpiutang_id)
                        )
                        WITH (
                          OIDS=FALSE
                        );
                        ');

            $this->execute('ALTER TABLE public.pembayaranpiutang_t
                            OWNER TO postgres;');


            $this->execute("
              CREATE OR REPLACE FUNCTION public.no_pembayaranpiutang()
              RETURNS trigger AS
            \$BODY\$
            DECLARE
              vId integer := 35; -- 
              vPrefix varchar;
              vLast varchar;
              vYear varchar;
              vMonth varchar;
              v_Nomor varchar;
              
            BEGIN
              
              SELECT 
                prefix,
                date_part('YEAR',now()) as year, 
                date_part('month',now()) as month,
                (
                  SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0')) last_no
                    FROM penomoran_k where penomoran_id = vId
                )
              INTO
                vPrefix,
                vYear,
                vMonth,
                vLast
                
              FROM penomoran_k WHERE penomoran_id = vId;
              v_Nomor = vPrefix || vYear || vMonth || vLast;

              UPDATE penomoran_k SET
                last_number = vLast,
                last_generate = V_Nomor
              WHERE penomoran_id = vId;

              NEW.no_pembayaranpiutang = v_Nomor;

              RETURN NEW;
            END
            \$BODY\$
              LANGUAGE plpgsql VOLATILE
              COST 100;
            ");

            $this->execute('ALTER FUNCTION public.no_pembayaranpiutang()
            OWNER TO postgres;');

            $this->execute('CREATE TRIGGER no_pembayaranpiutang
                      BEFORE INSERT
                      ON public.pembayaranpiutang_t
                      FOR EACH ROW
                      EXECUTE PROCEDURE public.no_pembayaranpiutang();');

            $this->execute("
              CREATE OR REPLACE FUNCTION public.pembayaran_piutang()
              RETURNS trigger AS
            \$BODY\$
            DECLARE

            vTotal_bayarpiutang FLOAT;
            vPemberianpiutang_id INT;
              
                
            BEGIN
              vPemberianpiutang_id :=NEW.pemberianpiutang_id;
              vTotal_bayarpiutang :=NEW.total_bayarpiutang;

              
              UPDATE pemberianpiutang_t 
              SET total_sisapiutang = total_piutang - vTotal_bayarpiutang, 
                total_bayarpiutang = total_bayarpiutang + vTotal_bayarpiutang
              WHERE pemberianpiutang_id = vPemberianpiutang_id;
                
              SELECT total_sisapiutang INTO vTotal_bayarpiutang
              FROM pemberianpiutang_t
              WHERE pemberianpiutang_id = vPemberianpiutang_id;
              
              IF(vTotal_bayarpiutang <= 0)
              THEN
                UPDATE pemberianpiutang_t 
                SET status_piutang = 348
                WHERE pemberianpiutang_id = vPemberianpiutang_id;
              END IF;

              RETURN NEW;
            END
            \$BODY\$
              LANGUAGE plpgsql VOLATILE
              COST 100;
            ");

            $this->execute('ALTER FUNCTION public.pembayaran_piutang()
            OWNER TO postgres;');

             $this->execute('CREATE TRIGGER pembayaran_piutang
            BEFORE INSERT
            ON public.pembayaranpiutang_t
            FOR EACH ROW
            EXECUTE PROCEDURE public.pembayaran_piutang();');

             $this->execute("
              CREATE OR REPLACE FUNCTION public.no_pemberianpiutang()
              RETURNS trigger AS
            \$BODY\$
            DECLARE
              vId integer := 34; -- 
              vPrefix varchar;
              vLast varchar;
              vYear varchar;
              vMonth varchar;
              v_Nomor varchar;
              
            BEGIN
              
              SELECT 
                prefix,
                date_part('YEAR',now()) as year, 
                date_part('month',now()) as month,
                (
                  SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0')) last_no
                    FROM penomoran_k where penomoran_id = vId
                )
              INTO
                vPrefix,
                vYear,
                vMonth,
                vLast
                
              FROM penomoran_k WHERE penomoran_id = vId;
              v_Nomor = vPrefix || vYear || vMonth || vLast;

              UPDATE penomoran_k SET
                last_number = vLast,
                last_generate = V_Nomor
              WHERE penomoran_id = vId;

              NEW.no_pemberianpiutang = v_Nomor;

              RETURN NEW;
            END
            \$BODY\$
              LANGUAGE plpgsql VOLATILE
              COST 100;
            ");

             $this->execute('ALTER FUNCTION public.no_pemberianpiutang()
            OWNER TO postgres;');

             $this->execute('CREATE TRIGGER no_pemberianpiutang
              BEFORE INSERT
              ON public.pemberianpiutang_t
              FOR EACH ROW
              EXECUTE PROCEDURE public.no_pemberianpiutang();');
                         
        }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191216_074953_pempiu_issue_1750_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191216_074953_pempiu_issue_1750_1 cannot be reverted.\n";

        return false;
    }
    */
}
