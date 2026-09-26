<?php

use yii\db\Migration;

/**
 * Class m221026_045613_migrate_VCS424_logasetobat_r_insert
 */
class m221026_045613_migrate_VCS424_logasetobat_r_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION public.logasetobat_r_insert()
        RETURNS trigger
        LANGUAGE plpgsql
       AS \$function\$
           
           DECLARE  
               v_qty_aset FLOAT8;
               v_harga_aset FLOAT8;
               v_weigthed_avg FLOAT8;
               v_harganetto FLOAT8;
               v_baseprice FLOAT8;
       
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
       
       
               --kondisi kalau udah ada di log bisa dipake harga aset
               SELECT
                   harga_aset,
                   weighted_avg
               INTO
                   v_harga_aset,
                   v_weigthed_avg
               FROM logasetobat_r
                   WHERE obatalkes_id = NEW.obatalkes_id and ruangan_id = new.ruangan_id
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
                      WHEN coalesce(NEW.qtystok_in,0) > 0 THEN 
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
       
       \$function\$
       ;
       ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221026_045613_migrate_VCS424_logasetobat_r_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221026_045613_migrate_VCS424_logasetobat_r_insert cannot be reverted.\n";

        return false;
    }
    */
}
