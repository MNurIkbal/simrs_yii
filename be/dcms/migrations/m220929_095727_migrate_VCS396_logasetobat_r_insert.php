<?php

use yii\db\Migration;

/**
 * Class m220929_095727_migrate_VCS396_logasetobat_r_insert
 */
class m220929_095727_migrate_VCS396_logasetobat_r_insert extends Migration
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
          
                                                 select harganetto
                                                 into v_baseprice
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
       
                                                 IF v_harga_aset IS NULL THEN
       
                                                     --kalau belum ada v_qty_aset dikali dengan latest price grn
                                                     SELECT DISTINCT ON (obatalkes_id) harga * v_qty_aset
                                                     INTO
                                                         v_harga_aset
                                                     FROM (
                                                         SELECT
                                                             obatalkes_id,
                                                             (harga / coalesce(nullif(qty_diterima,0),1)) AS harga,
                                                             created_date
                                                         FROM penerimaanobatdetail_t
                                                         UNION ALL
                                                         SELECT
                                                             obatalkes_id,
                                                             (harga_netto / coalesce(nullif(qty_kecil,0),1)) AS harga,
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
                                                 else 
                                                     IF NEW.penerimaanobatdetail_id IS NOT NULL THEN
                                                         -- Penerimaan PO
                                                         SELECT (p.harga / sm.nilai_konversi) AS harganetto
                                                         INTO
                                                             v_harganetto
                                                         FROM penerimaanobatdetail_t p
                                                         LEFT JOIN satuankonversi_m sm ON p.s_konversiobt_id = sm.satuankonversi_id 
                                                         WHERE p.penerimaanobatdetail_id = NEW.penerimaanobatdetail_id;
                                                     else 
                                                         if new.terimamutasidetail_id is not null then
                                                             -- terima mutasi
                                                             select coalesce(lr.weighted_avg,v_weigthed_avg)
                                                             into
                                                                 v_harganetto
                                                             from terimamutasiobatdetail_t td
                                                             left join stokobatalkes_t sot on sot.mutasiobatdetail_id = td.mutasiobatdetail_id 
                                                             left join logasetobat_r lr on lr.stokobatalkes_id = sot.stokobatalkes_id
                                                             where td.terimamutasiobatdetail_id = new.terimamutasidetail_id;
                                                         else 
                                                             if new.stokopnamedetail_id is not null then 
                                                                 select 
                                                                     weighted_avg
                                                                 into 
                                                                     v_harganetto
                                                                 FROM logasetobat_r
                                                                      WHERE obatalkes_id = NEW.obatalkes_id and ruangan_id = new.ruangan_id
                                                                 ORDER BY logasetobat_id DESC
                                                                 LIMIT 1;
                                                             ELSE
                                                                 -- normal case
                                                                 select harganetto 
                                                                 into v_harganetto
                                                                 from obatalkes_m 
                                                                 where obatalkes_id = new.obatalkes_id;
                                                             end if;
                                                         end if;
                                                    end if;
                                                 END IF;
       
                                                 -- INSERT table history logasetobat_r
                                                 --*jangan lupa simpen stokobatalkes_id
                                                 --*pastiin yg insert sini sesuai excel cogs
                                             --     IF (NEW.terimamutasidetail_id IS NULL 
                                             --             AND NEW.mutasiobatdetail_id IS NULL
                                             --             AND NEW.obatalkespasien_id IS NULL
                                             --             AND NEW.stokopnamedetail_id IS NULL
                                             --             AND NEW.pembatalanresep_id IS NULL
                                             --         ) THEN  
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
                                                             when NEW.qtystok_in + v_qty_aset = 0 then 0 
                                                             WHEN coalesce(NEW.qtystok_in,0) > 0 THEN (COALESCE(v_harga_aset,0) + (NEW.qtystok_in * v_harganetto))::FLOAT(2) / (COALESCE(v_qty_aset,0) + NEW.qtystok_in)::FLOAT(2)
                                                             ELSE coalesce(v_weigthed_avg,v_baseprice)
                                                         end
                                                         , -- weighted_avg
                                                         NEW.stokobatalkes_id
                                                     );
                                             --     END IF; 
       
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
        echo "m220929_095727_migrate_VCS396_logasetobat_r_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220929_095727_migrate_VCS396_logasetobat_r_insert cannot be reverted.\n";

        return false;
    }
    */
}
