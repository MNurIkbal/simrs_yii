<?php

use yii\db\Migration;

/**
 * Class m220810_071142_migrate_mhg_2197_infoobatexpired_v
 */
class m220810_071142_migrate_mhg_2197_infoobatexpired_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infoobatexpired_v";');

        $this->execute("
           CREATE VIEW \"public\".\"infoobatexpired_v\" AS  SELECT a.obatalkes_id,
    sum(a.stok) AS stok,
    sum(a.stok_exp) AS stok_exp,
    a.jumlah,
    a.status_mutasi,
    a.obatalkes_nama,
    a.satuankecil_id,
    a.satuan_kecil,
    a.tglkadaluarsa,
    a.harganetto,
    a.instalasi_nama,
    a.ruangan_nama,
    a.periodestokobat_id,
    a.tglperiodeposting_awal,
    a.tglperiodeposting_akhir,
    a.ruangan_id,
    a.instalasi_id,
    a.margin,
    a.ppn,
    a.disc,
    sum(a.stok_exp) * lr.weighted_avg::double precision AS cost_wa
   FROM ( SELECT hit.obatalkes_id,
            sum(hit.qtystok_in - hit.qtystok_out) AS stok,
                CASE
                    WHEN mutasi.status_mutasi = 401 THEN sum(hit.qtystok_in - mutasi.jumlah)
                    ELSE sum(hit.qtystok_in - hit.qtystok_out)
                END AS stok_exp,
            mutasi.jumlah,
            mutasi.status_mutasi,
            hit.obatalkes_nama,
            hit.satuankecil_id,
            hit.s_kecil AS satuan_kecil,
            hit.tglkadaluarsa,
            hit.harganetto,
            hit.instalasi_nama,
            hit.ruangan_nama,
            hit.periodestokobat_id,
            hit.tglperiodestok_awal AS tglperiodeposting_awal,
            hit.tglperiodestok_akhir AS tglperiodeposting_akhir,
            hit.ruangan_id,
            hit.instalasi_id,
            array_agg(hit.id_stok) AS id_stok,
            hit.margin,
            hit.ppn,
            hit.disc,
            hit.hn_last,
            hit.hn_min,
            hit.hn_max,
            hit.hn_avg
           FROM ( SELECT
                        CASE
                            WHEN stokobatalkes_t.stokobatalkesasal_id IS NULL THEN stokobatalkes_t.stokobatalkes_id
                            ELSE stokobatalkes_t.stokobatalkesasal_id
                        END AS id_stok,
                    stokobatalkes_t.obatalkes_id,
                    stokobatalkes_t.qtystok_in,
                    stokobatalkes_t.qtystok_out,
                    stokobatalkes_t.tglkadaluarsa,
                    obatalkes_m.obatalkes_nama,
                    obatalkes_m.satuankecil_id,
                    satuan_kecil.satuanunit_nama AS s_kecil,
                    instalasi_m.instalasi_nama,
                    ruangan_m.ruangan_id,
                    ruangan_m.ruangan_nama,
                    NULL::text AS periodestokobat_id,
                    NULL::text AS tglperiodestok_awal,
                    NULL::text AS tglperiodestok_akhir,
                    instalasi_m.instalasi_id,
                    obatalkes_m.harganetto,
                    obatalkes_m.hargaterakhir AS hn_last,
                    obatalkes_m.hargaminimum AS hn_min,
                    obatalkes_m.hargamaksimum AS hn_max,
                    obatalkes_m.hargaratarata AS hn_avg,
                    konfigfarmasi_k.persenppn AS ppn,
                    konfigfarmasi_k.persenmargin AS margin,
                    konfigfarmasi_k.persen_diskon AS disc
                   FROM stokobatalkes_t
                     LEFT JOIN ( SELECT a_1.obatalkes_id,
                            a_1.obatalkes_nama,
                            a_1.satuankecil_id,
                            a_1.harganetto,
                            a_1.hargaterakhir,
                            a_1.hargaminimum,
                            a_1.hargamaksimum,
                            a_1.hargaratarata
                           FROM obatalkes_m a_1) obatalkes_m ON stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id
                     LEFT JOIN ( SELECT a_1.ruangan_id,
                            a_1.ruangan_nama,
                            a_1.instalasi_id
                           FROM ruangan_m a_1) ruangan_m ON stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT a_1.instalasi_id,
                            a_1.instalasi_nama
                           FROM instalasi_m a_1) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     LEFT JOIN ( SELECT a_1.satuanunit_id,
                            a_1.satuanunit_nama
                           FROM satuanunit_m a_1) satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
                     LEFT JOIN ( SELECT a_1.persenppn,
                            a_1.persenmargin,
                            a_1.persen_diskon,
                            a_1.is_deleted
                           FROM konfigfarmasi_k a_1) konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
                  GROUP BY stokobatalkes_t.stokobatalkesasal_id, stokobatalkes_t.obatalkes_id, stokobatalkes_t.qtystok_in, stokobatalkes_t.qtystok_out, stokobatalkes_t.tglkadaluarsa, obatalkes_m.obatalkes_nama, obatalkes_m.satuankecil_id, satuan_kecil.satuanunit_nama, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, stokobatalkes_t.stokobatalkes_id, ruangan_m.ruangan_id, instalasi_m.instalasi_id, obatalkes_m.harganetto, obatalkes_m.hargaterakhir, obatalkes_m.hargaminimum, obatalkes_m.hargamaksimum, obatalkes_m.hargaratarata, konfigfarmasi_k.persenppn, konfigfarmasi_k.persenmargin, konfigfarmasi_k.persen_diskon) hit
             LEFT JOIN ( SELECT mutasiobatdetail_t.obatalkes_id,
                    mutasiobatdetail_t.tgl_kadaluarsa,
                    sum(mutasiobatdetail_t.jumlah_mutasi) AS jumlah,
                    mutasiobatruangan_t.status_mutasi,
                    mutasiobatruangan_t.ruanganasal_id AS ruangan_id
                   FROM mutasiobatdetail_t
                     JOIN ( SELECT a_1.status_mutasi,
                            a_1.ruanganasal_id,
                            a_1.mutasiobatruangan_id
                           FROM mutasiobatruangan_t a_1) mutasiobatruangan_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
                  GROUP BY mutasiobatdetail_t.obatalkes_id, mutasiobatdetail_t.tgl_kadaluarsa, mutasiobatruangan_t.status_mutasi, mutasiobatruangan_t.ruanganasal_id) mutasi ON hit.obatalkes_id = mutasi.obatalkes_id AND hit.tglkadaluarsa = mutasi.tgl_kadaluarsa AND hit.ruangan_id = mutasi.ruangan_id
          GROUP BY hit.obatalkes_id, mutasi.jumlah, mutasi.status_mutasi, hit.obatalkes_nama, hit.satuankecil_id, hit.s_kecil, hit.tglkadaluarsa, hit.harganetto, hit.instalasi_nama, hit.ruangan_nama, hit.periodestokobat_id, hit.tglperiodestok_awal, hit.tglperiodestok_akhir, hit.ruangan_id, hit.instalasi_id, hit.margin, hit.ppn, hit.disc, hit.hn_last, hit.hn_min, hit.hn_avg, hit.hn_max) a
     LEFT JOIN ( SELECT lr_1.ruangan_id,
            lr_1.obatalkes_id,
            sum(lr_1.weighted_avg) AS weighted_avg
           FROM logasetobat_r lr_1
             JOIN ( SELECT max(logasetobat_r.stokobatalkes_id) AS stokobatalkes_id,
                    logasetobat_r.ruangan_id,
                    logasetobat_r.obatalkes_id
                   FROM logasetobat_r
                  GROUP BY logasetobat_r.ruangan_id, logasetobat_r.obatalkes_id) tm ON lr_1.ruangan_id = tm.ruangan_id AND lr_1.stokobatalkes_id = tm.stokobatalkes_id AND lr_1.obatalkes_id = tm.obatalkes_id
          GROUP BY lr_1.ruangan_id, lr_1.obatalkes_id) lr ON lr.obatalkes_id = a.obatalkes_id AND lr.ruangan_id = a.ruangan_id
  GROUP BY a.obatalkes_id, a.jumlah, a.status_mutasi, a.obatalkes_nama, a.satuankecil_id, a.satuan_kecil, a.tglkadaluarsa, a.harganetto, a.instalasi_nama, a.ruangan_nama, a.periodestokobat_id, a.tglperiodeposting_awal, a.tglperiodeposting_akhir, a.ruangan_id, a.instalasi_id, a.margin, a.ppn, a.disc, lr.weighted_avg;
 ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220810_071142_migrate_mhg_2197_infoobatexpired_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220810_071142_migrate_mhg_2197_infoobatexpired_v cannot be reverted.\n";

        return false;
    }
    */
}
