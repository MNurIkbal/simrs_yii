<?php

use yii\db\Migration;

/**
 * Class m220624_122736_migrate_cssd_infostokbarang_v
 */
class m220624_122736_migrate_cssd_infostokbarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infostokbarang_v;');

        $this->execute("CREATE VIEW \"public\".\"infostokbarang_v\" AS
           SELECT hit.periodestok_id,
           hit.periodestok_nama,
           hit.instalasi_id,
           hit.instalasi_nama,
           hit.ruangan_id,
           hit.ruangan_nama,
           hit.barang_id,
           hit.barang_nama,
           hit.qty_masuk,
           hit.qty_keluar,
           (COALESCE(hit.jml_mutasi, (0)::double precision) + COALESCE((hit.qtycssd_det)::double precision, (0)::double precision)) AS qty_dipesan,
           (hit.total - (COALESCE(hit.jml_mutasi, (0)::double precision) + COALESCE((hit.qtycssd_det)::double precision, (0)::double precision))) AS qty_tersedia,
           hit.total AS qty_stok,
           hit.tglperiodestok_awal,
           hit.tglperiodestok_akhir,
           hit.barang_kode,
           hit.ppn,
           hit.harga_jual,
           hit.satuankecil_id,
           hit.satuankecil_nama,
           hit.satuansedang_id,
           hit.satuansedang_nama,
           hit.satuanbesar_id,
           hit.satuanbesar_nama,
           hit.on_ro,
           hit.on_po,
           hit.nilai_ro,
           hit.is_kadaluarsa,
           hit.ppn_konf,
           hit.margin,
           hit.disc,
           hit.hargaygdigunakan,
           hit.harga_netto,
           hit.a1 AS hn_last_margin,
           hit.a2 AS hn_last_diskon,
           hit.a3 AS hn_last_margin_diskon,
           hit.a4 AS hn_last_ppn,
           hit.a5 AS hargajual_last,
           hit.harga_min,
           hit.b1 AS hn_min_margin,
           hit.b2 AS hn_min_diskon,
           hit.b3 AS hn_min_margin_diskon,
           hit.b4 AS hn_min_ppn,
           hit.b5 AS hargajual_min,
           hit.harga_max,
           hit.c1 AS hn_max_margin,
           hit.c2 AS hn_max_diskon,
           hit.c3 AS hn_max_margin_diskon,
           hit.c4 AS hn_max_ppn,
           hit.c5 AS hargajual_max,
           hit.barang_average,
           hit.d1 AS hn_avg_margin,
           hit.d2 AS hn_avg_diskon,
           hit.d3 AS hn_avg_margin_diskon,
           hit.d4 AS hn_avg_ppn,
           hit.d5 AS hargajual_avg,
           CASE
           WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c5
           WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b5
           WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d5
           ELSE hit.a5
           END AS hargaygdipakai,
           CASE
           WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.harga_max
           WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.harga_min
           WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.barang_average
           ELSE hit.harga_netto
           END AS harganetto_ygdipakai,
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
           hit.kelompokbarang_id,
           hit.kelompokbarang_nama,
           hit.reference_mutasi
           FROM ( SELECT periodestokbarang_m.periodestokbarang_id AS periodestok_id,
           periodestokbarang_m.periodestokbarang_nama AS periodestok_nama,
           instalasi_m.instalasi_id,
           instalasi_m.instalasi_nama,
           stokbarang_r.ruangan_id,
           ruangan_m.ruangan_nama,
           stokbarang_r.barang_id,
           barang_m.barang_nama,
           stokbarang_r.qty_masuk,
           stokbarang_r.qty_keluar,
           stokbarang_r.qty_dipesan,
           stokbarang_r.qty_tersedia,
           stokbarang_r.qty_sisa,
           kartustok.total,
           periodestokbarang_m.tglperiodestok_awal,
           periodestokbarang_m.tglperiodestok_akhir,
           barang_m.barang_kode,
           barang_m.barang_ppn AS ppn,
           barang_m.barang_hargajual AS harga_jual,
           barang_m.barang_harganetto AS harga_netto,
           barang_m.barang_max AS harga_max,
           barang_m.barang_min AS harga_min,
           barang_m.barang_average,
           barang_m.on_ro,
           barang_m.on_po,
           barang_m.satuankecil_id,
           satuanunit_m.satuanunit_nama AS satuankecil_nama,
           NULL::text AS satuansedang_id,
           NULL::text AS satuansedang_nama,
           NULL::text AS satuanbesar_id,
           NULL::text AS satuanbesar_nama,
           barang_m.nilai_ro,
           barang_m.is_kadaluarsa,
           konfigfarmasi_k.persenppn AS ppn_konf,
           konfigfarmasi_k.persenmargin AS margin,
           konfigfarmasi_k.persen_diskon AS disc,
           konfigfarmasi_k.hargaygdigunakan,
           (barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) AS a1,
           (((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS a2,
           ((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS a3,
           ((((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS a4,
           (((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS a5,
           (barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) AS b1,
           (((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS b2,
           ((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS b3,
           ((((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS b4,
           (((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS b5,
           (barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) AS c1,
           (((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS c2,
           ((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS c3,
           ((((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS c4,
           (((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS c5,
           (barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) AS d1,
           (((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS d2,
           ((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS d3,
           ((((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS d4,
           (((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS d5,
           barang_m.kelompokbarang_id,
           kelompokbarang_m.kelompokbarang_nama,
           mutasi.jml AS jml_mutasi,
           mutasi.reference AS reference_mutasi,
           cssd_detail.qty AS qtycssd_det
           FROM ((((((((((stokbarang_r
           JOIN ( SELECT a.ruangan_id,
           a.ruangan_nama,
           a.instalasi_id
           FROM ruangan_m a) ruangan_m ON ((stokbarang_r.ruangan_id = ruangan_m.ruangan_id)))
           JOIN ( SELECT a.instalasi_id,
           a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
           JOIN ( SELECT a.barang_id,
           a.barang_nama,
           a.barang_kode,
           a.satuankecil_id,
           a.kelompokbarang_id,
           a.barang_ppn,
           a.barang_hargajual,
           a.barang_harganetto,
           a.barang_max,
           a.barang_min,
           a.barang_average,
           a.on_ro,
           a.on_po,
           a.nilai_ro,
           a.is_kadaluarsa,
           a.is_active,
           a.is_deleted
           FROM barang_m a) barang_m ON ((stokbarang_r.barang_id = barang_m.barang_id)))
           LEFT JOIN ( SELECT a.periodestokbarang_id,
           a.periodestokbarang_nama,
           a.tglperiodestok_awal,
           a.tglperiodestok_akhir
           FROM periodestokbarang_m a) periodestokbarang_m ON ((stokbarang_r.periodestokbarang_id = periodestokbarang_m.periodestokbarang_id)))
           JOIN ( SELECT a.is_deleted,
           a.persenppn,
           a.persenmargin,
           a.persen_diskon,
           a.hargaygdigunakan
           FROM konfigfarmasi_k a) konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
           JOIN ( SELECT a.satuanunit_id,
           a.satuanunit_nama
           FROM satuanunit_m a) satuanunit_m ON ((barang_m.satuankecil_id = satuanunit_m.satuanunit_id)))
           LEFT JOIN ( SELECT a.kelompokbarang_id,
           a.kelompokbarang_nama
           FROM kelompokbarang_m a) kelompokbarang_m ON ((barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id)))
           LEFT JOIN ( SELECT a.ruangan_id,
           a.barang_id,
           sum((a.qtystok_in - a.qtystok_out)) AS total
           FROM stokbarang_t a
           WHERE (a.is_deleted = false)
           GROUP BY a.ruangan_id, a.barang_id) kartustok ON (((kartustok.ruangan_id = stokbarang_r.ruangan_id) AND (kartustok.barang_id = stokbarang_r.barang_id))))
           LEFT JOIN ( SELECT mutasibarang_t.ruanganasal_id AS ruangan_id,
           a.barang_id,
           sum(a.qty_mutasi) AS jml,
           string_agg((mutasibarang_t.nomutasi_barang)::text, ','::text) AS reference
           FROM (mutasibarangdetail_t a
           LEFT JOIN ( SELECT a1.mutasibarang_id,
           a1.ruanganasal_id,
           a1.nomutasi_barang,
           a1.is_deleted,
           a1.status_mutasi
           FROM mutasibarang_t a1) mutasibarang_t ON ((mutasibarang_t.mutasibarang_id = a.mutasibarang_id)))
           WHERE ((a.is_deleted IS FALSE) AND (mutasibarang_t.is_deleted IS FALSE) AND (mutasibarang_t.status_mutasi = 401))
           GROUP BY mutasibarang_t.ruanganasal_id, a.barang_id) mutasi ON (((mutasi.ruangan_id = stokbarang_r.ruangan_id) AND (mutasi.barang_id = stokbarang_r.barang_id))))
           LEFT JOIN ( SELECT a.ruanganasal_id,
           b.barangalkes_id,
           sum(b.qty) AS qty
           FROM (cssd_t a
           JOIN ( SELECT b_1.cssd_id,
           b_1.barangalkes_id,
           b_1.qty,
           b_1.is_alkes
           FROM cssddet_t b_1
           WHERE (b_1.is_deleted = false)) b ON ((a.cssd_id = b.cssd_id)))
           WHERE ((b.is_alkes = false) AND (a.status_cssd = 1190) AND (a.is_deleted = false))
           GROUP BY a.ruanganasal_id, b.barangalkes_id) cssd_detail ON (((cssd_detail.barangalkes_id = stokbarang_r.barang_id) AND (cssd_detail.ruanganasal_id = stokbarang_r.ruangan_id))))
           WHERE ((barang_m.is_active = true) AND (barang_m.is_deleted = false) AND (stokbarang_r.is_periode = true))) hit
        ");
        
        $this->execute('ALTER TABLE public.infostokbarang_v OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_122736_migrate_cssd_infostokbarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_122736_migrate_cssd_infostokbarang_v cannot be reverted.\n";

        return false;
    }
    */
}
