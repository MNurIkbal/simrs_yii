<?php

use yii\db\Migration;

/**
 * Class m200227_035638_migratefunction_20200227
 */
class m200227_035638_migratefunction_20200227 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION if exists public.pembayaran_t();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.pembayaran_t()
  RETURNS trigger AS
\$BODY\$-- author yaya

DECLARE
    paramJson VARCHAR;
    vDataPembayaranPelayanan VARCHAR;
    vDataPembayaranPenjamin VARCHAR;
    vDataJenisPembayaran VARCHAR;
    
BEGIN
    paramJson := NEW.additional_data;
    vDataPembayaranPelayanan := paramJson::json->>'pembayaran_pelayanan';
    vDataPembayaranPenjamin := paramJson::json->>'pembayaran_penjamin';
    -- vDataJenisPembayaran := paramJson::json->>'pembayaran_jenis_pembayaran';
    
    -- Insert ke  pembayaranpelayanan_t
    IF (json_array_length(vDataPembayaranPelayanan::json) > 0) THEN 
        INSERT INTO pembayaranpelayanan_t (
                            carabayar_id,
                            ruangan_id,
                            penjamin_id,
                            pendaftaran_id,
                            tandabuktibayar_id,
                            pasien_id,
                            pasienadmisi_id,
                            ruangan_pelakhir_id,
                            no_pembayaran,
                            tgl_pembayaran,
                            total_biayaoa,
                            total_biayatindakan,
                            total_biayapelayanan,
                            total_subsidiasuransi,
                            total_subsidipemerintah,
                            total_subsidirs,
                            total_iurbiaya,
                            total_bayartindakan,
                            total_discount,
                            total_pembebasan,
                            total_sisatagihan,
                            statusbayar,
                            penjualanresep_id,
                            biaya_administrasi,
                            e_collection,
                            no_rekening,
                            nama_pemrekening,
                            penggunaan_uangmuka,
                            total_terbayar,
                            pembulatan,
                            additional_data,
                            created_by
        ) VALUES (
                            NEW.carabayar_id,
                            NEW.ruangan_id,
                            NEW.penjamin_id,
                            NEW.pendaftaran_id,
                            NEW.tandabuktibayar_id,
                            NEW.pasien_id,
                            NEW.pasienadmisi_id,
                            NEW.ruangan_pelakhir_id,
                            NEW.no_pembayaran,
                            NEW.tgl_pembayaran,
                            NEW.total_biayaoa,
                            NEW.total_biayatindakan,
                            NEW.total_biayapelayanan,
                            NEW.total_subsidiasuransi,
                            NEW.total_subsidipemerintah,
                            NEW.total_subsidirs,
                            NEW.total_iurbiaya,
                            NEW.total_bayartindakan,
                            NEW.total_discount,
                            NEW.total_pembebasan,
                            NEW.total_sisatagihan,
                            NEW.statusbayar,
                            NEW.penjualanresep_id,
                            NEW.biaya_administrasi,
                            NEW.e_collection,
                            NEW.no_rekening,
                            NEW.nama_pemrekening,
                            NEW.penggunaan_uangmuka,
                            NEW.total_terbayar,
                            NEW.pembulatan,
                            NEW.additional_data,
                            NEW.created_by
        );
        
    END IF;
    
    
    -- Insert ke  pembayaranpenjamin_t
    IF (json_array_length(vDataPembayaranPenjamin::json) > 0) THEN
        INSERT INTO pembayaranpenjamin_t (
                        pembayaran_id,
                        penjamin_id,
                        penjamin_nama,
                        no_kartu,
                        total_dijamin,
                        created_by
        ) VALUES (
                        NEW.pembayaran_id,
                        NEW.penjamin_id,
                        NEW.penjamin_nama,
                        NEW.no_kartu,
                        NEW.total_dijamin,
                        NEW.created_by
        );
    
    END IF;
    
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.pembayaran_t()
  OWNER TO postgres;');

    $this->execute('DROP TRIGGER if exists set_hargajual ON public.obatalkespasien_t;');
    
    $this->execute('DROP FUNCTION if exists public.harga_jual();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.harga_jual()
  RETURNS trigger AS
\$BODY\$
DECLARE

    
BEGIN
    NEW.hargajual_oa = NEW.hargasatuan_oa * NEW.qty_oa;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.harga_jual()
  OWNER TO postgres;');

        $this->execute('CREATE TRIGGER set_hargajual BEFORE INSERT ON public.obatalkespasien_t
FOR EACH ROW
EXECUTE PROCEDURE public.harga_jual();');


        $this->execute('DROP TRIGGER if exists trigger_stokoa ON public.stokobatalkes_t;');

        $this->execute('DROP FUNCTION if exists public.update_stokoa();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.update_stokoa()
  RETURNS trigger AS
\$BODY\$
DECLARE
v_stokobatalkes_id int4;
v_stok float8;
v_stokoa_aktif boolean;
v_ruangan_id int4;
v_obatalkes_id int4;
BEGIN
    v_stokobatalkes_id := NEW.stokobatalkes_id;
    v_stok := NEW.stok;
    v_stokoa_aktif := NEW.stokoa_aktif;
    v_ruangan_id := NEW.ruangan_id;
    v_obatalkes_id := NEW.obatalkes_id;
    
    IF(v_stokoa_aktif IS FALSE)
    THEN
--          UPDATE stokobatalkes_t 
--          SET stok = 0
--          WHERE stokobatalkes_id = v_stokobatalkes_id;
--          
            UPDATE stokobatalkes_r
            SET qty_sisa = 0,-- qty_sisa - v_stok,
                    qty_tersedia = 0,--qty_tersedia - v_stok,
                    qty_masuk = 0, --qty_masuk - v_stok
                    qty_keluar = 0 --qty_masuk - v_stok
            WHERE obatalkes_id = v_obatalkes_id
            AND ruangan_id = v_ruangan_id;
    END IF;
       
    RETURN NEW;

END;
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.update_stokoa()
  OWNER TO postgres;');

        $this->execute('CREATE TRIGGER trigger_stokoa
  AFTER UPDATE OF stokoa_aktif
  ON public.stokobatalkes_t
  FOR EACH ROW
  EXECUTE PROCEDURE public.update_stokoa();
');





        $this->execute('DROP FUNCTION if exists public.infostokbarang_fn();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.infostokbarang_fn()
  RETURNS TABLE(barang_id integer, barang_kode character varying, barang_nama character varying, barang_namalainnya character varying, barang_merk character varying, kelompokbarang_id integer, kelompokbarang_nama character varying, golonganbarang_id integer, golonganbarang_nama character varying, satuankecil_id integer, satuanunit_nama character varying, satuan1_id integer, satuan1_nama character varying, satuan2_id integer, satuan2_nama character varying, qty_awal double precision, qty_masuk double precision, qty_keluar double precision, qty_dipesan double precision, qty_tersedia double precision, qty_sisa double precision, barang_harganetto double precision, barang_hargajual double precision, barang_persendiskon double precision, barang_hpp double precision, barang_min double precision, barang_max double precision, barang_average double precision) AS
\$BODY\$
BEGIN 
  RETURN QUERY 
    SELECT 
        barang_m.barang_id::int4,
        barang_m.barang_kode::VARCHAR,
        barang_m.barang_nama::VARCHAR,
        barang_m.barang_namalainnya::VARCHAR,
        barang_m.barang_merk::VARCHAR,
        barang_m.kelompokbarang_id::int4,
        kelompokbarang_m.kelompokbarang_nama::VARCHAR,
        barang_m.golonganbarang_id::int4,
        fgetnamalookup(barang_m.golonganbarang_id)::VARCHAR ,
        barang_m.satuankecil_id::int4,
        satuanunit_m.satuanunit_nama::VARCHAR,
        barang_m.satuan1_id::int4,
        satuan1.satuanunit_nama::VARCHAR,
        barang_m.satuan2_id::int4,
        satuan2.satuanunit_nama::VARCHAR,
        stokbarang_r.qty_awal::float8,
        stokbarang_r.qty_masuk::float8,
        stokbarang_r.qty_keluar::float8,
        stokbarang_r.qty_dipesan::float8,
        stokbarang_r.qty_tersedia::float8,
        stokbarang_r.qty_sisa::float8,
        barang_m.barang_harganetto::float8,
        barang_m.barang_hargajual::float8,
        barang_m.barang_persendiskon::float8,
        barang_m.barang_hpp::float8,
        barang_m.barang_min::float8 ,
        barang_m.barang_max::float8,
        barang_m.barang_average::float8
    FROM stokbarang_r
    JOIN barang_m ON stokbarang_r.barang_id = barang_m.barang_id
    JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
    LEFT JOIN satuanunit_m ON barang_m.satuankecil_id = satuanunit_m.satuanunit_id
    LEFT JOIN satuanunit_m satuan1 ON barang_m.satuan1_id = satuan1.satuanunit_id 
    LEFT JOIN satuanunit_m satuan2 ON barang_m.satuan2_id = satuan2.satuanunit_id 
    LEFT JOIN supplier_m ON barang_m.supplier_id = supplier_m.supplier_id;

END; 
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;");

        $this->execute('ALTER FUNCTION public.infostokbarang_fn()
  OWNER TO postgres;');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200227_035638_migratefunction_20200227 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200227_035638_migratefunction_20200227 cannot be reverted.\n";

        return false;
    }
    */
}
