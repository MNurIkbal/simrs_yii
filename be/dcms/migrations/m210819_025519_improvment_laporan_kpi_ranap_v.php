<?php

use yii\db\Migration;

/**
 * Class m210819_025519_improvment_laporan_kpi_ranap_v
 */
class m210819_025519_improvment_laporan_kpi_ranap_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporankpiranap_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporankpiranap_v" AS  SELECT \'IPD\'::text AS patient_type,
                pendaftaran.no_pendaftaran, 
                pasienadmisi_t.tgl_admisi,
                pasien.no_rekam_medik,
                concat(pasien.nama_depan, \'\', pasien.nama_pasien) AS nama_pasien,
                pasien.tanggal_lahir,
                concat(COALESCE(kamarruangan_m.kamarruangan_nokamar, \'\'::character varying), \'-\', COALESCE(kamartempattidur_m.no_tempattidur, \'\'::character varying)) AS kamar_terakhir,
                jenis_kamar.lookup_name AS jenis_kamar,
                ruangan_m.ruangan_nama AS ruangan_terakhir,
                dokter_dpjp.nama_pegawai AS nama_dpjp,
                pasienpulang.tglpasienpulang,
                pasienpulang.kondisi_pulang,
                pasienpulang.cara_keluar,
                penjamin_m.penjamin_nama,
                masukkamar.tgl_masukkamar,
                rencanapulang.tgl_rencanapulang,
                \'\'::text AS nomor_tagihan,
                pendaftaran.tgl_stopakomodasi AS tgl_tagihan,
                pasienpulang.tglpasienpulang AS tgl_bayar,
                pasienpulang.tglpasienpulang AS tgl_dibersihkan,
                round((date_part(\'EPOCH\'::text, (pasienpulang.tglpasienpulang - masukkamar.tgl_masukkamar)) / (3600)::double precision)) AS jam_ranap,
                    CASE
                        WHEN (round((date_part(\'EPOCH\'::text, (masukkamar.tgl_masukkamar - pendaftaran.tgl_pendaftaran)) / (3600)::double precision)) < (0)::double precision) THEN (0)::double precision
                        ELSE round((date_part(\'EPOCH\'::text, (masukkamar.tgl_masukkamar - pendaftaran.tgl_pendaftaran)) / (3600)::double precision))
                    END AS jam_tunggu
               FROM (((((((((((pasienadmisi_t
                 JOIN ( SELECT pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.pasienadmisi_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.pegawai_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.tgl_stopakomodasi
                       FROM pendaftaran_t) pendaftaran ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran.pasienadmisi_id)))
                 JOIN ( SELECT pasien_m.pasien_id,
                        pasien_m.nama_pasien,
                        pasien_m.no_rekam_medik,
                        l_nama_depan.lookup_name AS nama_depan,
                        pasien_m.tanggal_lahir
                       FROM (pasien_m
                         LEFT JOIN lookup_m l_nama_depan ON (((pasien_m.namadepan)::integer = l_nama_depan.lookup_id)))) pasien ON ((pendaftaran.pasien_id = pasien.pasien_id)))
                 JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
                 JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
                 JOIN lookup_m jenis_kamar ON ((kamarruangan_m.kamarruangan_jenis = jenis_kamar.lookup_id)))
                 JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN ( SELECT pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai
                       FROM pegawai_m) dokter_dpjp ON ((COALESCE(pasienadmisi_t.pegawai_id, pendaftaran.pegawai_id) = dokter_dpjp.pegawai_id)))
                 JOIN ( SELECT pasienpulang_t.pasienpulang_id,
                        pasienpulang_t.tglpasienpulang,
                        kondisikeluar_m.kondisikeluar_nama AS kondisi_pulang,
                        carakeluar_m.carakeluar_nama AS cara_keluar
                       FROM ((pasienpulang_t
                         JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
                         LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))) pasienpulang ON ((pasienadmisi_t.pasienpulang_id = pasienpulang.pasienpulang_id)))
                 JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN ( SELECT min(masukkamar_t.tgl_masukkamar) AS tgl_masukkamar,
                        masukkamar_t.pasienadmisi_id
                       FROM masukkamar_t
                      GROUP BY masukkamar_t.pasienadmisi_id) masukkamar ON ((pasienadmisi_t.pasienadmisi_id = masukkamar.pasienadmisi_id)))
                 LEFT JOIN ( SELECT max(rencanapulang_t.rencana_pulang) AS tgl_rencanapulang,
                        rencanapulang_t.pasienadmisi_id
                       FROM rencanapulang_t
                      WHERE (rencanapulang_t.is_deleted IS FALSE)
                      GROUP BY rencanapulang_t.pasienadmisi_id) rencanapulang ON ((pasienadmisi_t.pasienadmisi_id = rencanapulang.pasienadmisi_id)))
              ORDER BY pasienadmisi_t.pasienadmisi_id DESC;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210819_025519_improvment_laporan_kpi_ranap_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210819_025519_improvment_laporan_kpi_ranap_v cannot be reverted.\n";

        return false;
    }
    */
}
