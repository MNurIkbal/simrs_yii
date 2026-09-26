<?php

use yii\db\Migration;

/**
 * Class m220524_084917_migrate_MHG_1744_tambahstokopnamebarang_fn
 */
class m220524_084917_migrate_MHG_1744_tambahstokopnamebarang_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION if exists public.tambahstokopnamebarang_fn;');

        $this->execute("
			CREATE OR REPLACE FUNCTION public.tambahstokopnamebarang_fn(x_ruangan_id int4)
			  RETURNS TABLE(barang_id int4, barang_nama varchar, barang_kode varchar, kelompok_barang varchar, subkelompok_barang varchar) AS \$BODY\$
                    
											  BEGIN
											      RETURN QUERY 

						select DISTINCT on (x.barang_id::int)
						x.barang_id::int,
						    x.barang_nama::VARCHAR,
								x.barang_kode::VARCHAR,
						    x.kelompok_barang::VARCHAR,
						    x.subkelompok_barang::VARCHAR
						--		x.kondisi::VARCHAR
								from (
						select 
								a.* from (
							SELECT 
						---		'SOP'::TEXT as Kondisi,
						    barang_m.barang_id,
						    barang_m.barang_nama,
								barang_m.barang_kode,
						    kelompokbarang_m.kelompokbarang_nama AS kelompok_barang,
						    subkelompokbarang_m.subkelompok_nama AS subkelompok_barang
						   FROM barang_m
						     LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
						     LEFT JOIN subkelompokbarang_m ON barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id
						  WHERE barang_m.is_deleted = false AND barang_m.is_active = true
							) a WHERE a.barang_id not in (

										SELECT distinct
						    formsobarangdetail_t.barang_id --,
								--barang_m.barang_nama
						   FROM formsobarangdetail_t
						     JOIN formsobarang_t ON formsobarangdetail_t.formsobarang_id = formsobarang_t.formsobarang_id
						     --JOIN barang_m ON formsobarangdetail_t.barang_id = barang_m.barang_id
						     LEFT JOIN ( SELECT st.ruangan_id,
						            st.barang_id,
						            sum(st.qtystok_in - st.qtystok_out) AS total
						           FROM stokbarang_t st
											 where st.stokbarang_aktif = true and st.ruangan_id = x_ruangan_id::int
						          GROUP BY st.ruangan_id, st.barang_id  ) kartustok ON kartustok.ruangan_id = formsobarang_t.ruangan_id AND kartustok.barang_id = formsobarangdetail_t.barang_id
												--LEFT JOIN obatalkes_m on formsobarangdetail_t.barang_id = barang_m.barang_id
												where formsobarang_t.ruangan_id = x_ruangan_id::int and formsobarang_t.stokopnamebarang_id is null
											ORDER BY  formsobarangdetail_t.barang_id
						) and a.barang_id not in (		SELECT distinct stokopnamebarangdetail_t.barang_id from stokopnamebarangdetail_t INNER JOIN stokopnamebarang_t on stokopnamebarangdetail_t.stokopnamebarang_id = stokopnamebarang_t.stokopnamebarang_id
						WHERE stokopnamebarang_t.is_verifikasi = false  and stokopnamebarang_t.ruangan_id = x_ruangan_id::int)
						union all 
						   	 select
								 a.* from (
							SELECT 
						  -- 'FSO'::TEXT as Kondisi,
						    barang_m.barang_id,
						    barang_m.barang_nama,
								barang_m.barang_kode,
						    kelompokbarang_m.kelompokbarang_nama AS kelompok_barang,
						    subkelompokbarang_m.subkelompok_nama AS subkelompok_barang
						   FROM barang_m
						     LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
						     LEFT JOIN subkelompokbarang_m ON barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id
						  WHERE barang_m.is_deleted = false AND barang_m.is_active = true
							) a WHERE a.barang_id  not in (
							SELECT distinct
						    formsobarangdetail_t.barang_id
						   FROM formsobarangdetail_t
						     JOIN formsobarang_t ON formsobarangdetail_t.formsobarang_id = formsobarang_t.formsobarang_id
						     --JOIN barang_m ON formsobarangdetail_t.barang_id = barang_m.barang_id
						     LEFT JOIN ( SELECT st.ruangan_id,
						            st.barang_id,
						            sum(st.qtystok_in - st.qtystok_out) AS total
						           FROM stokbarang_t st
											 where st.stokbarang_aktif = true and st.ruangan_id = x_ruangan_id::int
						          GROUP BY st.ruangan_id, st.barang_id  ) kartustok ON kartustok.ruangan_id = formsobarang_t.ruangan_id AND kartustok.barang_id = formsobarangdetail_t.barang_id
											ORDER BY  formsobarangdetail_t.barang_id
	
							))x; --where x.kondisi = x_kondisi::TEXT;
											  END
											\$BODY\$
			  LANGUAGE plpgsql IMMUTABLE
			  COST 100
			  ROWS 1000
			
           ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220524_084917_migrate_MHG_1744_tambahstokopnamebarang_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220524_084917_migrate_MHG_1744_tambahstokopnamebarang_fn cannot be reverted.\n";

        return false;
    }
    */
}
