<?php

use yii\db\Migration;

/**
 * Class m190717_085833_indikatorrs_r_function
 */
class m190717_085833_indikatorrs_r_function extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
         DROP FUNCTION IF exists public.indikatorrs_r();
        ');

          $this->execute('
            CREATE OR REPLACE FUNCTION public.indikatorrs_r()
  RETURNS character varying AS
$BODY$
    DECLARE vout VARCHAR;
    vmonth VARCHAR;
    vyear VARCHAR;
    vcount int4;
    vlama_rawat int4;
    vhari_perawatan int4;
    vtotal_tt int4;
    vjumlah_hari int4;
    vtotal_pasienkeluar int4;
    vtotal_mati int4;
    vtotal_matilebih48jam int4;
    
BEGIN
    vout := \'Affected rows: 1\';
    vmonth := RIGHT(CONCAT(\'0\', (EXTRACT(MONTH FROM CURRENT_DATE))::VARCHAR),2);
    vyear := EXTRACT(YEAR FROM CURRENT_DATE)::VARCHAR;

    --kolom : \'lama_rawat\' (yg di ambil total yang bulan ini saja)--
    SELECT
        SUM(x.lama_rawat) INTO vlama_rawat
    FROM
    (SELECT
        to_char(pasienadmisi_t.tgl_admisi, \'YYYY-MM\'::text)AS tgl_masuk,
        to_char(pasienpulang_t.tglpasienpulang, \'YYYY-MM\'::text) AS tgl_pulang,
        fgetpasien_nama(pendaftaran_t.pasien_id) AS pasien_nama,
        pasienpulang_t.lama_rawat
    FROM pasienadmisi_t
        JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id and pasienpulang_t.is_deleted=FALSE
        JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id and pendaftaran_t.is_deleted=FALSE) x
    GROUP BY x.tgl_pulang;


    --kolom : \'hari_perawatan\'--
    SELECT
        CASE vmonth 
            WHEN \'01\' THEN sum(jan)
            WHEN \'02\' THEN sum(feb)
            WHEN \'03\' THEN sum(mar)
            WHEN \'04\' THEN sum(apr)
            WHEN \'05\' THEN sum(mei)
            WHEN \'06\' THEN sum(jun)
            WHEN \'07\' THEN sum(jul)
            WHEN \'08\' THEN sum(agus)
            WHEN \'09\' THEN sum(sept)
            WHEN \'10\' THEN sum(okt)
            WHEN \'11\' THEN sum(nov)
            ELSE sum(des)
        END INTO vhari_perawatan
    FROM hariperawatan_r
    WHERE periode = vyear::int4
    GROUP BY periode;


    --kolom \'jumlah_tempat_tidur\' --

    SELECT
        COUNT(x.no_bed) INTO vtotal_tt
    FROM
    (SELECT 
    kamarruangan_m.ruangan_id,
    ruangan_m.ruangan_nama as ruangan,
    kamarruangan_m.kamarruangan_nokamar as kamar,
    kamartempattidur_m.no_tempattidur as no_bed
    FROM kamartempattidur_m
    JOIN kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                                                 AND kamarruangan_m.is_deleted=FALSE 
                                                 AND kamarruangan_m.is_active=TRUE
    JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id 
                                        AND ruangan_m.is_deleted=FALSE
                                        AND ruangan_m.is_active=TRUE
    WHERE kamartempattidur_m.is_deleted=FALSE 
    AND kamartempattidur_m.is_active=true) x;

--kolom \'jumlah_hari_periode\' --

SELECT  
    DATE_PART(\'days\', 
        DATE_TRUNC(\'month\', NOW()) 
        + \'1 MONTH\'::INTERVAL 
        - \'1 DAY\'::INTERVAL
    ) INTO vjumlah_hari;

--kolom \'jumlah_pasien_keluar\' (yg di ambil total yang bulan ini saja)--
    SELECT
        COUNT(x.pasien_nama) INTO vtotal_pasienkeluar
    FROM
    (
    SELECT
    to_char(pasienadmisi_t.tgl_admisi, \'YYYY-MM\'::text)AS tgl_masuk,
    to_char(pasienpulang_t.tglpasienpulang, \'YYYY-MM\'::text) AS tgl_pulang,
     fgetpasien_nama(pendaftaran_t.pasien_id) AS pasien_nama,
     pasienpulang_t.lama_rawat
    FROM pasienadmisi_t
    JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id and pasienpulang_t.is_deleted=FALSE
    JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id and pendaftaran_t.is_deleted=FALSE
    ) x
    GROUP BY x.tgl_pulang;

--kolom \'pasien_mati_all\' (yg di ambil total yang bulan ini saja)--

    SELECT
        COUNT(x.pasien_nama) INTO vtotal_mati
    FROM
    (
        SELECT
            to_char(pasienadmisi_t.tgl_admisi, \'YYYY-MM\'::text)AS tgl_masuk,
            to_char(pasienpulang_t.tglpasienpulang, \'YYYY-MM\'::text) AS tgl_pulang,
            to_char(pasienpulang_t.tgl_meninggal, \'YYYY-MM\'::text) AS tgl_meninggal,
            fgetpasien_nama(pendaftaran_t.pasien_id) AS pasien_nama,
            pasienpulang_t.lama_rawat
        FROM pasienadmisi_t
        JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id and pasienpulang_t.is_deleted=FALSE
        JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id and pendaftaran_t.is_deleted=FALSE
        WHERE pasienpulang_t.carakeluar_id= 4
    ) x
    GROUP BY x.tgl_meninggal;

--kolom \'pasien_mati_48_jam\' (yg di ambil total yang bulan ini saja)--
    SELECT
        count(x.pasien_nama) INTO vtotal_matilebih48jam
    FROM
    (
    SELECT
        to_char(pasienadmisi_t.tgl_admisi, \'YYYY-MM\'::text)AS tgl_masuk,
        to_char(pasienpulang_t.tglpasienpulang, \'YYYY-MM\'::text) AS tgl_pulang,
        to_char(pasienpulang_t.tgl_meninggal, \'YYYY-MM\'::text) AS tgl_meninggal,
        fgetpasien_nama(pendaftaran_t.pasien_id) AS pasien_nama,
        pasienpulang_t.lama_rawat
    FROM pasienadmisi_t
        JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id and pasienpulang_t.is_deleted=FALSE
        JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id and pendaftaran_t.is_deleted=FALSE
        WHERE pasienpulang_t.carakeluar_id= 4 and pasienpulang_t.kondisikeluar_id = 5
    ) x
    GROUP BY x.tgl_meninggal;
    
    DELETE FROM indikatorrs_r
    WHERE bulan = vmonth
    AND tahun = vyear;
    
    INSERT INTO indikatorrs_r(bulan, tahun, lama_dirawat, hari_perawatan, jumlah_tempat_tidur, jumlah_hari_periode, jumlah_pasien_keluar, pasien_mati_48_jam, pasien_mati_all, created_date, is_deleted, is_active)     
    VALUES (
        vmonth, vyear, COALESCE(vlama_rawat,0), COALESCE(vhari_perawatan,0), COALESCE(vtotal_tt,0), vjumlah_hari, COALESCE(vtotal_pasienkeluar,0), COALESCE(vtotal_mati,0), COALESCE(vtotal_matilebih48jam,0), CURRENT_DATE, FALSE, TRUE);
        
    RETURN vout;
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
            ');

           $this->execute('
         ALTER FUNCTION public.indikatorrs_r()
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190717_085833_indikatorrs_r_function cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190717_085833_indikatorrs_r_function cannot be reverted.\n";

        return false;
    }
    */
}
