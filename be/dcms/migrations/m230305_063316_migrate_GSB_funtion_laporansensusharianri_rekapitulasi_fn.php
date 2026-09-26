<?php

use yii\db\Migration;

/**
 * Class m230305_063316_migrate_GSB_funtion_laporansensusharianri_rekapitulasi_fn
 */
class m230305_063316_migrate_GSB_funtion_laporansensusharianri_rekapitulasi_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."laporansensusharianri_rekapitulasi_fn"("xfirstdate" date, "xlastdate" date, "xruangan_id" int4, "xkelaspelayanan_id" int4, "xstatus_pasien" int4)
  RETURNS TABLE("vtanggal" varchar, "pasien_hari_sebelumnya" int4, "pasien_masuk" int4, "pasien_pindahan" int4, "jml_123" int4, "keluar_hidup" int4, "keluar_dipindahkan" int4, "keluar_meninggaljml" int4, "keluar_meninggalkur48" int4, "keluar_meninggalleb48" int4, "keluar_rujukrslain" int4, "jml_567" int4) AS $BODY$

DECLARE
    vdays int4;
    vdate int4;
    tanggal date;
    vhari varchar;
    vtemp_pasien_awal int4;

BEGIN
SELECT (xlastdate::date - xfirstdate::date) + 1 INTO vdays;
vdate := 0;

WHILE vdate < vdays
LOOP
    IF vdate = 0
    THEN
        SELECT DATE_TRUNC(\'day\', xfirstdate::TIMESTAMP) INTO vtanggal;
    ELSE
        SELECT DATE_TRUNC(\'day\', vtanggal::TIMESTAMP) + \'1 DAY\'::INTERVAL INTO vtanggal;
    END IF;
    
    
    -- FORMAT TANGGAL
    vtanggal := vtanggal::DATE;
    
    -- PASIEN AWAL
    SELECT 0 INTO pasien_hari_sebelumnya;
    
--  IF (vdate = 0)
--  THEN
--      SELECT f_getpasienawal(vtanggal::DATE, xruangan_id, xkelaspelayanan_id) INTO pasien_hari_sebelumnya;
--  ELSE
--      pasien_hari_sebelumnya := vtemp_pasien_awal;
--  END IF; 
    
--  IF (vdate = 0)
--  THEN
--      SELECT
--          pasien_akhir INTO pasien_hari_sebelumnya
--      FROM sensuspasienranap_r
--      WHERE ruangan_id = xruangan_id
--      AND kelaspelayanan_id = xkelaspelayanan_id
--      AND tgl_sensus = vtanggal::DATE;
--  ELSE
--      SELECT
--          pasien_akhir INTO pasien_hari_sebelumnya
--      FROM sensuspasienranap_r
--      WHERE tgl_sensus = vtanggal::DATE;
        
    --END IF;
    
    -- PASIEN MASUK
    SELECT f_getpasienmasuk(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO pasien_masuk;
    
    -- PASIEN PINDAHAN
    SELECT f_getpasienpindahan(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO pasien_pindahan;
--  SELECT
--          pasien_pindahan INTO pasien_pindahan
--      FROM sensuspasienranap_r
--      WHERE ruangan_id = xruangan_id
--      AND kelaspelayanan_id = xkelaspelayanan_id
--      AND tgl_sensus = vtanggal::DATE;
    
    -- JUMLAH KOLOM 1+2+3
    --SELECT pasien_hari_sebelumnya + pasien_masuk + pasien_pindahan INTO jml_123;
    SELECT 0 INTO jml_123;
    
    -- KELUAR HIDUP
    SELECT f_getkeluarhidup(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO keluar_hidup;
    
    -- KELUAR DIPINDAHKAN
    SELECT f_getkeluardipindahkan(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO keluar_dipindahkan;
    
    -- KELUAR MENINGGAL KURANG DARI 48 JAM
    SELECT f_getkeluarmeninggalkur48(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO keluar_meninggalkur48;
    
    -- KELUAR MENINGGAL LEBIH DARI 48 JAM
    SELECT f_getkeluarmeninggalleb48(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO keluar_meninggalleb48;
    
    -- JUMLAH KELUAR MENINGGAL
    SELECT keluar_meninggalkur48 + keluar_meninggalleb48 INTO keluar_meninggaljml;
    
    
    SELECT f_getkelurrujukrslain(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO keluar_rujukrslain;
    
    -- JUMLAH KOLOM 5+6+7
    --SELECT keluar_hidup + keluar_dipindahkan + keluar_meninggaljml INTO jml_567;
    SELECT 0 INTO jml_567;
    
    
    -- PASIEN AKHIR
    --SELECT jml_123 - jml_567 INTO pasien_akhir_rekap;
    
    -- SET TEMP UNTUK PASIEN AWAL
    --vtemp_pasien_awal := pasien_akhir_rekap;
    
    
    -- RETURN DATA ROW
    RETURN NEXT;
    
    vdate  := vdate + 1;
END LOOP;

END
$BODY$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230305_063316_migrate_GSB_funtion_laporansensusharianri_rekapitulasi_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230305_063316_migrate_GSB_funtion_laporansensusharianri_rekapitulasi_fn cannot be reverted.\n";

        return false;
    }
    */
}
