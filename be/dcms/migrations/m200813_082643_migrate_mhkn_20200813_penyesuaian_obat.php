<?php

use yii\db\Migration;

/**
 * Class m200813_082643_migrate_mhkn_20200813_penyesuaian_obat
 */
class m200813_082643_migrate_mhkn_20200813_penyesuaian_obat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."purchasereq_t" ADD COLUMN "is_prcyto" bool DEFAULT false;');

        $this->execute('DROP VIEW if exists "public"."infopurchasereqdetail_v";');

        $this->execute("CREATE VIEW \"public\".\"infopurchasereqdetail_v\" AS  SELECT purchasereq_t.purchasereq_id,
    purchasereqdetail_t.purchasereqdetail_id,
    purchasereq_t.no_pr,
    purchasereqdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    purchasereqdetail_t.qty_input,
    purchasereqdetail_t.qty_konversi,
    purchasereqdetail_t.satuan_id,
    satuan_1.satuanunit_nama AS satuan,
    purchasereqdetail_t.satuankonversi_id,
    satuan_2.satuanunit_nama AS satuan_konversi,
    purchasereqdetail_t.catatan,
    COALESCE(obat_sisa.qty_sisa, (0)::double precision) AS stok,
    satuan_stok.satuanunit_nama AS satuan_stok
   FROM ((((((purchasereq_t
     JOIN purchasereqdetail_t ON ((purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id)))
     JOIN obatalkes_m ON ((purchasereqdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m satuan_1 ON ((purchasereqdetail_t.satuan_id = satuan_1.satuanunit_id)))
     LEFT JOIN satuanunit_m satuan_2 ON ((purchasereqdetail_t.satuankonversi_id = satuan_2.satuanunit_id)))
     LEFT JOIN satuanunit_m satuan_stok ON ((obatalkes_m.satuankecil_id = satuan_stok.satuanunit_id)))
     LEFT JOIN ( SELECT stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.ruangan_id,
            sum(stokobatalkes_r.qty_sisa) AS qty_sisa
           FROM stokobatalkes_r
          GROUP BY stokobatalkes_r.obatalkes_id, stokobatalkes_r.ruangan_id) obat_sisa ON (((purchasereqdetail_t.obatalkes_id = obat_sisa.obatalkes_id) AND (purchasereq_t.ruangan_id = obat_sisa.ruangan_id))))
  WHERE ((purchasereq_t.is_deleted = false) AND (purchasereqdetail_t.is_deleted = false));");

        $this->execute('ALTER TABLE "public"."infopurchasereqdetail_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infopurchasereq_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopurchasereq_v\" AS  SELECT purchasereq_t.purchasereq_id,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr,
    purchasereq_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan,
    purchasereq_t.pegawai_id,
    pegawai_m.nama_pegawai AS pegawai,
    purchasereq_t.reference,
    purchasereq_t.status,
    fgetnamalookup((purchasereq_t.status)::integer) AS status_pr,
    purchasereq_t.is_prcyto,
        CASE
            WHEN (purchasereq_t.is_prcyto = true) THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS pr_cyto
   FROM ((purchasereq_t
     JOIN ruangan_m ON ((purchasereq_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN pegawai_m ON ((purchasereq_t.pegawai_id = pegawai_m.pegawai_id)))
  WHERE (purchasereq_t.is_deleted = false);");

        $this->execute('ALTER TABLE "public"."infopurchasereq_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."obatalkes_v";');

        $this->execute("
            CREATE VIEW \"public\".\"obatalkes_v\" AS  SELECT hit.obatalkes_id,
    hit.obatalkes_nama,
    hit.jenisobatalkes_id,
    hit.jenisobatalkes_nama,
    hit.ven_id,
    hit.ven,
    hit.groupinacbg_id,
    hit.lead_time,
    hit.avg_usage,
    hit.min_order,
    hit.max_order,
    hit.nilai_ro,
    hit.margin,
    hit.ppn,
    hit.disc,
    hit.hn_last,
    hit.a1 AS hn_last_margin,
    hit.a2 AS hn_last_diskon,
    hit.a3 AS hn_last_margin_diskon,
    hit.a4 AS hn_last_ppn,
    hit.a5 AS hargajual_last,
    hit.hn_min,
    hit.b1 AS hn_min_margin,
    hit.b2 AS hn_min_diskon,
    hit.b3 AS hn_min_margin_diskon,
    hit.b4 AS hn_min_ppn,
    hit.b5 AS hargajual_min,
    hit.hn_max,
    hit.c1 AS hn_max_margin,
    hit.c2 AS hn_max_diskon,
    hit.c3 AS hn_max_margin_diskon,
    hit.c4 AS hn_max_ppn,
    hit.c5 AS hargajual_max,
    hit.hn_avg,
    hit.d1 AS hn_avg_margin,
    hit.d2 AS hn_avg_diskon,
    hit.d3 AS hn_avg_margin_diskon,
    hit.d4 AS hn_avg_ppn,
    hit.d5 AS hargajual_avg,
    hit.harga_jual AS hargaygdipakai,
    hit.harganetto AS harganetto_ygdipakai,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.hn_max
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.hn_min
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.hn_avg
            ELSE hit.hn_last
        END AS harga_sugesstion,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN
            CASE (COALESCE(hit.harganetto, (0)::double precision) - COALESCE(hit.hn_max, (0)::double precision))
                WHEN 0 THEN 0
                ELSE 1
            END
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN
            CASE (COALESCE(hit.harganetto, (0)::double precision) - COALESCE(hit.hn_min, (0)::double precision))
                WHEN 0 THEN 0
                ELSE 1
            END
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN
            CASE (COALESCE(hit.harganetto, (0)::double precision) - COALESCE(hit.hn_avg, (0)::double precision))
                WHEN 0 THEN 0
                ELSE 1
            END
            ELSE
            CASE (COALESCE(hit.harganetto, (0)::double precision) - COALESCE(hit.hn_last, (0)::double precision))
                WHEN 0 THEN 0
                ELSE 1
            END
        END AS selisih,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c1
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b1
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d1
            ELSE hit.a1
        END AS hn_margin,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c2
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b2
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d2
            ELSE hit.a2
        END AS hn_diskon,
        CASE
            WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c4
            WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b4
            WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d4
            ELSE hit.a4
        END AS hn_ppn,
    hit.satuankecil_id,
    hit.satuankecil_nama,
    hit.group_jenisobat,
    hit.obatalkes_kode
   FROM ( SELECT obatalkes_m.obatalkes_id,
            obatalkes_m.obatalkes_nama,
            obatalkes_m.jenisobatalkes_id,
            jenisobatalkes_m.jenisobatalkes_nama,
            obatalkes_m.ven AS ven_id,
            fgetnamalookup(obatalkes_m.ven) AS ven,
            obatalkes_m.harganetto,
            obatalkes_m.groupinacbg_id,
            obatalkes_m.lead_time,
            obatalkes_m.avg_usage,
            obatalkes_m.min_order,
            obatalkes_m.max_order,
            obatalkes_m.nilai_ro,
            obatalkes_m.hargaterakhir AS hn_last,
            obatalkes_m.hargaminimum AS hn_min,
            obatalkes_m.hargamaksimum AS hn_max,
            obatalkes_m.hargaratarata AS hn_avg,
            konfigfarmasi_k.persenppn AS ppn,
            fgetpersenmargin(obatalkes_m.harganetto) AS margin,
            konfigfarmasi_k.persen_diskon AS disc,
            konfigfarmasi_k.hargaygdigunakan,
            (obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir)) / (100)::double precision)) AS a1,
            (((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS a2,
            ((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir)) / (100)::double precision)) - (((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS a3,
            ((((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir)) / (100)::double precision)) - (((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS a4,
            (((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir)) / (100)::double precision)) - (((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir)) / (100)::double precision)) - (((obatalkes_m.hargaterakhir + ((obatalkes_m.hargaterakhir * fgetpersenmargin(obatalkes_m.hargaterakhir)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS a5,
            (obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum)) / (100)::double precision)) AS b1,
            (((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS b2,
            ((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum)) / (100)::double precision)) - (((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS b3,
            ((((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum)) / (100)::double precision)) - (((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS b4,
            (((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum)) / (100)::double precision)) - (((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum)) / (100)::double precision)) - (((obatalkes_m.hargaminimum + ((obatalkes_m.hargaminimum * fgetpersenmargin(obatalkes_m.hargaminimum)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS b5,
            (obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum)) / (100)::double precision)) AS c1,
            (((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS c2,
            ((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum)) / (100)::double precision)) - (((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS c3,
            ((((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum)) / (100)::double precision)) - (((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS c4,
            (((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum)) / (100)::double precision)) - (((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum)) / (100)::double precision)) - (((obatalkes_m.hargamaksimum + ((obatalkes_m.hargamaksimum * fgetpersenmargin(obatalkes_m.hargamaksimum)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS c5,
            (obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata)) / (100)::double precision)) AS d1,
            (((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS d2,
            ((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata)) / (100)::double precision)) - (((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS d3,
            ((((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata)) / (100)::double precision)) - (((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS d4,
            (((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata)) / (100)::double precision)) - (((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata)) / (100)::double precision)) - (((obatalkes_m.hargaratarata + ((obatalkes_m.hargaratarata * fgetpersenmargin(obatalkes_m.hargaratarata)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS d5,
            obatalkes_m.satuankecil_id,
            satuan_kecil.satuanunit_nama AS satuankecil_nama,
            (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) - (((obatalkes_m.harganetto + ((obatalkes_m.harganetto * fgetpersenmargin(obatalkes_m.harganetto)) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS harga_jual,
            jenisobatalkes_m.group_jenisobat,
            obatalkes_m.obatalkes_kode
           FROM (((obatalkes_m
             JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
             JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
             LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id)))
          WHERE ((obatalkes_m.is_active = true) AND (obatalkes_m.is_deleted = false))) hit;");

        $this->execute('ALTER TABLE "public"."obatalkes_v" OWNER TO "postgres";');

      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200813_082643_migrate_mhkn_20200813_penyesuaian_obat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200813_082643_migrate_mhkn_20200813_penyesuaian_obat cannot be reverted.\n";

        return false;
    }
    */
}
