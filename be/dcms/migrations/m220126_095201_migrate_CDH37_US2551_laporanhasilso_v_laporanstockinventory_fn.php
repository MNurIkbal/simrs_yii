<?php

use yii\db\Migration;

/**
 * Class m220126_095201_migrate_CDH37_US2551_laporanhasilso_v_laporanstockinventory_fn
 */
class m220126_095201_migrate_CDH37_US2551_laporanhasilso_v_laporanstockinventory_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanhasilso_v;');
      
        $this->execute("
          CREATE VIEW \"public\".\"laporanhasilso_v\" AS   SELECT a.stokopnamedetail_id,
    a.kondisi,
    a.tgl_form_so,
    a.no_form_so,
    a.tgl_validasi_so,
    a.validasi_by,
    a.ruangan_id,
    a.instalasi_id,
    a.nostokopname,
    a.instalasi_ruangan,
    a.jenis_obatalkes,
    a.obatalkes_id,
    a.kode_obat,
    a.nama_obat,
    a.satuan_kecil,
    a.weighted_avg,
    a.stok_sistem,
    a.stok_fisik,
    a.selisih,
    a.selisih * a.weighted_avg AS total_harga_selisi,
    a.tgl_implementasi
   FROM ( SELECT stokopnamedetail_t.stokopnamedetail_id,
                CASE
                    WHEN stokobatalkes_t.tglstok_in IS NOT NULL THEN 'IN'::text
                    ELSE 'OUT'::text
                END AS kondisi,
                CASE
                    WHEN stokobatalkes_t.tglstok_in IS NULL THEN max(stokobatalkes_t.tglstok_out)
                    WHEN stokobatalkes_t.tglstok_in IS NOT NULL THEN max(stokobatalkes_t.tglstok_in)
                    ELSE NULL::timestamp without time zone
                END AS tgl_stokopname,
            formulirstokopname_t.created_date AS tgl_form_so,
            formulirstokopname_t.noformulir AS no_form_so,
            stokopname_t.tglverifikasi AS tgl_validasi_so,
            peg_verif_so.nama_pegawai AS validasi_by,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id,
            stokopname_t.nostokopname,
            concat(instalasi_m.instalasi_nama, ' - ', ruangan_m.ruangan_nama) AS instalasi_ruangan,
            jenisobatalkes_m.jenisobatalkes_nama AS jenis_obatalkes,
            stokopnamedetail_t.obatalkes_id,
            obatalkes_m.obatalkes_kode AS kode_obat,
            obatalkes_m.obatalkes_nama AS nama_obat,
            sat_kecil.satuanunit_nama AS satuan_kecil,
                CASE
                    WHEN lr.weighted_avg IS NOT NULL THEN lr.weighted_avg::double precision
                    ELSE obatalkes_m.harganetto
                END AS weighted_avg,
            formstokopname_t.volume_stok AS stok_sistem,
            COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) AS stok_fisik,
            COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem AS selisih,
                CASE
                    WHEN lr.weighted_avg IS NOT NULL THEN lr.weighted_avg::double precision * (stokopnamedetail_t.volume_fisik - stokopnamedetail_t.volume_sistem)
                    ELSE obatalkes_m.harganetto * (stokopnamedetail_t.volume_fisik - stokopnamedetail_t.volume_sistem)
                END AS total_harga_selisi,
            stokopname_t.tgl_implementasi
           FROM stokopname_t
             JOIN stokopnamedetail_t ON stokopname_t.stokopname_id = stokopnamedetail_t.stokopname_id AND stokopnamedetail_t.is_deleted = false
             LEFT JOIN formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
             LEFT JOIN pegawai_m peg_verif_so ON stokopname_t.pegawaiverifikasi_id = peg_verif_so.pegawai_id
             LEFT JOIN ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
             LEFT JOIN satuanunit_m sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
             LEFT JOIN formstokopname_t ON stokopnamedetail_t.stokopnamedetail_id = formstokopname_t.stokopnamedetail_id
             LEFT JOIN stokobatalkes_t ON stokopnamedetail_t.stokopnamedetail_id = stokobatalkes_t.stokopnamedetail_id
             LEFT JOIN logasetobat_r lr ON stokobatalkes_t.stokobatalkes_id = lr.stokobatalkes_id
          WHERE stokobatalkes_t.stokopnamedetail_id IS NOT NULL
          GROUP BY stokopnamedetail_t.stokopnamedetail_id, stokobatalkes_t.tglstok_in, stokobatalkes_t.tglstok_out, formulirstokopname_t.created_date, formulirstokopname_t.noformulir, stokopname_t.tglverifikasi, peg_verif_so.nama_pegawai, ruangan_m.ruangan_id, instalasi_m.instalasi_id, stokopname_t.nostokopname, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, jenisobatalkes_m.jenisobatalkes_nama, stokopnamedetail_t.obatalkes_id, obatalkes_m.obatalkes_kode, obatalkes_m.obatalkes_nama, sat_kecil.satuanunit_nama, lr.weighted_avg, obatalkes_m.harganetto, formstokopname_t.volume_stok, stokopnamedetail_t.volume_fisik, stokopname_t.tgl_implementasi) a;");

                $this->execute('DROP FUNCTION if exists public.laporanstockinventory_fn;');

                $this->execute("
                    CREATE OR REPLACE FUNCTION public.laporanstockinventory_fn(x_date date)
  RETURNS TABLE(ruangan_nama varchar, obatalkes_kode varchar, obatalkes_nama varchar, jenis_obat varchar, is_generik varchar, is_oral varchar, nilai_konv float8, stok_fisik float8, qty_satuankecil float8, qty_satuanbesar float8, stok float8, satuan_besar varchar, satuan_kecil varchar, baseprice_kecil float8, baseprice_besar float8, harga_konversi float8, harga_netto float8, harga float8, total_harga float8, total_satuankecil float8, total_satuanbesar float8) AS \$BODY\$

BEGIN
    RETURN QUERY 
    --select * from laporanstockinventory_fn('2021-12-25')

SELECT *FROM (
        SELECT 
            ruangan_m.ruangan_nama as ruangan_nama,
            obatalkes_m.obatalkes_kode as obatalkes_kode,
            obatalkes_m.obatalkes_nama as obatalkes_nama, 
            jenisobatalkes_m.jenisobatalkes_nama as jenis_obat,
            CASE
                WHEN obatalkes_m.is_generik = TRUE THEN 'Generik'::VARCHAR
                ELSE 'Non Generik'::VARCHAR
            end as is_generik,
                CASE
                WHEN obatalkes_m.is_oral = TRUE THEN 'Oral'::VARCHAR
                ELSE 'Non Oral'::VARCHAR
            end as is_oral,
          konversi.nilai_konversi as nilai_konv,
            stok.fisik as stok_fisik,
            stok.fisik as qty_satuankecil,
            (stok.fisik / konversi.nilai_konversi) as qty_satuanbesar,
            stok.fisik as stok,
            satuanbesar.satuanunit_nama as satuan_besar,
            satuankecil.satuanunit_nama as satuan_kecil,
            coalesce(lr.weighted_avg,obatalkes_m.harganetto) as baseprice_kecil,
            (coalesce(lr.weighted_avg,obatalkes_m.harganetto) * obatalkes_m.kemasan_besar) as baseprice_besar,
            (obatalkes_m.harganetto * konversi.nilai_konversi) as harga_konversi,
            obatalkes_m.harganetto as harga_netto,
           -- (obatalkes_m.harganetto * konversi.nilai_konversi * (stok.fisik / konversi.nilai_konversi)) as harga,
                    (coalesce(lr.weighted_avg,obatalkes_m.harganetto) * konversi.nilai_konversi * (stok.fisik / konversi.nilai_konversi)) as harga,
                    (stok.fisik * (coalesce(lr.weighted_avg,obatalkes_m.harganetto)))  as total_harga,
          (stok.fisik * (coalesce(lr.weighted_avg,obatalkes_m.harganetto))) as total_satuankecil,
          ((stok.fisik / konversi.nilai_konversi) * (COALESCE(lr.weighted_avg,obatalkes_m.harganetto) * konversi.nilai_konversi)) as total_satuanbesar
            FROM (
            SELECT 
                ruangan_id,
                obatalkes_id,
                SUM(qtystok_in-qtystok_out) as fisik
            FROM stokobatalkes_t
            WHERE (CASE 
                            WHEN stokobatalkes_t.tglstok_in is not null THEN stokobatalkes_t.tglstok_in 
                            ELSE stokobatalkes_t.tglstok_out 
                        END) <= x_date::date + interval '23 hours 59 minutes'
            GROUP BY ruangan_id,obatalkes_id
        ) stok
        LEFT join ruangan_m on ruangan_m.ruangan_id = stok.ruangan_id
        LEFT join obatalkes_m on obatalkes_m.obatalkes_id = stok.obatalkes_id
        LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
        LEFT JOIN (SELECT 
                                    satuankecil_id,
                                    satuanbesar_id,
                                    nilai_konversi,
                                    obatalkes_id,
                                    is_deleted,
                                    is_active
                             FROM satuankonversi_m) konversi ON konversi.satuanbesar_id = obatalkes_m.satuanbesar_id 
                                                                                            AND konversi.satuankecil_id = obatalkes_m.satuankecil_id 
                                                                                            AND konversi.obatalkes_id = stok.obatalkes_id
                                                                                            and konversi.is_deleted = false 
                                                                                            and konversi.is_active = true
        LEFT JOIN satuanunit_m satuankecil ON satuankecil.satuanunit_id = obatalkes_m.satuankecil_id
        LEFT JOIN satuanunit_m satuanbesar ON satuanbesar.satuanunit_id = obatalkes_m.satuanbesar_id
                
                LEFt JOIN ( select lr.ruangan_id ,lr.obatalkes_id ,lr.weighted_avg 
from logasetobat_r lr 
inner join (
    select  max(stokobatalkes_id) as stokobatalkes_id,ruangan_id ,obatalkes_id 
    from logasetobat_r
    group by ruangan_id ,obatalkes_id
) tm on lr.ruangan_id = tm.ruangan_id and lr.stokobatalkes_id = tm.stokobatalkes_id and lr.obatalkes_id = tm.obatalkes_id ) lr on lr.obatalkes_id = stok.obatalkes_id and lr.ruangan_id = stok.ruangan_id
                
                --LEFT JOIN (select lr3.weighted_avg,lr3.obatalkes_id,lr3.ruangan_id from logasetobat_r lr3 
                --order by lr3.stokobatalkes_id desc) lr3 on stok.obatalkes_id = lr3.obatalkes_id and lr3.ruangan_id =ruangan_m.ruangan_id
        ORDER by ruangan_m.ruangan_nama,obatalkes_m.obatalkes_kode
) x;
    
END
\$BODY\$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000;");

   
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220126_095201_migrate_CDH37_US2551_laporanhasilso_v_laporanstockinventory_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220126_095201_migrate_CDH37_US2551_laporanhasilso_v_laporanstockinventory_fn cannot be reverted.\n";

        return false;
    }
    */
}
