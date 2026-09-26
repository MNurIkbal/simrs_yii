<?php

use yii\db\Migration;

/**
 * Class m250522_072130_migrate_optimasi_view_kesimpulanrd_v
 */
class m250522_072130_migrate_optimasi_view_kesimpulanrd_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."kesimpulanrd_v";
        ');

        $this->execute('
            CREATE VIEW "public"."kesimpulanrd_v" AS  SELECT kesimpulanrd_t.kesimpulanrd_id,
    kesimpulanrd_t.pendaftaran_id,
    kesimpulanrd_t.pasienpulang_id,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    kondisikeluar_m.kondisikeluar_nama,
    pasienpulang_t.tglpasienpulang,
    pasienpulang_t.tgl_meninggal,
    kesimpulanrd_t.instruksi_lanjutan,
    kesimpulanrd_t.tgl_lanjut_rawat,
    kesimpulanrd_t.poliklinik_id,
    kesimpulanrd_t.dokter_id,
    kesimpulanrd_t.kondisi,
    kesimpulanrd_t.hr,
    kesimpulanrd_t.rr,
    kesimpulanrd_t.spo2,
    kesimpulanrd_t.t,
    kesimpulanrd_t.gcs_eye_id,
    eye.metodegcs_nilai AS nilai_eye,
    kesimpulanrd_t.gcs_verbal_id,
    verbal.metodegcs_nilai AS nilai_verbal,
    kesimpulanrd_t.gcs_motorik_id,
    motorik.metodegcs_nilai AS nilai_motorik,
    kesimpulanrd_t.hasil_gcs,
    kesimpulanrd_t.gcs_kategori,
    kesimpulanrd_t.is_kapitis,
    kesimpulanrd_t.reseptur_id,
    eye.metodegcs_nama AS gcs_eye_nama,
    verbal.metodegcs_nama AS gcs_verbal_nama,
    motorik.metodegcs_nama AS gcs_motorik_nama,
    dokter.nama_pegawai AS dokter_pulang,
    dokter_pulang.nama_pegawai AS dokter_dpjp_pulang,
    poliklinik.ruangan_nama AS poliklinik_nama,
    ( SELECT row_to_json(x.*) AS array_to_json
           FROM ( SELECT reseptur_t.pendaftaran_id,
                    reseptur_t.tglreseptur,
                    pegawai_m.nama_pegawai,
                    ruangan_m_1.ruangan_tujuan
                   FROM reseptur_t
                     JOIN ( SELECT a.pendaftaran_id,
                            max(a.reseptur_id) AS reseptur_id
                           FROM reseptur_t a
                          WHERE a.status_reseptur <> 432
                          GROUP BY a.pendaftaran_id) max_resep ON reseptur_t.reseptur_id = max_resep.reseptur_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) pegawai_m ON reseptur_t.pegawai_id = pegawai_m.pegawai_id
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama AS ruangan_tujuan
                           FROM ruangan_m a) ruangan_m_1 ON reseptur_t.ruangan_id = ruangan_m_1.ruangan_id
                  WHERE kesimpulanrd_t.pendaftaran_id = reseptur_t.pendaftaran_id AND reseptur_t.status_reseptur <> 432) x) AS info_resep,
    ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
           FROM ( SELECT penjualanresep_t.pendaftaran_id,
                    penjualanresep_t.iter
                   FROM penjualanresep_t
                  WHERE kesimpulanrd_t.pendaftaran_id = penjualanresep_t.penjualanresep_id
                  GROUP BY penjualanresep_t.pendaftaran_id, penjualanresep_t.iter) x) AS detail_resep,
    pasienpulang_t.tempattidurtujuan_id,
        CASE COALESCE(pasienpulang_t.tempattidurtujuan_id, 0)
            WHEN 0 THEN NULL::text
            ELSE concat(COALESCE(kamarruangan_m.kamarruangan_nokamar, \'\'::character varying), \' - \', COALESCE(kamartempattidur_m.no_tempattidur, \'\'::character varying))
        END AS tempattidurtujuan_nama,
    pasienpulang_t.catatan_lain,
    kamarruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    COALESCE(pasienpulang_t.kamarruangan_jenis, kamarruangan_m.kamarruangan_jenis) AS kamarruangan_jenis,
    jenis_kamar.lookup_name AS kamarruangan_jenis_nama,
    pasienpulang_t.catatan_tindakan,
    dokter_tujuan.nama_pegawai AS dokter_tujuan
   FROM kesimpulanrd_t
     JOIN ( SELECT a.carakeluar_id,
            a.tglpasienpulang,
            a.tgl_meninggal,
            a.tempattidurtujuan_id,
            a.catatan_lain,
            a.kamarruangan_jenis,
            a.pasienpulang_id,
            a.kondisikeluar_id,
            a.dpjp_id,
            a.pasienbatalpulang_id,
            a.catatan_tindakan,
            a.dokterspesialis_id
           FROM pasienpulang_t a) pasienpulang_t ON kesimpulanrd_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     JOIN ( SELECT a.carakeluar_id,
            a.carakeluar_nama
           FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     JOIN ( SELECT kondisikeluar_m_1.kondisikeluar_id,
            kondisikeluar_m_1.kondisikeluar_nama 
           FROM kondisikeluar_m kondisikeluar_m_1) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
     LEFT JOIN ( SELECT metodegcs_m.metodegcs_id,
            metodegcs_m.metodegcs_nama,
            metodegcs_m.metodegcs_nilai
           FROM metodegcs_m) eye ON kesimpulanrd_t.gcs_eye_id = eye.metodegcs_id
     LEFT JOIN ( SELECT metodegcs_m.metodegcs_id,
            metodegcs_m.metodegcs_nama,
            metodegcs_m.metodegcs_nilai
           FROM metodegcs_m) verbal ON kesimpulanrd_t.gcs_verbal_id = verbal.metodegcs_id
     LEFT JOIN ( SELECT metodegcs_m.metodegcs_id,
            metodegcs_m.metodegcs_nama,
            metodegcs_m.metodegcs_nilai
           FROM metodegcs_m) motorik ON kesimpulanrd_t.gcs_motorik_id = motorik.metodegcs_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) dokter ON kesimpulanrd_t.dokter_id = dokter.pegawai_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) poliklinik ON kesimpulanrd_t.poliklinik_id = poliklinik.ruangan_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) dokter_pulang ON pasienpulang_t.dpjp_id = dokter_pulang.pegawai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter_tujuan ON pasienpulang_t.dokterspesialis_id = dokter_tujuan.pegawai_id
     LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur,
            a.kamarruangan_id
           FROM kamartempattidur_m a) kamartempattidur_m ON pasienpulang_t.tempattidurtujuan_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar,
            a.kamarruangan_jenis,
            a.ruangan_id
           FROM kamarruangan_m a) kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) jenis_kamar ON COALESCE(pasienpulang_t.kamarruangan_jenis, kamarruangan_m.kamarruangan_jenis) = jenis_kamar.lookup_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
  WHERE pasienpulang_t.pasienbatalpulang_id IS NULL;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250522_072130_migrate_optimasi_view_kesimpulanrd_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250522_072130_migrate_optimasi_view_kesimpulanrd_v cannot be reverted.\n";

        return false;
    }
    */
}
