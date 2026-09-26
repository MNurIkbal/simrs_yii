<?php

use yii\db\Migration;

/**
 * Class m210414_004824_migrate_20210414_logasetobat
 */
class m210414_004824_migrate_20210414_logasetobat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

$this->execute('CREATE TABLE "public"."logasetobat_r" (
  "logasetobat_id" serial8,
  "obatalkes_id" int4,
  "ruangan_id" int4,
  "tipe" varchar(100) COLLATE "pg_catalog"."default",
  "qty_transaksi" float8,
  "harga_transaksi" float8,
  "qty_aset" float8,
  "harga_aset" float8,
  "weighted_avg" numeric(53,2),
  "stokobatalkes_id" int4,
  CONSTRAINT "logasetobat_r_pkey" PRIMARY KEY ("logasetobat_id")
)
;');

$this->execute("CREATE OR REPLACE FUNCTION \"public\".\"logasetobat_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
DECLARE  
    v_qty_aset FLOAT8;
    v_harga_aset FLOAT8;
    v_weigthed_avg FLOAT8;
    v_harganetto FLOAT8;
        
BEGIN

    SELECT
        SUM(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)
    INTO
        v_qty_aset
    FROM stokobatalkes_t
    WHERE obatalkes_id = NEW.obatalkes_id
    GROUP BY obatalkes_id;

    
    --kondisi kalau udah ada di log bisa dipake harga aset
    SELECT
        harga_aset,
        weighted_avg
    INTO
        v_harga_aset,
        v_weigthed_avg
    FROM logasetobat_r
         WHERE obatalkes_id = NEW.obatalkes_id
    ORDER BY logasetobat_id DESC
    LIMIT 1;

    IF v_harga_aset IS NULL THEN

        --kalau belum ada v_qty_aset dikali dengan latest price grn
        SELECT DISTINCT ON (obatalkes_id) harga * v_qty_aset
        INTO
            v_harga_aset
        FROM (
            SELECT
                obatalkes_id,
                (harga / qty_diterima) AS harga,
                created_date
            FROM penerimaanobatdetail_t
            UNION ALL
            SELECT
                obatalkes_id,
                (harga_netto / qty_kecil) AS harga,
                created_date
            FROM penerimaansuppdetail_t
        ) t
        WHERE obatalkes_id = new.obatalkes_id
        ORDER BY obatalkes_id, created_date DESC;
    END IF;

    -- Cari harga netto
    IF NEW.penerimaansuppdetail_id IS NOT NULL THEN
        -- Penerimaan manual
        SELECT (harga_netto / qty_kecil) AS harganetto
        INTO
            v_harganetto
        FROM penerimaansuppdetail_t
        WHERE penerimaansuppdetail_id = NEW.penerimaansuppdetail_id;
    ELSE
        IF NEW.penerimaanobatdetail_id IS NOT NULL THEN
            -- Penerimaan PO
            SELECT (p.harga / sm.nilai_konversi) AS harganetto
            INTO
                v_harganetto
            FROM penerimaanobatdetail_t p
            LEFT JOIN satuankonversi_m sm ON p.s_konversiobt_id = sm.satuankonversi_id 
            WHERE p.penerimaanobatdetail_id = NEW.penerimaanobatdetail_id;
        ELSE
            -- normal case
            v_harganetto = NEW.harganetto;
        END IF;
    END IF;

    -- INSERT table history logasetobat_r
    --*jangan lupa simpen stokobatalkes_id
    --*pastiin yg insert sini sesuai excel cogs
    IF (NEW.terimamutasidetail_id IS NULL 
            AND NEW.mutasiobatdetail_id IS NULL
            AND NEW.obatalkespasien_id IS NULL
            AND NEW.stokopnamedetail_id IS NULL
            AND NEW.pembatalanresep_id IS NULL
        ) THEN  
        INSERT INTO logasetobat_r (     
            obatalkes_id,
            ruangan_id,
            tipe,
            qty_transaksi,
            harga_transaksi,
            qty_aset,
            harga_aset,
            weighted_avg,
            stokobatalkes_id
        )VALUES(
            NEW.obatalkes_id ,
            NEW.ruangan_id ,
            CASE
                WHEN NEW.qtystok_in <> 0 THEN 'IN'
                ELSE 'OUT'
            END, --tipe
            CASE
                WHEN NEW.qtystok_in <> 0 THEN NEW.qtystok_in
                ELSE NEW.qtystok_out
            END, --qty_transaksi
            CASE
                WHEN NEW.qtystok_in <> 0 THEN NEW.qtystok_in * v_harganetto
                ELSE NEW.qtystok_out * coalesce(v_weigthed_avg,0)
            END, -- harga_transaksi
            CASE
                WHEN NEW.qtystok_in <> 0 THEN COALESCE(v_qty_aset,0) + NEW.qtystok_in
                ELSE COALESCE(v_qty_aset,0) - NEW.qtystok_out
            END, -- qty_aset
            CASE
                WHEN NEW.qtystok_in <> 0 THEN COALESCE(v_harga_aset,0) + (NEW.qtystok_in * v_harganetto)
                ELSE COALESCE(v_harga_aset,0) - (NEW.qtystok_out * coalesce(v_weigthed_avg,0))
            END, -- harga_aset
            CASE
                WHEN NEW.qtystok_in <> 0 THEN (COALESCE(v_harga_aset,0) + (NEW.qtystok_in * v_harganetto))::FLOAT(2) / (COALESCE(v_qty_aset,0) + NEW.qtystok_in)::FLOAT(2)
                ELSE coalesce(v_weigthed_avg,0)
            END, -- weighted_avg
            NEW.stokobatalkes_id
        );
    END IF; 

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

$this->execute('CREATE TRIGGER "logasetobat_r" BEFORE INSERT ON "public"."stokobatalkes_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."logasetobat_r_insert"();');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210414_004824_migrate_20210414_logasetobat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210414_004824_migrate_20210414_logasetobat cannot be reverted.\n";

        return false;
    }
    */
}
