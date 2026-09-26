<?php

use yii\db\Migration;

/**
 * Class m221207_091630_migrate_hotfix_laporanstockinventorybarang_fn
 */
class m221207_091630_migrate_hotfix_laporanstockinventorybarang_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION if exists public.laporanstockinventorybarang_fn;');

        $this->execute("
			CREATE OR REPLACE FUNCTION public.laporanstockinventorybarang_fn(x_date date)
			  RETURNS TABLE(ruangan_nama varchar, ruanganid int4, 
				 instalasi_nama varchar, instalasi_id int4, barang_kode varchar, barang_nama varchar, kelompok_barang varchar, subkelompok_nama varchar, nilai_konv float8, stok_fisik float8, qty_satuankecil float8, qty_satuanbesar float8, stok float8, satuan_besar varchar, satuan_kecil varchar, baseprice_kecil float8, baseprice_besar float8, harga_konversi float8, harga_netto float8, harga float8, total_harga float8, total_satuankecil float8, total_satuanbesar float8) AS \$BODY\$

			BEGIN
				RETURN QUERY 
				SELECT distinct * FROM ( --1374
													SELECT 
														ruangan_m.ruangan_nama::VARCHAR as ruangan_nama,
														ruangan_m.ruangan_id as ruanganid,
														instalasi_m.instalasi_nama::VARCHAR as instalasi_nama,
														instalasi_m.instalasi_id as instalasi_id,
														barang_m.barang_kode::VARCHAR as barang_kode,
														barang_m.barang_nama::VARCHAR as barang_nama, 
														kelompokbarang_m.kelompokbarang_nama::VARCHAR as kelompok_barang,
														subkelompokbarang_m.subkelompok_nama::VARCHAR as subkelompok_nama,
											-- 			CASE 
											-- 					WHEN stok.tglstok_in is not null THEN stok.tglstok_in::DATE 
											-- 					WHEN stok.tglstok_out is not null THEN stok.tglstok_out::DATE
											-- 			END AS tanggal_inventory ,
														konversi.nilai_konversi::FLOAT8 as nilai_konv,
														stok.fisik::FLOAT8 as stok_fisik,
														stok.fisik::FLOAT8 as qty_satuankecil,
														(stok.fisik / konversi.nilai_konversi)::FLOAT8 as qty_satuanbesar,
														stok.fisik::FLOAT8 as stok,
														satuanbesar.satuanunit_nama::VARCHAR as satuan_besar,
														satuankecil.satuanunit_nama::VARCHAR as satuan_kecil,
														barang_m.barang_harganetto::FLOAT8 as baseprice_kecil,
														(barang_m.barang_harganetto * barang_m.isi_satuan1)::FLOAT8 as baseprice_besar,
														(barang_m.barang_harganetto * konversi.nilai_konversi)::FLOAT8 as harga_konversi,
														barang_m.barang_harganetto::FLOAT8 as harga_netto,
														(barang_m.barang_harganetto * konversi.nilai_konversi * (stok.fisik / konversi.nilai_konversi))::FLOAT8 as harga,
														(stok.fisik * barang_m.barang_harganetto)::FLOAT8  as total_harga,
														(stok.fisik * barang_m.barang_harganetto)::FLOAT8 as total_satuankecil,
														((stok.fisik / konversi.nilai_konversi) * (barang_m.barang_harganetto * konversi.nilai_konversi))::FLOAT8 as total_satuanbesar
													FROM (
														SELECT 
															ruangan_id as ruanganid,
															barang_id,
															max(stokbarang_id) as id_stok,
															SUM(qtystok_in-qtystok_out) as fisik
														FROM stokbarang_t
														WHERE (CASE 
																		WHEN stokbarang_t.tglstok_in is not null THEN stokbarang_t.tglstok_in 
																		ELSE stokbarang_t.tglstok_out 
																	END) <=  x_date::date + interval '23 hours 59 minutes'
														GROUP BY ruanganid,barang_id
													) stok
													LEFT join ruangan_m on ruangan_m.ruangan_id = stok.ruanganid
													LEFT join instalasi_m on ruangan_m.instalasi_id = instalasi_m.instalasi_id
													LEFT join (select a.barang_id,a.barang_kode,a.barang_nama,a.barang_harganetto,a.satuan1_id,a.satuankecil_id,a.kelompokbarang_id,a.subkelompokbarang_id,a.isi_satuan1 from barang_m a where a.is_deleted = false) barang_m on barang_m.barang_id = stok.barang_id
													LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id= kelompokbarang_m.kelompokbarang_id
													LEFT JOIN subkelompokbarang_m on subkelompokbarang_m.subkelompokbarang_id = barang_m.subkelompokbarang_id
													LEFT JOIN (SELECT 
																				satuankecil_id,
																				satuanbesar_id,
																				nilai_konversi,
																				barang_id
																		 FROM satuankonversibrg_m where is_deleted = false) konversi ON konversi.satuanbesar_id = barang_m.satuan1_id 
																																		AND	konversi.satuankecil_id = barang_m.satuankecil_id 
																																		AND konversi.barang_id = stok.barang_id
													LEFT JOIN satuanunit_m satuankecil ON satuankecil.satuanunit_id = barang_m.satuankecil_id
													LEFT JOIN satuanunit_m satuanbesar ON satuanbesar.satuanunit_id = barang_m.satuan1_id
													ORDER by ruangan_m.ruangan_nama,barang_m.barang_kode
											) x;
	
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
        echo "m221207_091630_migrate_hotfix_laporanstockinventorybarang_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221207_091630_migrate_hotfix_laporanstockinventorybarang_fn cannot be reverted.\n";

        return false;
    }
    */
}
