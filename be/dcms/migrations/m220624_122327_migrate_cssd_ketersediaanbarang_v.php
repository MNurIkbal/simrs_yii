<?php

use yii\db\Migration;

/**
 * Class m220624_122327_migrate_cssd_ketersediaanbarang_v
 */
class m220624_122327_migrate_cssd_ketersediaanbarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.ketersediaanbarang_v;');

        $this->execute("CREATE VIEW \"public\".\"ketersediaanbarang_v\" AS
           SELECT hit.instalasi_id,
           hit.instalasi_nama,
           hit.ruangan_id,
           hit.ruangan_nama,
           hit.barang_id,
           hit.barang_nama,
           hit.barang_kode,
           hit.satuankecil_id,
           hit.satuanbesar_nama,
           hit.satuan2_id,
           hit.satuansedang_nama,
           hit.satuankecil_nama,
           hit.satuan1_id,
           hit.kelompokbarang_id,
           hit.kelompokbarang_nama,
           hit.subkelompokbarang_id,
           hit.subkelompok_nama,
           hit.barang_harganetto,
           hit.barang_hargajual,
           CASE
           WHEN (hit.barang_min IS NULL) THEN (0)::double precision
           ELSE hit.barang_min
           END AS barang_min,
           CASE
           WHEN (hit.barang_max IS NULL) THEN (0)::double precision
           ELSE hit.barang_max
           END AS barang_max,
           hit.barang_average,
           hit.qty_masuk,
           hit.qty_keluar,
           hit.qty_stok,
           hit.barang_namalainnya,
           hit.nilai_ro,
           hit.total_stok,
           (COALESCE((hit.jml_mutasi)::double precision, (0)::double precision) + COALESCE((hit.qtycssd_det)::double precision, (0)::double precision)) AS qty_dipesan,
           COALESCE((hit.jml_mutasi)::double precision, (0)::double precision) AS jml_mutasi,
           (COALESCE(hit.total_stok) - (COALESCE((hit.jml_mutasi)::double precision, (0)::double precision) + COALESCE((hit.qtycssd_det)::double precision, (0)::double precision))) AS qty_tersedia,
           hit.reference_mutasi
           FROM ( SELECT instalasi_m.instalasi_id,
           instalasi_m.instalasi_nama,
           stokbarang_r.ruangan_id,
           ruangan_m.ruangan_nama,
           stokbarang_r.barang_id,
           barang_m.barang_nama,
           barang_m.barang_kode,
           barang_m.satuankecil_id,
           satuan_besar.satuanunit_nama AS satuanbesar_nama,
           barang_m.satuan2_id,
           satuan_sedang.satuanunit_nama AS satuansedang_nama,
           satuan_kecil.satuanunit_nama AS satuankecil_nama,
           barang_m.satuan1_id,
           barang_m.kelompokbarang_id,
           kelompokbarang_m.kelompokbarang_nama,
           barang_m.subkelompokbarang_id,
           subkelompokbarang_m.subkelompok_nama,
           barang_m.barang_harganetto,
           barang_m.barang_hargajual,
           barang_m.barang_min,
           barang_m.barang_max,
           barang_m.barang_average,
           stokbarang_r.qty_masuk,
           stokbarang_r.qty_keluar,
           stokbarang_r.qty_dipesan,
           stokbarang_r.qty_tersedia,
           stokbarang_r.qty_sisa AS qty_stok,
           barang_m.barang_namalainnya,
           barang_m.nilai_ro,
           kartustok.total AS total_stok,
           mutasi.jml AS jml_mutasi,
           mutasi.reference AS reference_mutasi,
           cssd_detail.qty AS qtycssd_det
           FROM (((((((((((stokbarang_r
           JOIN ( SELECT a.ruangan_id,
           a.instalasi_id,
           a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON ((stokbarang_r.ruangan_id = ruangan_m.ruangan_id)))
           JOIN ( SELECT a.instalasi_id,
           a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
           JOIN ( SELECT a.barang_nama,
           a.barang_kode,
           a.satuankecil_id,
           a.satuan2_id,
           a.satuan1_id,
           a.subkelompokbarang_id,
           a.barang_harganetto,
           a.barang_hargajual,
           a.barang_min,
           a.barang_max,
           a.barang_average,
           a.barang_namalainnya,
           a.nilai_ro,
           a.barang_id,
           a.kelompokbarang_id,
           a.is_deleted,
           a.is_active
           FROM barang_m a) barang_m ON ((stokbarang_r.barang_id = barang_m.barang_id)))
           LEFT JOIN ( SELECT a.satuanunit_nama,
           a.satuanunit_id
           FROM satuanunit_m a) satuan_kecil ON ((barang_m.satuankecil_id = satuan_kecil.satuanunit_id)))
           LEFT JOIN ( SELECT a.satuanunit_nama,
           a.satuanunit_id
           FROM satuanunit_m a) satuan_besar ON ((barang_m.satuan1_id = satuan_besar.satuanunit_id)))
           LEFT JOIN ( SELECT a.satuanunit_nama,
           a.satuanunit_id
           FROM satuanunit_m a) satuan_sedang ON ((barang_m.satuan2_id = satuan_sedang.satuanunit_id)))
           LEFT JOIN ( SELECT a.kelompokbarang_nama,
           a.kelompokbarang_id
           FROM kelompokbarang_m a) kelompokbarang_m ON ((barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id)))
           LEFT JOIN ( SELECT a.subkelompok_nama,
           a.subkelompokbarang_id
           FROM subkelompokbarang_m a) subkelompokbarang_m ON ((barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id)))
           LEFT JOIN ( SELECT st.ruangan_id,
           st.barang_id,
           sum((st.qtystok_in - st.qtystok_out)) AS total
           FROM stokbarang_t st
           GROUP BY st.ruangan_id, st.barang_id) kartustok ON (((kartustok.ruangan_id = stokbarang_r.ruangan_id) AND (kartustok.barang_id = stokbarang_r.barang_id))))
           LEFT JOIN ( SELECT mt2.ruanganasal_id AS ruangan_id,
           mt.barang_id,
           sum(mt.jumlah_input) AS jml,
           string_agg((mt2.nomutasi_barang)::text, ','::text) AS reference
           FROM (mutasibarangdetail_t mt
           LEFT JOIN ( SELECT a.ruanganasal_id,
           a.nomutasi_barang,
           a.mutasibarang_id,
           a.is_deleted,
           a.status_mutasi
           FROM mutasibarang_t a) mt2 ON ((mt2.mutasibarang_id = mt.mutasibarang_id)))
           WHERE ((mt.is_deleted IS FALSE) AND (mt2.is_deleted IS FALSE) AND (mt2.status_mutasi = 401))
           GROUP BY mt2.ruanganasal_id, mt.barang_id) mutasi ON (((mutasi.ruangan_id = stokbarang_r.ruangan_id) AND (mutasi.barang_id = stokbarang_r.barang_id))))
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
           WHERE ((barang_m.is_deleted = false) AND (barang_m.is_active = true))) hit
        ");
        
        $this->execute('ALTER TABLE public.ketersediaanbarang_v OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_122327_migrate_cssd_ketersediaanbarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_122327_migrate_cssd_ketersediaanbarang_v cannot be reverted.\n";

        return false;
    }
    */
}
