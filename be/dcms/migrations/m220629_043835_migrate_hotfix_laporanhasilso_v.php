<?php

use yii\db\Migration;

/**
 * Class m220629_043835_migrate_hotfix_laporanhasilso_v
 */
class m220629_043835_migrate_hotfix_laporanhasilso_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanhasilso_v";');

        $this->execute("
           CREATE VIEW \"public\".\"laporanhasilso_v\" AS  SELECT a.stokopnamedetail_id,
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
    a.weighted_avg_backup,
    a.weighted_avg,
    a.wa,
    a.stok_sistem,
    a.stok_fisik,
    a.selisih,
    a.selisih * a.weighted_avg AS total_harga_selisi,
    a.tgl_implementasi,
    a.selisih_weighted_avg
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
                    WHEN lr.weighted_avg IS NULL AND (COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem) = 0::double precision THEN stokopnamedetail_t.weighted_avg
                    WHEN lr.weighted_avg IS NOT NULL THEN lr.weighted_avg::double precision
                    WHEN lr.weighted_avg IS NULL AND (COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem) <> 0::double precision THEN obatalkes_m.harganetto
                    ELSE NULL::double precision
                END AS weighted_avg_backup,
            lr.weighted_avg AS wa,
            formstokopname_t.volume_stok AS stok_sistem,
            COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) AS stok_fisik,
            COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem AS selisih,
                CASE
                    WHEN lr.weighted_avg IS NOT NULL THEN lr.weighted_avg::double precision * (stokopnamedetail_t.volume_fisik - stokopnamedetail_t.volume_sistem)
                    ELSE obatalkes_m.harganetto * (stokopnamedetail_t.volume_fisik - stokopnamedetail_t.volume_sistem)
                END AS total_harga_selisih,
            stokopname_t.tgl_implementasi,
            COALESCE(stokopnamedetail_t.weighted_avg, logasetobat.weighted_avg::double precision, obatalkes_m.harganetto, nulllogasetobat.harga_weighted_avg) AS weighted_avg,
            COALESCE(stokopnamedetail_t.weighted_avg, logasetobat.weighted_avg::double precision, obatalkes_m.harganetto, nulllogasetobat.harga_weighted_avg) * (COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem) AS selisih_weighted_avg
           FROM stokopname_t
             JOIN (
						 SELECT a.stokopnamedetail_id,a.stokopname_id,a.obatalkes_id,a.is_deleted,a.revisi_stok,a.volume_fisik,a.volume_sistem,a.weighted_avg
						 	from stokopnamedetail_t a)stokopnamedetail_t ON stokopname_t.stokopname_id = stokopnamedetail_t.stokopname_id AND stokopnamedetail_t.is_deleted = false
             LEFT JOIN (SELECT a.created_date,a.noformulir,a.formulirstokopname_id from formulirstokopname_t a) formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
             LEFT JOIN( SELECT a.pegawai_id,a.nama_pegawai from pegawai_m a) peg_verif_so ON stokopname_t.pegawaiverifikasi_id = peg_verif_so.pegawai_id
             LEFT JOIN (select a.ruangan_id,a.ruangan_nama,a.instalasi_id FROM ruangan_m a) ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN (SELECT a.instalasi_id,a.instalasi_nama from instalasi_m a)instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN (SELECT a.obatalkes_id,a.obatalkes_kode,a.obatalkes_nama,a.jenisobatalkes_id,a.harganetto,a.satuankecil_id from obatalkes_m a)obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN (SELECT a.jenisobatalkes_id,a.jenisobatalkes_nama from jenisobatalkes_m a)  jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
             LEFT JOIN (SELECT a.satuanunit_id,a.satuanunit_nama from satuanunit_m a)  sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
             LEFT JOIN (SELECT a.formstokopname_id,a.volume_stok,a.stokopnamedetail_id from  formstokopname_t a)formstokopname_t ON stokopnamedetail_t.stokopnamedetail_id = formstokopname_t.stokopnamedetail_id
             LEFT JOIN (SELECT a.stokobatalkes_id,a.stokopnamedetail_id,a.tglstok_in,a.tglstok_out from stokobatalkes_t a )stokobatalkes_t ON stokopnamedetail_t.stokopnamedetail_id = stokobatalkes_t.stokopnamedetail_id
             LEFT JOIN (SELECT a.stokobatalkes_id,a.weighted_avg,a.obatalkes_id,a.ruangan_id from logasetobat_r a) lr ON stokobatalkes_t.stokobatalkes_id = lr.stokobatalkes_id
             LEFT JOIN ( SELECT DISTINCT ON (lr_1.ruangan_id, lr_1.obatalkes_id) lr_1.logasetobat_id,
                    lr_1.obatalkes_id,
                    lr_1.ruangan_id,
                    COALESCE(lr_1.weighted_avg, 0::numeric) AS weighted_avg,
                    lr_1.stokobatalkes_id
                   FROM logasetobat_r lr_1
                  ORDER BY lr_1.ruangan_id, lr_1.obatalkes_id, lr_1.stokobatalkes_id DESC) logasetobat ON stokopnamedetail_t.obatalkes_id = logasetobat.obatalkes_id AND stokopname_t.ruangan_id = logasetobat.ruangan_id
             LEFT JOIN ( SELECT stokobatalkes_t_1.obatalkes_id,
                    sum(stok_1.harga_asset / stok_1.qty_asset) AS harga_weighted_avg
                   FROM stokobatalkes_t stokobatalkes_t_1
                     LEFT JOIN ( SELECT penerimaan_obat.harga * stok_obat.qty_asset AS harga_asset,
                            penerimaan_obat.obatalkes_id,
                            stok_obat.qty_asset
                           FROM ( SELECT penerimaanobatdetail_t.obatalkes_id,
                                    penerimaanobatdetail_t.harga /
CASE
 WHEN penerimaanobatdetail_t.qty_diterima = 0 THEN 1
 ELSE NULL::integer
END::double precision AS harga
                                   FROM penerimaanobatdetail_t
                                UNION ALL
                                 SELECT penerimaansuppdetail_t.obatalkes_id,
                                    penerimaansuppdetail_t.harga_netto /
CASE
 WHEN penerimaansuppdetail_t.qty_kecil = 0 THEN 1
 ELSE NULL::integer
END::double precision AS harga
                                   FROM penerimaansuppdetail_t) penerimaan_obat
                             LEFT JOIN ( SELECT sum(st_ob.qtystok_in - st_ob.qtystok_out) AS qty_asset,
                                    st_ob.obatalkes_id
                                   FROM stokobatalkes_t st_ob
                                  GROUP BY st_ob.obatalkes_id) stok_obat ON penerimaan_obat.obatalkes_id = stok_obat.obatalkes_id) stok_1 ON stokobatalkes_t_1.obatalkes_id = stok_1.obatalkes_id
                  GROUP BY stokobatalkes_t_1.obatalkes_id) nulllogasetobat ON stokopnamedetail_t.obatalkes_id = nulllogasetobat.obatalkes_id
          GROUP BY stokopnamedetail_t.stokopnamedetail_id, stokobatalkes_t.tglstok_in, stokobatalkes_t.tglstok_out, formulirstokopname_t.created_date, formulirstokopname_t.noformulir, stokopname_t.tglverifikasi, peg_verif_so.nama_pegawai, ruangan_m.ruangan_id, instalasi_m.instalasi_id, stokopname_t.nostokopname, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, jenisobatalkes_m.jenisobatalkes_nama, stokopnamedetail_t.obatalkes_id, obatalkes_m.obatalkes_kode, obatalkes_m.obatalkes_nama, nulllogasetobat.harga_weighted_avg, sat_kecil.satuanunit_nama, lr.weighted_avg, obatalkes_m.harganetto, formstokopname_t.volume_stok, logasetobat.weighted_avg, stokopnamedetail_t.volume_fisik, stokopname_t.tgl_implementasi,stokopnamedetail_t.revisi_stok,stokopnamedetail_t.volume_sistem,stokopnamedetail_t.weighted_avg) a; ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220629_043835_migrate_hotfix_laporanhasilso_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220629_043835_migrate_hotfix_laporanhasilso_v cannot be reverted.\n";

        return false;
    }
    */
}
