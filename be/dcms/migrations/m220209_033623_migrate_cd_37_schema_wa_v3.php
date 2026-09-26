<?php

use yii\db\Migration;

/**
 * Class m220209_033623_migrate_cd_37_schema_wa_v3
 */
class m220209_033623_migrate_cd_37_schema_wa_v3 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
					$this->execute('ALTER TABLE "public"."mutasiobatdetail_t" ADD COLUMN IF NOT EXISTS "cost" float8 ;');
		
		
					$this->execute('DROP VIEW IF EXISTS "public"."infoobatexpired_v";');

					$this->execute('
						            CREATE VIEW "public"."infoobatexpired_v" AS  SELECT array_agg(stokobatalkes_t.stokobatalkes_id) AS id_stok,
				    stokobatalkes_t.obatalkes_id,
				    obatalkes_m.obatalkes_nama,
				    stokobatalkes_t.tglkadaluarsa,
				    sum(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out) AS stok,
				        CASE
				            WHEN mutasi.status_mutasi = 401 THEN sum(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out) - COALESCE(mutasi.jumlah, 0::double precision)
				            ELSE sum(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)
				        END AS stok_exp,
				    COALESCE(mutasi.jumlah, 0::double precision) AS jumlah,
				    mutasi.status_mutasi,
				    obatalkes_m.harganetto,
				    sum(obatalkes_m.harganetto * (stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) AS jumlah_harganetto,
				    obatalkes_m.satuankecil_id,
				    satuanunit_m.satuanunit_nama AS satuan_kecil,
				    stokobatalkes_t.ruangan_id,
				    ruangan_m.ruangan_nama,
				    ruangan_m.instalasi_id,
				    instalasi_m.instalasi_nama,
				    sum(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out) * COALESCE(lr.weighted_avg::double precision, obatalkes_m.harganetto) AS cost_wa
				   	FROM stokobatalkes_t
				     JOIN obatalkes_m ON obatalkes_m.obatalkes_id = stokobatalkes_t.obatalkes_id
				     JOIN ruangan_m ON ruangan_m.ruangan_id = stokobatalkes_t.ruangan_id
				     JOIN instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id
				     LEFT JOIN satuanunit_m ON satuanunit_m.satuanunit_id = stokobatalkes_t.satuankecil_id
				     LEFT JOIN ( SELECT mutasiobatdetail_t.obatalkes_id,
				            mutasiobatdetail_t.tgl_kadaluarsa,
				            sum(mutasiobatdetail_t.jumlah_mutasi) AS jumlah,
				            mutasiobatruangan_t.status_mutasi,
				            mutasiobatruangan_t.ruanganasal_id AS ruangan_id
				           FROM mutasiobatdetail_t
				             JOIN mutasiobatruangan_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
				          WHERE mutasiobatruangan_t.status_mutasi = 401 AND mutasiobatruangan_t.is_deleted IS FALSE
				          GROUP BY mutasiobatdetail_t.obatalkes_id, mutasiobatdetail_t.tgl_kadaluarsa, mutasiobatruangan_t.status_mutasi, mutasiobatruangan_t.ruanganasal_id) 
						  mutasi ON stokobatalkes_t.obatalkes_id = mutasi.obatalkes_id AND stokobatalkes_t.tglkadaluarsa = mutasi.tgl_kadaluarsa AND stokobatalkes_t.ruangan_id = mutasi.ruangan_id
				     	  LEFT JOIN ( SELECT lr_1.ruangan_id,
				            lr_1.obatalkes_id,
				            sum(lr_1.weighted_avg) AS weighted_avg
				           FROM logasetobat_r lr_1
				             JOIN ( SELECT max(logasetobat_r.stokobatalkes_id) AS stokobatalkes_id,
				                    logasetobat_r.ruangan_id,
				                    logasetobat_r.obatalkes_id
				                   FROM logasetobat_r
				                  GROUP BY logasetobat_r.ruangan_id, logasetobat_r.obatalkes_id) tm 
								  ON lr_1.ruangan_id = tm.ruangan_id AND lr_1.stokobatalkes_id = tm.stokobatalkes_id AND lr_1.obatalkes_id = tm.obatalkes_id
				          GROUP BY lr_1.ruangan_id, lr_1.obatalkes_id) lr ON lr.obatalkes_id = stokobatalkes_t.obatalkes_id AND lr.ruangan_id = stokobatalkes_t.ruangan_id
				  GROUP BY stokobatalkes_t.obatalkes_id, 
				  obatalkes_m.obatalkes_nama, obatalkes_m.satuankecil_id, satuanunit_m.satuanunit_nama,
				  stokobatalkes_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, 
				  instalasi_m.instalasi_nama, stokobatalkes_t.tglkadaluarsa, 
				  obatalkes_m.harganetto,
				   mutasi.jumlah, mutasi.status_mutasi, lr.weighted_avg
				 HAVING sum(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out) > 0::double precision;');
 
				 $this->execute('DROP TRIGGER if exists "logasetobat_r" ON "public"."stokobatalkes_t";');		

				 $this->execute("
				 					CREATE OR REPLACE FUNCTION public.logasetobat_r_insert()
				 					  RETURNS pg_catalog.trigger AS \$BODY\$
    
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
									                  (harga / coalesce(qty_diterima,1)) AS harga,
									                  created_date
									              FROM penerimaanobatdetail_t
									              UNION ALL
									              SELECT
									                  obatalkes_id,
									                  (harga_netto / coalesce(qty_kecil,1)) AS harga,
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

				 					\$BODY\$
				 					  LANGUAGE plpgsql VOLATILE
				 					  COST 100;");
					  
				 					  $this->execute('
				 					  				  CREATE TRIGGER logasetobat_r BEFORE INSERT ON public.stokobatalkes_t
				 					  				  FOR EACH ROW
				 					  				  EXECUTE PROCEDURE public.logasetobat_r_insert();');
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220209_033623_migrate_cd_37_schema_wa_v3 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220209_033623_migrate_cd_37_schema_wa_v3 cannot be reverted.\n";

        return false;
    }
    */
}
