<?php

use yii\db\Migration;

/**
 * Class m191021_031655_mutasibarang_1666
 */
class m191021_031655_mutasibarang_1666 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."mutasibarang_t" 
                        ALTER COLUMN "status_mutasi" SET DEFAULT 401;');

/*infomutasibarangdetail_v*/
         $this->execute('DROP VIEW if exists public.infomutasibarangdetail_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.infomutasibarangdetail_v AS 
 SELECT mutasibarangdetail_t.mutasibarangdetail_id,
    mutasibarang_t.mutasibarang_id,
    mutasibarang_t.nomutasi_barang,
    mutasibarang_t.tgl_mutasibarang,
    instalasi_tujuan.instalasi_id AS instalasi_tujuan_id,
    instalasi_tujuan.instalasi_nama,
    ruangan_tujuan.ruangan_id AS ruangan_tujuan_id,
    ruangan_tujuan.ruangan_nama,
    instalasi_m.instalasi_id AS instalasi_asal_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    ruangan_m.ruangan_id AS ruangan_asal_id,
    ruangan_m.ruangan_nama AS ruangan_asal,
    mutasibarangdetail_t.qty_mutasi,
    barang_m.barang_id,
    barang_m.barang_nama,
    mutasibarangdetail_t.satuankecil_id AS satuanbrg,
    satuan_kecil.satuanunit_nama AS lookup_value,
    mutasibarangdetail_t.satuankecil_id,
    mutasibarangdetail_t.satuanbesar_id,
    satuan_kecil.satuanunit_nama AS satuankecil_nama,
    satuan_besar.satuanunit_nama AS satuanbesar_nama,
    mutasibarangdetail_t.harga_netto,
    mutasibarangdetail_t.jumlah_input AS qty_input,
    pesanbarangdetail_t.jumlah_input AS qty_dipesan,
    pesanbarang_t.no_pemesanan
   FROM mutasibarangdetail_t
     JOIN mutasibarang_t ON mutasibarangdetail_t.mutasibarang_id = mutasibarang_t.mutasibarang_id
     JOIN barang_m ON mutasibarangdetail_t.barang_id = barang_m.barang_id
     JOIN ruangan_m ON mutasibarang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangan_tujuan ON mutasibarang_t.ruangantujuan_id = ruangan_tujuan.ruangan_id
     JOIN instalasi_m instalasi_tujuan ON ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id
     JOIN satuanunit_m satuan_besar ON mutasibarangdetail_t.satuanbesar_id = satuan_besar.satuanunit_id
     JOIN satuanunit_m satuan_kecil ON mutasibarangdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
     JOIN pesanbarangdetail_t ON mutasibarangdetail_t.pesanbarangdetail_id = pesanbarangdetail_t.pesanbarangdetail_id
     JOIN pesanbarang_t ON pesanbarang_t.pesanbarang_id = pesanbarangdetail_t.pesanbarang_id
  WHERE mutasibarang_t.is_active = true AND mutasibarangdetail_t.is_deleted = false;");

         $this->execute('ALTER TABLE public.infomutasibarangdetail_v
  OWNER TO postgres;');

         $this->execute('DROP TRIGGER if exists no_terimamutasibarang ON public.terimamutasibarang_t;');

         $this->execute('DROP FUNCTION if exists public.no_terimamutasibarang();');

         $this->execute("
            CREATE OR REPLACE FUNCTION public.no_terimamutasibarang()
  RETURNS trigger AS
\$BODY\$
DECLARE
    vId integer := 32; -- 
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

    NEW.noterimamutasi = v_Nomor;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

         $this->execute('ALTER FUNCTION public.no_terimamutasibarang()
  OWNER TO postgres;');

         $this->execute('CREATE TRIGGER no_terimamutasibarang
                      BEFORE INSERT
                      ON public.terimamutasibarang_t
                      FOR EACH ROW
                      EXECUTE PROCEDURE public.no_terimamutasibarang();
                    ');
         
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191021_031655_mutasibarang_1666 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191021_031655_mutasibarang_1666 cannot be reverted.\n";

        return false;
    }
    */
}
