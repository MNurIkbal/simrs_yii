<?php

use yii\db\Migration;

/**
 * Class m220513_114441_migrate_VCS168_rl5_4_10besarpenyakitrj_v
 */
class m220513_114441_migrate_VCS168_rl5_4_10besarpenyakitrj_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."rl5_4_10besarpenyakitrj_v";');
        $this->execute("CREATE VIEW \"public\".\"rl5_4_10besarpenyakitrj_v\" AS  SELECT diagnosa_id,
        diagnosa_kode,
        diagnosa_nama,
        bulan,
        tahun,
        COALESCE(e.jml_laki, 0::bigint) AS jml_laki,
        COALESCE(c.jml_perempuan, 0::bigint) AS jml_perempuan,
        COALESCE(e.jml_laki, 0::bigint) + COALESCE(c.jml_perempuan, 0::bigint) AS jumlah
       FROM ( SELECT x.diagnosa_id,
                x.diagnosa_kode,
                x.diagnosa_nama,
                x.bulan,
                x.tahun,
                count(*) AS jml_laki
               FROM ( SELECT pendaftaran_t.pendaftaran_id,
                        koreksidiagnosa_t.diagnosa_id,
                        diagnosa_m.diagnosa_kode,
                        diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
                        koreksidiagnosa_t.pasien_id,
                        pasien_m.nama_pasien,
                        pasien_m.jeniskelamin AS jenis_kelamin_id,
                        look_jeniskelamin.lookup_name AS jenis_kelamin,
                        koreksidiagnosa_t.tgl_koreksidiagnosa,
                        to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text)::character varying AS bulan,
                        to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text)::character varying AS tahun,
                            CASE koreksidiagnosa_t.is_diagnosa_baru
                                WHEN true THEN 'Baru'::text
                                ELSE 'Lama'::text
                            END::character varying AS nama_kasus_diagnosa,
                        COALESCE(koreksidiagnosa_t.is_diagnosa_baru, true) AS is_diagnosa_baru
                       FROM koreksidiagnosa_t
                         JOIN ( SELECT a.pendaftaran_id,
                                a.tgl_pendaftaran,
                                a.instalasi_id
                               FROM pendaftaran_t a) pendaftaran_t ON koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                         JOIN ( SELECT pasien_m_1.pasien_id,
                                pasien_m_1.nama_pasien,
                                pasien_m_1.jeniskelamin
                               FROM pasien_m pasien_m_1) pasien_m ON koreksidiagnosa_t.pasien_id = pasien_m.pasien_id
                         JOIN ( SELECT diagnosa_m_1.diagnosa_id,
                                diagnosa_m_1.diagnosa_kode,
                                diagnosa_m_1.diagnosa_nama,
                                diagnosa_m_1.diagnosa_namalainnya
                               FROM diagnosa_m diagnosa_m_1) diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
                         JOIN ( SELECT a.lookup_id,
                                a.lookup_name
                               FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
                      WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2 AND koreksidiagnosa_t.pasienadmisi_id IS NULL AND koreksidiagnosa_t.is_deleted IS FALSE AND pendaftaran_t.instalasi_id = 1 AND koreksidiagnosa_t.is_diagnosa_baru IS TRUE) x
              WHERE x.jenis_kelamin_id::integer = 15
              GROUP BY x.diagnosa_id, x.diagnosa_kode, x.diagnosa_nama, x.tahun, x.bulan) e
         FULL JOIN ( SELECT x.diagnosa_id,
                x.diagnosa_kode,
                x.diagnosa_nama,
                x.bulan,
                x.tahun,
                count(*) AS jml_perempuan
               FROM ( SELECT pendaftaran_t.pendaftaran_id,
                        koreksidiagnosa_t.diagnosa_id,
                        diagnosa_m.diagnosa_kode,
                        diagnosa_m.diagnosa_namalainnya AS diagnosa_nama,
                        koreksidiagnosa_t.pasien_id,
                        pasien_m.nama_pasien,
                        pasien_m.jeniskelamin AS jenis_kelamin_id,
                        look_jeniskelamin.lookup_name AS jenis_kelamin,
                        koreksidiagnosa_t.tgl_koreksidiagnosa,
                        to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text)::character varying AS bulan,
                        to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text)::character varying AS tahun,
                            CASE koreksidiagnosa_t.is_diagnosa_baru
                                WHEN true THEN 'Baru'::text
                                ELSE 'Lama'::text
                            END::character varying AS nama_kasus_diagnosa,
                        COALESCE(koreksidiagnosa_t.is_diagnosa_baru, true) AS is_diagnosa_baru
                       FROM koreksidiagnosa_t
                         JOIN ( SELECT a.pendaftaran_id,
                                a.tgl_pendaftaran,
                                a.instalasi_id
                               FROM pendaftaran_t a) pendaftaran_t ON koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                         JOIN ( SELECT pasien_m_1.pasien_id,
                                pasien_m_1.nama_pasien,
                                pasien_m_1.jeniskelamin
                               FROM pasien_m pasien_m_1) pasien_m ON koreksidiagnosa_t.pasien_id = pasien_m.pasien_id
                         JOIN ( SELECT diagnosa_m_1.diagnosa_id,
                                diagnosa_m_1.diagnosa_kode,
                                diagnosa_m_1.diagnosa_nama,
                                diagnosa_m_1.diagnosa_namalainnya
                               FROM diagnosa_m diagnosa_m_1) diagnosa_m ON koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id
                         JOIN ( SELECT a.lookup_id,
                                a.lookup_name
                               FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
                      WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2 AND koreksidiagnosa_t.pasienadmisi_id IS NULL AND koreksidiagnosa_t.is_deleted IS FALSE AND pendaftaran_t.instalasi_id = 1 AND koreksidiagnosa_t.is_diagnosa_baru IS TRUE) x
              WHERE x.jenis_kelamin_id::integer = 16
              GROUP BY x.diagnosa_id, x.diagnosa_kode, x.diagnosa_nama, x.tahun, x.bulan) c USING (diagnosa_id, diagnosa_kode, diagnosa_nama, tahun, bulan)
      ORDER BY (COALESCE(e.jml_laki, 0::bigint) + COALESCE(c.jml_perempuan, 0::bigint)) DESC;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220513_114441_migrate_VCS168_rl5_4_10besarpenyakitrj_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220513_114441_migrate_VCS168_rl5_4_10besarpenyakitrj_v cannot be reverted.\n";

        return false;
    }
    */
}
