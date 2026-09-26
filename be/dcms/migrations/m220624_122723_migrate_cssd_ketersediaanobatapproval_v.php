<?php

use yii\db\Migration;

/**
 * Class m220624_122723_migrate_cssd_ketersediaanobatapproval_v
 */
class m220624_122723_migrate_cssd_ketersediaanobatapproval_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.ketersediaanobatapproval_v;');

        $this->execute("CREATE VIEW \"public\".\"ketersediaanobatapproval_v\" AS
           SELECT hit.instalasi_id,
           hit.instalasi_nama,
           hit.ruangan_id,
           hit.ruangan_nama,
           hit.obatalkes_id,
           hit.obatalkes_nama,
           hit.obatalkes_nama AS obatalkes_namalain,
           hit.obatalkes_kode,
           hit.qty_masuk,
           hit.qty_keluar,
           hit.nilai_ro,
           hit.satuanbesar_id,
           hit.satuanbesar_nama,
           hit.satuankecil_id,
           hit.satuankecil_nama,
           hit.satuansedang_id,
           hit.satuansedang_nama,
           hit.jenisobatalkes_id,
           hit.jenisobatalkes_nama,
           hit.group_jenisobat,
           CASE
           WHEN (hit.min_stok IS NULL) THEN (0)::double precision
           ELSE hit.min_stok
           END AS min_stok,
           CASE
           WHEN (hit.max_stok IS NULL) THEN (0)::double precision
           ELSE hit.max_stok
           END AS max_stok,
           hit.taal_stok AS qty_stok,
           COALESCE(hit.jml_mutasi, (0)::double precision) AS jml_mutasi,
           COALESCE(hit.jml_resep_farmasi, (0)::double precision) AS jml_resep_farmasi,
           COALESCE(hit.jml_resep_dokter, (0)::double precision) AS jml_resep_dokter,
           ((COALESCE(hit.jml_mutasi, (0)::double precision) + COALESCE(hit.jml_resep_farmasi, (0)::double precision)) + COALESCE(hit.jml_resep_dokter, (0)::double precision)) AS qty_dipesan,
           (hit.taal_stok - ((COALESCE(hit.jml_mutasi, (0)::double precision) + COALESCE(hit.jml_resep_farmasi, (0)::double precision)) + COALESCE(hit.jml_resep_dokter, (0)::double precision))) AS qty_tersedia,
           hit.reference_mutasi,
           hit.reference_resep_farmasi,
           hit.reference_resep_dokter
           FROM ( SELECT instalasi_m.instalasi_id,
           instalasi_m.instalasi_nama,
           stokobatalkes_r.ruangan_id,
           ruangan_m.ruangan_nama,
           stokobatalkes_r.obatalkes_id,
           obatalkes_m.obatalkes_namalain,
           obatalkes_m.obatalkes_kode,
           obatalkes_m.hargajual,
           obatalkes_m.satuankecil_id,
           satuan_besar.satuanunit_nama AS satuanbesar_nama,
           obatalkes_m.satuansedang_id,
           satuan_sedang.satuanunit_nama AS satuansedang_nama,
           satuan_kecil.satuanunit_nama AS satuankecil_nama,
           obatalkes_m.satuanbesar_id,
           obatalkes_m.jenisobatalkes_id,
           jenisobatalkes_m.jenisobatalkes_nama,
           jenisobatalkes_m.group_jenisobat,
           konfigfarmasi_k.persenppn AS ppn,
           konfigfarmasi_k.persenmargin AS margin,
           konfigfarmasi_k.persen_diskon AS disc,
           konfigfarmasi_k.hargaygdigunakan,
           obatalkes_m.harganetto,
           obatalkes_m.hargamaksimum,
           obatalkes_m.hargaminimum,
           obatalkes_m.hargaratarata,
           stokobatalkes_r.qty_masuk,
           stokobatalkes_r.qty_keluar,
           stokobatalkes_r.qty_dipesan,
           stokobatalkes_r.qty_tersedia,
           stokobatalkes_r.qty_sisa AS qty_stok,
           obatalkes_m.obatalkes_nama,
           obatalkes_m.nilai_ro,
           konfigrak_m.min_stok,
           konfigrak_m.max_stok,
           kaaustok.taal AS taal_stok,
           mutasi.jml AS jml_mutasi,
           resepfarmasi.jml AS jml_resep_farmasi,
           resepdokter.jml AS jml_resep_dokter,
           mutasi.reference AS reference_mutasi,
           resepfarmasi.reference AS reference_resep_farmasi,
           resepdokter.reference AS reference_resep_dokter
           FROM (((((((((((((stokobatalkes_r
           JOIN ( SELECT a.ruangan_id,
           a.ruangan_nama,
           a.instalasi_id
           FROM ruangan_m a) ruangan_m ON ((stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id)))
           JOIN ( SELECT a.instalasi_id,
           a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
           JOIN ( SELECT a.obatalkes_id,
           a.obatalkes_kode,
           a.obatalkes_nama,
           a.obatalkes_namalain,
           a.satuankecil_id,
           a.satuansedang_id,
           a.satuanbesar_id,
           a.jenisobatalkes_id,
           a.hargajual,
           a.harganetto,
           a.hargamaksimum,
           a.hargaminimum,
           a.hargaratarata,
           a.nilai_ro,
           a.hargaterakhir,
           a.is_active,
           a.is_deleted
           FROM obatalkes_m a) obatalkes_m ON ((stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id)))
           LEFT JOIN ( SELECT a.satuanunit_id,
           a.satuanunit_nama
           FROM satuanunit_m a) satuan_kecil ON ((obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id)))
           LEFT JOIN ( SELECT a.satuanunit_id,
           a.satuanunit_nama
           FROM satuanunit_m a) satuan_besar ON ((obatalkes_m.satuanbesar_id = satuan_besar.satuanunit_id)))
           LEFT JOIN ( SELECT a.satuanunit_id,
           a.satuanunit_nama
           FROM satuanunit_m a) satuan_sedang ON ((obatalkes_m.satuansedang_id = satuan_sedang.satuanunit_id)))
           JOIN ( SELECT a.jenisobatalkes_id,
           a.jenisobatalkes_nama,
           a.group_jenisobat
           FROM jenisobatalkes_m a) jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
           LEFT JOIN ( SELECT a.is_deleted,
           a.persenppn,
           a.persenmargin,
           a.persen_diskon,
           a.hargaygdigunakan
           FROM konfigfarmasi_k a) konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
           LEFT JOIN ( SELECT a.konfigrak_id,
           a.stokobatr_id,
           a.min_stok,
           a.max_stok
           FROM konfigrak_m a) konfigrak_m ON ((stokobatalkes_r.stokobatr_id = konfigrak_m.stokobatr_id)))
           LEFT JOIN ( SELECT a.ruangan_id,
           a.obatalkes_id,
           sum((a.qtystok_in - a.qtystok_out)) AS taal
           FROM stokobatalkes_t a
           GROUP BY a.ruangan_id, a.obatalkes_id) kaaustok ON (((kaaustok.ruangan_id = stokobatalkes_r.ruangan_id) AND (kaaustok.obatalkes_id = stokobatalkes_r.obatalkes_id))))
           LEFT JOIN ( SELECT mutasiobatruangan_t.ruanganasal_id AS ruangan_id,
           a.obatalkes_id,
           sum(a.jumlah_mutasi) AS jml,
           string_agg((mutasiobatruangan_t.nomutasioa)::text, ','::text) AS reference
           FROM (mutasiobatdetail_t a
           LEFT JOIN ( SELECT a1.mutasiobatruangan_id,
           a1.nomutasioa,
           a1.is_deleted,
           a1.status_mutasi,
           a1.ruanganasal_id
           FROM mutasiobatruangan_t a1) mutasiobatruangan_t ON ((mutasiobatruangan_t.mutasiobatruangan_id = a.mutasiobatruangan_id)))
           WHERE ((a.is_deleted IS FALSE) AND (mutasiobatruangan_t.is_deleted IS FALSE) AND (mutasiobatruangan_t.status_mutasi = 401))
           GROUP BY mutasiobatruangan_t.ruanganasal_id, a.obatalkes_id) mutasi ON (((mutasi.ruangan_id = stokobatalkes_r.ruangan_id) AND (mutasi.obatalkes_id = stokobatalkes_r.obatalkes_id))))
           LEFT JOIN ( SELECT penjualanresep_t.ruangan_id,
           a.obatalkes_id,
           sum(COALESCE(a.det_konversi, a.qty_konversi)) AS jml,
           string_agg((penjualanresep_t.noresep)::text, ','::text) AS reference
           FROM ((obatalkespasien_t a
           LEFT JOIN ( SELECT a1.obatalkes_id
           FROM obatalkes_m a1) obatalkes_m_1 ON ((obatalkes_m_1.obatalkes_id = a.obatalkes_id)))
           LEFT JOIN ( SELECT a1.penjualanresep_id,
           a1.is_deleted,
           a1.ruangan_id,
           a1.noresep,
           a1.status_reseptur,
           a1.reseptur_id
           FROM penjualanresep_t a1) penjualanresep_t ON ((penjualanresep_t.penjualanresep_id = a.penjualanresep_id)))
           WHERE ((a.is_deleted IS FALSE) AND (penjualanresep_t.is_deleted IS FALSE) AND (penjualanresep_t.status_reseptur = 346) AND (penjualanresep_t.reseptur_id IS NULL))
           GROUP BY penjualanresep_t.ruangan_id, a.obatalkes_id) resepfarmasi ON (((resepfarmasi.ruangan_id = stokobatalkes_r.ruangan_id) AND (resepfarmasi.obatalkes_id = stokobatalkes_r.obatalkes_id))))
           LEFT JOIN ( SELECT a.ruangan_id,
           resepturdetail_t.obatalkes_id,
           sum(COALESCE(resepturdetail_t.det_konversi, resepturdetail_t.qty_konversi)) AS jml,
           string_agg((a.noresep)::text, ','::text) AS reference
           FROM (reseptur_t a
           JOIN ( SELECT a1.reseptur_id,
           a1.det_konversi,
           a1.qty_konversi,
           a1.obatalkes_id,
           a1.is_deleted
           FROM resepturdetail_t a1) resepturdetail_t ON ((resepturdetail_t.reseptur_id = a.reseptur_id)))
           WHERE ((a.status_reseptur = 346) AND (a.is_deleted IS FALSE) AND (resepturdetail_t.is_deleted IS FALSE))
           GROUP BY a.ruangan_id, resepturdetail_t.obatalkes_id) resepdokter ON (((resepdokter.ruangan_id = stokobatalkes_r.ruangan_id) AND (resepdokter.obatalkes_id = stokobatalkes_r.obatalkes_id))))
           WHERE ((obatalkes_m.is_active = true) AND (obatalkes_m.is_deleted = false))) hit
        ");
        
        $this->execute('ALTER TABLE public.ketersediaanobatapproval_v OWNER TO postgres;');
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_122723_migrate_cssd_ketersediaanobatapproval_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_122723_migrate_cssd_ketersediaanobatapproval_v cannot be reverted.\n";

        return false;
    }
    */
}
