<?php

use yii\db\Migration;

/**
 * Class m201112_050935_migrate_mhkn_20201112_laporansensuspasienranap_fn
 */
class m201112_050935_migrate_mhkn_20201112_laporansensuspasienranap_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"laporansensuspasienranap_fn\"(\"xdate\" date, \"xruangan_id\" int4, \"xkelaspelayanan_id\" int4)
  RETURNS TABLE(\"vtanggal\" varchar, \"pasien_awal\" int4, \"pasien_masuk\" int4, \"pasien_pindahan\" int4, \"jml_234\" int4, \"keluar_hidup\" int4, \"keluar_dipindahkan\" int4, \"keluar_meninggaljml\" int4, \"keluar_meninggalkur48\" int4, \"keluar_meninggalleb48\" int4, \"jml_678\" int4, \"pasien_akhir\" int4) AS \$BODY\$

DECLARE
    vdays int4;
    vdate int4;
    vtemp_pasien_awal int4;
    vmonth VARCHAR;
    vyear VARCHAR;

BEGIN
vtanggal := '01';
vdays := 0;
vdate := 1;
vmonth := 0;
vyear := 0;

SELECT DATE_PART('days', DATE_TRUNC('month', xdate::TIMESTAMP) + '1 MONTH'::INTERVAL - '1 DAY'::INTERVAL) INTO vdays;
SELECT DATE_PART('month', xdate::TIMESTAMP) INTO vmonth;
SELECT DATE_PART('year', xdate::TIMESTAMP) INTO vyear;
SELECT CONCAT(vyear, '-', vmonth, '-', vtanggal) INTO vtanggal;

WHILE vdate < vdays + 1
LOOP
    -- FORMAT TANGGAL
    vtanggal := vtanggal::DATE;

    -- PASIEN AWAL
    SELECT f_getpasienawal(vtanggal::DATE, xruangan_id, xkelaspelayanan_id) INTO pasien_awal;
    
    -- PASIEN MASUK
    SELECT f_getpasienmasuk(vtanggal::DATE, xruangan_id, xkelaspelayanan_id) INTO pasien_masuk;
    
    -- PASIEN PINDAHAN
    SELECT f_getpasienpindahan(vtanggal::DATE, xruangan_id, xkelaspelayanan_id) INTO pasien_pindahan;
    
    -- JUMLAH KOLOM 2+3+4
    SELECT pasien_awal + pasien_masuk + pasien_pindahan INTO jml_234;
    
    -- KELUAR HIDUP
    SELECT f_getkeluarhidup(vtanggal::DATE, xruangan_id, xkelaspelayanan_id) INTO keluar_hidup;
    
    -- KELUAR DIPINDAHKAN
    SELECT f_getkeluardipindahkan(vtanggal::DATE, xruangan_id, xkelaspelayanan_id) INTO keluar_dipindahkan;
    
    -- KELUAR MENINGGAL KURANG DARI 48 JAM
    SELECT f_getkeluarmeninggalkur48(vtanggal::DATE, xruangan_id, xkelaspelayanan_id) INTO keluar_meninggalkur48;
    
    -- KELUAR MENINGGAL LEBIH DARI 48 JAM
    SELECT f_getkeluarmeninggalleb48(vtanggal::DATE, xruangan_id, xkelaspelayanan_id) INTO keluar_meninggalleb48;
    
    -- JUMLAH KELUAR MENINGGAL
    SELECT keluar_meninggalkur48 + keluar_meninggalleb48 INTO keluar_meninggaljml;
    
    -- JUMLAH KOLOM 6+7+8
    SELECT keluar_hidup + keluar_dipindahkan + keluar_meninggaljml INTO jml_678;
    
    -- PASIEN AKHIR
    SELECT jml_234 - jml_678 INTO pasien_akhir;
    
    -- SET TEMP UNTUK PASIEN AWAL
    vtemp_pasien_awal := pasien_akhir;
    
    -- RETURN DATA ROW
    RETURN NEXT;
    
    vdate  := vdate + 1;
    vtanggal := NULL;
    
    IF (vdate < 10)
    THEN
        SELECT CONCAT(vyear, '-', vmonth, '-', '0', vdate) INTO vtanggal;
    ELSE
        SELECT CONCAT(vyear, '-', vmonth, '-', vdate) INTO vtanggal;
    END IF;
    
END LOOP;

END
\$BODY\$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000
             ;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201112_050935_migrate_mhkn_20201112_laporansensuspasienranap_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201112_050935_migrate_mhkn_20201112_laporansensuspasienranap_fn cannot be reverted.\n";

        return false;
    }
    */
}
