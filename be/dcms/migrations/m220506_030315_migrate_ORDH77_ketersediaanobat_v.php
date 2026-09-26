<?php

use yii\db\Migration;

/**
 * Class m220506_030315_migrate_ORDH77_ketersediaanobat_v
 */
class m220506_030315_migrate_ORDH77_ketersediaanobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."ketersediaanobat_v";');
        $this->execute("CREATE VIEW \"public\".\"ketersediaanobat_v\" AS  SELECT hit.instalasi_id,
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
                WHEN hit.min_stok IS NULL THEN 0::double precision
                ELSE hit.min_stok
            END AS min_stok,
            CASE
                WHEN hit.max_stok IS NULL THEN 0::double precision
                ELSE hit.max_stok
            END AS max_stok,
        hit.total_stok AS qty_stok,
        COALESCE(hit.jml_mutasi, 0::double precision) AS jml_mutasi,
        COALESCE(hit.jml_resep_farmasi, 0::double precision) AS jml_resep_farmasi,
        COALESCE(hit.jml_resep_dokter, 0::double precision) AS jml_resep_dokter,
        COALESCE(hit.jml_mutasi, 0::double precision) + COALESCE(hit.jml_resep_farmasi, 0::double precision) + COALESCE(hit.jml_resep_dokter, 0::double precision) AS qty_dipesan,
        hit.total_stok - (COALESCE(hit.jml_mutasi, 0::double precision) + COALESCE(hit.jml_resep_farmasi, 0::double precision) + COALESCE(hit.jml_resep_dokter, 0::double precision)) AS qty_tersedia,
        hit.reference_mutasi,
        hit.reference_resep_farmasi,
        hit.reference_resep_dokter,
        hit.hargaygdigunakan,
        hit.hargamaksimum,
        hit.hargaminimum,
        hit.hargaratarata,
        hit.hargaterakhir,
        hit.harganetto,
        hit.ven_id,
        hit.ven_name
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
                obatalkes_m.hargaterakhir,
                stokobatalkes_r.qty_masuk,
                stokobatalkes_r.qty_keluar,
                stokobatalkes_r.qty_dipesan,
                stokobatalkes_r.qty_tersedia,
                stokobatalkes_r.qty_sisa AS qty_stok,
                obatalkes_m.obatalkes_nama,
                obatalkes_m.nilai_ro,
                konfigrak_m.min_stok,
                konfigrak_m.max_stok,
                kartustok.total AS total_stok,
                mutasi.jml AS jml_mutasi,
                resepfarmasi.jml AS jml_resep_farmasi,
                resepdokter.jml AS jml_resep_dokter,
                mutasi.reference AS reference_mutasi,
                resepfarmasi.reference AS reference_resep_farmasi,
                resepdokter.reference AS reference_resep_dokter,
                obatalkes_m.ven AS ven_id,
                look_ven.lookup_name AS ven_name
               FROM stokobatalkes_r
                 JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama,
                        a.instalasi_id
                       FROM ruangan_m a) ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
                 JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama
                       FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 JOIN ( SELECT a.obatalkes_id,
                        a.obatalkes_namalain,
                        a.obatalkes_kode,
                        a.hargajual,
                        a.satuankecil_id,
                        a.satuansedang_id,
                        a.satuanbesar_id,
                        a.jenisobatalkes_id,
                        a.harganetto,
                        a.hargamaksimum,
                        a.hargaminimum,
                        a.hargaratarata,
                        a.hargaterakhir,
                        a.obatalkes_nama,
                        a.nilai_ro,
                        a.ven,
                        a.is_active,
                        a.is_deleted
                       FROM obatalkes_m a) obatalkes_m ON stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id
                 LEFT JOIN ( SELECT a.satuanunit_id,
                        a.satuanunit_nama
                       FROM satuanunit_m a) satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
                 LEFT JOIN ( SELECT a.satuanunit_id,
                        a.satuanunit_nama
                       FROM satuanunit_m a) satuan_besar ON obatalkes_m.satuanbesar_id = satuan_besar.satuanunit_id
                 LEFT JOIN ( SELECT a.satuanunit_id,
                        a.satuanunit_nama
                       FROM satuanunit_m a) satuan_sedang ON obatalkes_m.satuansedang_id = satuan_sedang.satuanunit_id
                 JOIN ( SELECT a.jenisobatalkes_id,
                        a.jenisobatalkes_nama,
                        a.group_jenisobat
                       FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                 LEFT JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
                 LEFT JOIN ( SELECT a.stokobatr_id,
                        a.obatalkes_id,
                        a.min_stok,
                        a.max_stok,
                        a.is_deleted
                       FROM konfigrak_m a) konfigrak_m ON stokobatalkes_r.stokobatr_id = konfigrak_m.stokobatr_id AND stokobatalkes_r.obatalkes_id = konfigrak_m.obatalkes_id AND konfigrak_m.is_deleted = false
                 LEFT JOIN ( SELECT st.ruangan_id,
                        st.obatalkes_id,
                        sum(st.qtystok_in - st.qtystok_out) AS total
                       FROM stokobatalkes_t st
                      GROUP BY st.ruangan_id, st.obatalkes_id) kartustok ON kartustok.ruangan_id = stokobatalkes_r.ruangan_id AND kartustok.obatalkes_id = stokobatalkes_r.obatalkes_id
                 LEFT JOIN ( SELECT mt2.ruanganasal_id AS ruangan_id,
                        mt.obatalkes_id,
                        sum(mt.jumlah_mutasi) AS jml,
                        string_agg(mt2.nomutasioa::text, ','::text) AS reference
                       FROM mutasiobatdetail_t mt
                         LEFT JOIN mutasiobatruangan_t mt2 ON mt2.mutasiobatruangan_id = mt.mutasiobatruangan_id
                      WHERE mt.is_deleted IS FALSE AND mt2.is_deleted IS FALSE AND mt2.status_mutasi = 401
                      GROUP BY mt2.ruanganasal_id, mt.obatalkes_id) mutasi ON mutasi.ruangan_id = stokobatalkes_r.ruangan_id AND mutasi.obatalkes_id = stokobatalkes_r.obatalkes_id
                 LEFT JOIN ( SELECT pt.ruangan_id,
                        ot.obatalkes_id,
                        sum(COALESCE(ot.det_konversi, ot.qty_konversi)) AS jml,
                        string_agg(pt.noresep::text, ','::text) AS reference
                       FROM obatalkespasien_t ot
                         LEFT JOIN obatalkes_m om ON om.obatalkes_id = ot.obatalkes_id
                         LEFT JOIN penjualanresep_t pt ON pt.penjualanresep_id = ot.penjualanresep_id
                      WHERE ot.is_deleted IS FALSE AND pt.is_deleted IS FALSE AND pt.status_reseptur <> 660 AND pt.status_reseptur <> 432 AND pt.reseptur_id IS NULL
                      GROUP BY pt.ruangan_id, ot.obatalkes_id) resepfarmasi ON resepfarmasi.ruangan_id = stokobatalkes_r.ruangan_id AND resepfarmasi.obatalkes_id = stokobatalkes_r.obatalkes_id
                 LEFT JOIN ( SELECT rt.ruangan_id,
                        dt.obatalkes_id,
                        sum(COALESCE(dt.det_konversi, dt.qty_konversi)) AS jml,
                        string_agg(rt.noresep::text, ','::text) AS reference
                       FROM reseptur_t rt
                         JOIN resepturdetail_t dt ON dt.reseptur_id = rt.reseptur_id
                      WHERE rt.status_reseptur <> 660 AND rt.status_reseptur <> 432 AND rt.is_deleted IS FALSE AND dt.is_deleted IS FALSE
                      GROUP BY rt.ruangan_id, dt.obatalkes_id) resepdokter ON resepdokter.ruangan_id = stokobatalkes_r.ruangan_id AND resepdokter.obatalkes_id = stokobatalkes_r.obatalkes_id
                 LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                       FROM lookup_m a) look_ven ON obatalkes_m.ven = look_ven.lookup_id
              WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false) hit;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220506_030315_migrate_ORDH77_ketersediaanobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220506_030315_migrate_ORDH77_ketersediaanobat_v cannot be reverted.\n";

        return false;
    }
    */
}
