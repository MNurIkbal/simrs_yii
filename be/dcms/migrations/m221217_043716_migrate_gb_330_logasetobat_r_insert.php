<?php

use yii\db\Migration;

/**
 * Class m221217_043716_migrate_gb_330_logasetobat_r_insert
 */
class m221217_043716_migrate_gb_330_logasetobat_r_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('DROP TRIGGER IF EXISTS logasetobat_r ON stokobatalkes_t;');
		
		$this->execute('DROP TRIGGER IF EXISTS logasetobat_r ON validasipoobat_t;');
				
		$this->execute('DROP FUNCTION if exists public.logasetobat_r_insert;');
		
        $this->execute("
			CREATE OR REPLACE FUNCTION \"public\".\"logasetobat_r_insert\"()
			  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
           
			           DECLARE  
			               v_qty_aset FLOAT8;
			               v_harga_aset FLOAT8;
			               v_weigthed_avg FLOAT8;
			               v_harganetto FLOAT8;
			               v_baseprice FLOAT8;
			               v_ruangan INT4;
			               v_is_weighted_avg_rs BOOL;
       
			           BEGIN
			               SELECT
			                   SUM(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)
			               INTO
			                   v_qty_aset
			               FROM stokobatalkes_t
			               WHERE obatalkes_id = NEW.obatalkes_id and ruangan_id = new.ruangan_id
			               GROUP BY obatalkes_id;
       
			               select 
			                   harganetto
			               into 
			                   v_baseprice
			               from obatalkes_m
			               where obatalkes_id = new.obatalkes_id;
              
			               select 
			               		is_weighted_avg_rs 
			               into 
			              		v_is_weighted_avg_rs 
			               from konfigfarmasi_k kk 
			               where konfigfarmasi_id = 1;
       
			               select kode_id into v_ruangan from lookuptransaksi_m where kode_transaksi = 'gudang_farmasi';
              
			               if v_is_weighted_avg_rs = false then
			               		v_ruangan = new.ruangan_id;
			               end if;
              
			               --kondisi kalau udah ada di log bisa dipake harga aset
			               SELECT
			                   harga_aset,
			                   weighted_avg
			               INTO
			                   v_harga_aset,
			                   v_weigthed_avg
			               FROM logasetobat_r
			                   WHERE obatalkes_id = NEW.obatalkes_id and ruangan_id = v_ruangan --new.ruangan_id
			               ORDER BY logasetobat_id DESC
			               LIMIT 1;
       
			               --kalau belum ada v_qty_aset dikali dengan latest price grn
			               IF v_harga_aset IS NULL THEN
			                   SELECT DISTINCT ON (obatalkes_id) harga * v_qty_aset
			                   INTO
			                       v_harga_aset
			                   FROM (
			                       SELECT
			                           obatalkes_id,
			                           case when qty_diterima = 0 then
			                               v_baseprice
			                           else
			                               (harga / coalesce(nullif(qty_diterima,0),1))
			                           end AS harga,
			                           created_date
			                       FROM penerimaanobatdetail_t
			                       UNION ALL
			                       SELECT
			                           obatalkes_id,
			                           case when qty_kecil = 0 then
			                               v_baseprice
			                           else
			                               (harga_netto / coalesce(nullif(qty_kecil,0),1)) 
			                           end AS harga,
			                           created_date
			                       FROM penerimaansuppdetail_t
			                   ) t
			                   WHERE obatalkes_id = new.obatalkes_id
			                   ORDER BY obatalkes_id, created_date DESC;
			               END IF;
       
			               if v_weigthed_avg is null then
			                   v_weigthed_avg = v_baseprice;
			               end if;
       
			               v_harganetto = new.harganetto;
       
			               IF (
			                   new.stokopnamedetail_id is not null
			                   or new.mutasiobatdetail_id is not null
			                   or new.terimamutasidetail_id is not null
			                   or new.returpenerimaanobatdetail_id is not null
			                   or new.adjusmenobatkeluar_id is not null
			                   or (new.adjusmenobatmasuk_id is not null and new.harganetto = 0)
			               ) then
			                   v_harganetto = v_weigthed_avg;
			               end if;
       
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
			                       WHEN coalesce(NEW.qtystok_in,0) <> 0 THEN 'IN'
			                       ELSE 'OUT'
			                   END, --tipe
			                   CASE
			                       WHEN coalesce(NEW.qtystok_in,0) <> 0 THEN NEW.qtystok_in
			                       ELSE NEW.qtystok_out
			                   END, --qty_transaksi
			                   CASE
			                       WHEN coalesce(NEW.qtystok_in,0) <> 0 THEN NEW.qtystok_in * v_harganetto
			                       ELSE NEW.qtystok_out * coalesce(v_weigthed_avg,v_baseprice)
			                   END, -- harga_transaksi
			                   CASE
			                       WHEN coalesce(NEW.qtystok_in,0) <> 0 THEN COALESCE(v_qty_aset,0) + NEW.qtystok_in
			                       ELSE COALESCE(v_qty_aset,0) - NEW.qtystok_out
			                   END, -- qty_aset
			                   CASE
			                       WHEN coalesce(NEW.qtystok_in,0) <> 0 THEN COALESCE(v_harga_aset,0) + (NEW.qtystok_in * v_harganetto)
			                       ELSE COALESCE(v_harga_aset,0) - (NEW.qtystok_out * coalesce(v_weigthed_avg,v_baseprice))
			                   END, -- harga_aset
			                   case
			                      WHEN coalesce(NEW.qtystok_in,0) > 0 and NEW.ruangan_id = v_ruangan THEN 
			                          (COALESCE(v_harga_aset,0) + (NEW.qtystok_in * v_harganetto))::FLOAT(2) / 
			                          coalesce(
			                                 nullif(
			                                     (COALESCE(v_qty_aset,0) + NEW.qtystok_in), 0
			                                 ), 1
			                          )::FLOAT(2)
			                      ELSE 
			                          coalesce(v_weigthed_avg,v_baseprice)
			                   end
			                   , -- weighted_avg
			                   NEW.stokobatalkes_id
			               );
       
			               RETURN NEW;
       
			           end
       
			       \$BODY\$
			  LANGUAGE plpgsql VOLATILE
			  COST 100
            ");
			
			$this->execute('
				CREATE TRIGGER "logasetobat_r" BEFORE INSERT ON "public"."stokobatalkes_t"
					FOR EACH ROW
				EXECUTE PROCEDURE "public"."logasetobat_r_insert"();
						');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221217_043716_migrate_gb_330_logasetobat_r_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221217_043716_migrate_gb_330_logasetobat_r_insert cannot be reverted.\n";

        return false;
    }
    */
}
