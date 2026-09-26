<?php

use yii\db\Migration;

/**
 * Class m191007_230003_t_form_so_barang
 */
class m191007_230003_t_form_so_barang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."formsobarangdetail_t" 
                          ADD COLUMN "tgl_kadaluarsa" date,
                          ALTER COLUMN "formsobarang_id" SET NOT NULL;');

         $this->execute('DROP TRIGGER if exists no_formulir ON public.formsobarang_t;');

         $this->execute('DROP FUNCTION if exists public.no_formsobarang();');

         $this->execute("
            CREATE OR REPLACE FUNCTION public.no_formsobarang()
  RETURNS trigger AS
\$BODY\$
DECLARE
    vId integer := 31; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    v_Nomor varchar;
    vFormsobrg integer;
    
BEGIN
    
    SELECT 
        formsobarang_id 
    INTO
        vFormsobrg
    FROM 
        formsobarang_t 
    ORDER BY    
        formsobarang_id 
    DESC LIMIT 1;
    
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
    
    UPDATE 
        formsobarang_t
    SET
        noformulir = v_Nomor
    WHERE
        formsobarang_id = vFormsobrg;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

         $this->execute('ALTER FUNCTION public.no_formsobarang()
  OWNER TO postgres;');

         $this->execute('CREATE TRIGGER no_formulir
  AFTER INSERT
  ON public.formsobarang_t
  FOR EACH ROW
  EXECUTE PROCEDURE public.no_formsobarang();');

         $this->execute('DROP VIEW if exists public.infostokbarangdetail_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.infostokbarangdetail_v AS 
 SELECT proses.barang_id,
    sum(proses.qtystok_in - proses.qtystok_out) AS stok_sistem,
    proses.barang_nama,
    proses.kelompok_barang,
    proses.subkelompok_barang,
    proses.nobatch,
    proses.tglkadaluarsa,
    proses.barang_harganetto,
    proses.instalasi_nama,
    proses.ruangan_nama,
    proses.periodestokbarang_id,
    proses.tglperiodestok_awal,
    proses.tglperiodestok_akhir,
    proses.ruangan_id,
    proses.instalasi_id,
    proses.sop_barang_id,
    proses.sop_sopbarangdetail_id,
    proses.id_stok
   FROM ( SELECT
                CASE
                    WHEN stokbarang_t.stokbarangasal_id IS NULL THEN stokbarang_t.stokbarang_id
                    ELSE stokbarang_t.stokbarangasal_id
                END AS id_stok,
            stokbarang_t.barang_id,
            stokbarang_t.qtystok_in,
            stokbarang_t.qtystok_out,
            stokbarang_t.nobatch,
            stokbarang_t.tglkadaluarsa,
            barang_m.barang_nama,
            subkelompokbarang_m.subkelompok_nama AS subkelompok_barang,
            kelompokbarang_m.kelompokbarang_nama AS kelompok_barang,
            barang_m.barang_harganetto,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            stokbarang_r.periodestokbarang_id,
            periodestokbarang_m.tglperiodestok_awal,
            periodestokbarang_m.tglperiodestok_akhir,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id,
            formsobarangdetail_t.barang_id AS sop_barang_id,
            formsobarangdetail_t.stokopnamebarangdetail_id AS sop_sopbarangdetail_id
           FROM stokbarang_t
             JOIN barang_m ON stokbarang_t.barang_id = barang_m.barang_id
             LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
             LEFT JOIN subkelompokbarang_m ON barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id
             JOIN ruangan_m ON stokbarang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN formsobarangdetail_t ON stokbarang_t.barang_id = formsobarangdetail_t.barang_id AND stokbarang_t.ruangan_id = formsobarangdetail_t.ruangan_id
             JOIN stokbarang_r ON stokbarang_t.barang_id = stokbarang_t.barang_id AND stokbarang_t.ruangan_id = stokbarang_r.ruangan_id
             LEFT JOIN periodestokbarang_m ON stokbarang_r.periodestokbarang_id = periodestokbarang_m.periodestokbarang_id
          WHERE stokbarang_t.stokbarang_aktif = true AND (formsobarangdetail_t.barang_id IS NOT NULL AND formsobarangdetail_t.stokopnamebarangdetail_id IS NOT NULL OR formsobarangdetail_t.barang_id IS NULL AND formsobarangdetail_t.stokopnamebarangdetail_id IS NULL)) proses
  GROUP BY proses.barang_id, proses.barang_nama, proses.nobatch, proses.tglkadaluarsa, proses.instalasi_nama, proses.ruangan_nama, proses.barang_harganetto, proses.periodestokbarang_id, proses.tglperiodestok_awal, proses.tglperiodestok_akhir, proses.ruangan_id, proses.instalasi_id, proses.sop_barang_id, proses.sop_sopbarangdetail_id, proses.id_stok, proses.kelompok_barang, proses.subkelompok_barang;
");

         $this->execute('ALTER TABLE public.infostokbarangdetail_v
  OWNER TO postgres;');

         
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191007_230003_t_form_so_barang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191007_230003_t_form_so_barang cannot be reverted.\n";

        return false;
    }
    */
}
