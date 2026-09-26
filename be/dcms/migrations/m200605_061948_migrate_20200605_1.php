<?php

use yii\db\Migration;

/**
 * Class m200605_061948_migrate_20200605_1
 */
class m200605_061948_migrate_20200605_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."detailpemesananobatalkes_v";');

        $this->execute("
            CREATE VIEW \"public\".\"detailpemesananobatalkes_v\" AS  SELECT pesanobatalkes_t.pesanobatalkes_id,
    pesanobatalkes_t.tglpemesanan,
    pesanobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    pesanobatalkes_t.nopemesanan,
    pesanobatalkes_t.keterangan_pesan,
    pesanobatalkes_t.ruanganpemesan_id,
    ruangpemesan.ruangan_id AS ruangan_pemesan_id,
    ruangpemesan.ruangan_nama AS ruangan_pemesan,
    instalasipesan.instalasi_id AS instalasi_pemesan_id,
    instalasipesan.instalasi_nama AS instalasi_pemesan,
    pesanobatdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_nama AS obatalkes_namalain,
    obatalkes_m.obatalkes_nama,
    pesanobatdetail_t.jumlah_pesan,
    COALESCE(qty_diterima.qty_diterima, (0)::double precision) AS jumlah_diterima,
    pesanobatalkes_t.tglmintadikirim,
    pesanobatdetail_t.mutasiobatdetail_id,
    pesanobatdetail_t.satuankecil_id,
    pesanobatdetail_t.pesanobatdetail_id,
    pesanobatdetail_t.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuan_besar,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    pesanobatdetail_t.jumlah_input AS qty_besar,
    obatalkes_m.harganetto,
    obatalkes_m.hargamaksimum,
    obatalkes_m.hargaminimum,
    obatalkes_m.hargaratarata,
    obatalkes_m.discount,
    pesanobatdetail_t.satuan_pemesanan,
    sat_pemesanan.satuanunit_nama AS satuan_pemesanan_nama,
    pesanobatdetail_t.jumlah_pesan AS qty_kecil,
    pesanobatalkes_t.statuspesan,
    COALESCE(stok_pengirim.qty_tersedia, (0)::double precision) AS stok_pengirim,
    COALESCE(stok_pemesan.qty_tersedia, (0)::double precision) AS stok_pemesan,
    mutasi.jumlah_mutasi,
    mutasi.satuan_kirim,
    (COALESCE(mutasi.jumlah_mutasi, (0)::double precision) / (COALESCE(NULLIF(pesanobatdetail_t.jumlah_pesan, (0)::double precision), (1)::double precision) / COALESCE(NULLIF(pesanobatdetail_t.jumlah_input, (0)::double precision), (1)::double precision))) AS jumlah_input_mutasi
   FROM (((((((((((((pesanobatdetail_t
     JOIN pesanobatalkes_t ON ((pesanobatdetail_t.pesanobatalkes_id = pesanobatalkes_t.pesanobatalkes_id)))
     JOIN ruangan_m ON ((pesanobatalkes_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ruangpemesan ON ((pesanobatalkes_t.ruanganpemesan_id = ruangpemesan.ruangan_id)))
     JOIN instalasi_m instalasipesan ON ((ruangpemesan.instalasi_id = instalasipesan.instalasi_id)))
     JOIN obatalkes_m ON ((pesanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m satuan_besar ON ((pesanobatdetail_t.satuanbesar_id = satuan_besar.satuanunit_id)))
     LEFT JOIN satuanunit_m satuan_kecil ON ((pesanobatdetail_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     LEFT JOIN satuanunit_m sat_pemesanan ON ((pesanobatdetail_t.satuan_pemesanan = sat_pemesanan.satuanunit_id)))
     LEFT JOIN ( SELECT stokobatalkes_r.ruangan_id,
            stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.qty_tersedia
           FROM stokobatalkes_r
          WHERE (stokobatalkes_r.is_deleted = false)) stok_pengirim ON (((pesanobatalkes_t.ruangan_id = stok_pengirim.ruangan_id) AND (pesanobatdetail_t.obatalkes_id = stok_pengirim.obatalkes_id))))
     LEFT JOIN ( SELECT stokobatalkes_r.ruangan_id,
            stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.qty_tersedia
           FROM stokobatalkes_r
          WHERE (stokobatalkes_r.is_deleted = false)) stok_pemesan ON (((pesanobatalkes_t.ruanganpemesan_id = stok_pemesan.ruangan_id) AND (pesanobatdetail_t.obatalkes_id = stok_pemesan.obatalkes_id))))
     LEFT JOIN ( SELECT mutasiobatdetail_t.pesanobatdetail_id,
            terimamutasiobatdetail_t.jmlterima AS qty_diterima
           FROM ((terimamutasiobatdetail_t
             JOIN mutasiobatdetail_t ON ((terimamutasiobatdetail_t.mutasiobatdetail_id = mutasiobatdetail_t.mutasiobatdetail_id)))
             JOIN mutasiobatruangan_t ON ((mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id)))
          WHERE (mutasiobatruangan_t.status_mutasi = 400)) qty_diterima ON ((pesanobatdetail_t.pesanobatdetail_id = qty_diterima.pesanobatdetail_id)))
     LEFT JOIN ( SELECT mutasiobatdetail_t.pesanobatdetail_id,
            mutasiobatdetail_t.jumlah_input,
            mutasiobatdetail_t.jumlah_mutasi,
            mutasiobatdetail_t.satuankecil_id,
            satuanunit_m.satuanunit_nama AS satuan_kirim
           FROM (mutasiobatdetail_t
             LEFT JOIN satuanunit_m ON ((mutasiobatdetail_t.satuankecil_id = satuanunit_m.satuanunit_id)))) mutasi ON ((pesanobatdetail_t.pesanobatdetail_id = mutasi.pesanobatdetail_id)))
  WHERE ((pesanobatdetail_t.is_active = true) AND (pesanobatdetail_t.is_deleted = false));");

        $this->execute('DROP VIEW if exists "public"."infostokobatdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infostokobatdetail_v\" AS  SELECT proses.obatalkes_id,
    sum((proses.qtystok_in - proses.qtystok_out)) AS stok_sistem,
    proses.obatalkes_nama,
    proses.nobatch,
    proses.tglkadaluarsa,
    proses.harganetto,
    proses.instalasi_nama,
    proses.ruangan_nama,
    proses.periodestokobat_id,
    proses.tglperiodestok_awal AS tglperiodeposting_awal,
    proses.tglperiodestok_akhir AS tglperiodeposting_akhir,
    proses.ruangan_id,
    proses.instalasi_id,
    proses.sop_obatalkes_id,
    proses.periodestok_nama
   FROM ( SELECT
                CASE
                    WHEN (stokobatalkes_t.stokobatalkesasal_id IS NULL) THEN stokobatalkes_t.stokobatalkes_id
                    ELSE stokobatalkes_t.stokobatalkesasal_id
                END AS id_stok,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.nobatch,
            stokobatalkes_t.tglkadaluarsa,
            obatalkes_m.obatalkes_nama,
            obatalkes_m.harganetto AS harganetto2,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            stokobatalkes_r.periodestokobat_id,
            periodestokobat_m.tglperiodestok_awal,
            periodestokobat_m.tglperiodestok_akhir,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id,
            formstokopname_t.obatalkes_id AS sop_obatalkes_id,
            formstokopname_t.stokopnamedetail_id AS sop_stokopnamedetail_id,
            periodestokobat_m.periodestok_nama,
            konfigfarmasi_k.hargaygdigunakan,
            obatalkes_m.hargamaksimum,
            obatalkes_m.hargaminimum,
            obatalkes_m.hargaratarata,
                CASE
                    WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'MAX'::text) THEN obatalkes_m.hargamaksimum
                    WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'MIN'::text) THEN obatalkes_m.hargaminimum
                    WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'AVG'::text) THEN obatalkes_m.hargaratarata
                    ELSE obatalkes_m.harganetto
                END AS harganetto
           FROM (((((((stokobatalkes_t
             JOIN obatalkes_m ON ((stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN ruangan_m ON ((stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
             LEFT JOIN ( SELECT formstokopname_t_1.formstokopname_id,
                    formstokopname_t_1.stokopnamedetail_id,
                    formstokopname_t_1.obatalkes_id,
                    formstokopname_t_1.formulirstokopname_id,
                    formstokopname_t_1.volume_stok,
                    formstokopname_t_1.periodestok_id,
                    formstokopname_t_1.ruangan_id,
                    formstokopname_t_1.additional_data,
                    formstokopname_t_1.created_date,
                    formstokopname_t_1.created_by,
                    formstokopname_t_1.modified_count,
                    formstokopname_t_1.last_modified_date,
                    formstokopname_t_1.last_modified_by,
                    formstokopname_t_1.is_deleted,
                    formstokopname_t_1.is_active,
                    formstokopname_t_1.deleted_date,
                    formstokopname_t_1.deleted_by,
                    formstokopname_t_1.nobatch,
                    formstokopname_t_1.stokobatalkes_id,
                    formstokopname_t_1.tglkadaluarsa
                   FROM formstokopname_t formstokopname_t_1
                  WHERE (formstokopname_t_1.stokopnamedetail_id IS NULL)) formstokopname_t ON (((stokobatalkes_t.obatalkes_id = formstokopname_t.obatalkes_id) AND (stokobatalkes_t.ruangan_id = formstokopname_t.ruangan_id))))
             JOIN stokobatalkes_r ON (((stokobatalkes_t.obatalkes_id = stokobatalkes_r.obatalkes_id) AND (stokobatalkes_t.ruangan_id = stokobatalkes_r.ruangan_id) AND (stokobatalkes_r.is_periode = true))))
             LEFT JOIN periodestokobat_m ON ((stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id)))
             JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
          WHERE ((formstokopname_t.formstokopname_id IS NULL))) proses
  GROUP BY proses.obatalkes_id, proses.obatalkes_nama, proses.nobatch, proses.tglkadaluarsa, proses.instalasi_nama, proses.ruangan_nama, proses.harganetto, proses.periodestokobat_id, proses.tglperiodestok_awal, proses.tglperiodestok_akhir, proses.ruangan_id, proses.instalasi_id, proses.sop_obatalkes_id, proses.periodestok_nama;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200605_061948_migrate_20200605_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200605_061948_migrate_20200605_1 cannot be reverted.\n";

        return false;
    }
    */
}
