<?php

use yii\db\Migration;

/**
 * Class m211028_033949_migrate_US1714_RM_laporansensusharianri
 */
class m211028_033949_migrate_US1714_RM_laporansensusharianri extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporansensusharianri_sedangrawatinap_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporansensusharianri_sedangrawatinap_v\" AS
            SELECT (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date AS tgl_admisi,
            pasienadmisi_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            kamar_asal.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            masukkamar_t.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            kamar_asal.kamarruangan_nokamar AS kamar,
            tempattidur_asal.no_tempattidur AS tempattidur,
            penjamin_m.penjamin_nama,
            pegawai_m.nama_pegawai AS nama_dokter,
            asesmenmedis_t.diagnosa_id AS diagnosa_nama,
            pasien_m.no_mobile_pasien,
            pasien_m.no_telepon_pasien,
            pasien_m.jeniskelamin AS jeniskelamin_id,
            look_jenkel.lookup_name AS jeniskelamin_nama
            FROM ((((((((((((pendaftaran_t
            JOIN ( SELECT a.pasienadmisi_id,
            a.tgl_admisi,
            a.pasien_id,
            a.kamartempattidur_id,
            a.penjamin_id,
            a.pegawai_id,
            a.status_ranap
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN ( SELECT a.pasienadmisi_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kelaspelayanan_id,
            a.tgl_masukkamar
            FROM masukkamar_t a) masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.no_telepon_pasien,
            a.no_mobile_pasien,
            a.jeniskelamin
            FROM pasien_m a) pasien_m ON ((pasienadmisi_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
            FROM ruangan_m a) ruangan_m ON ((masukkamar_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kelaspelayanan_id,
            a.kamarruangan_nokamar
            FROM kamarruangan_m a) kamar_asal ON ((masukkamar_t.kamarruangan_id = kamar_asal.kamarruangan_id)))
            LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
            FROM kamartempattidur_m a) tempattidur_asal ON ((pasienadmisi_t.kamartempattidur_id = tempattidur_asal.kamartempattidur_id)))
            JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
            FROM kelaspelayanan_m a) kelaspelayanan_m ON ((masukkamar_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.diagnosa_id
            FROM asesmenmedis_t a) asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (pendaftaran_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id))))
            LEFT JOIN lookup_m look_jenkel ON (((pasien_m.jeniskelamin)::integer = look_jenkel.lookup_id)))
            WHERE ((pendaftaran_t.is_deleted IS FALSE) AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL) AND (pasienadmisi_t.status_ranap = 441))
            ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
            $this->execute('
                ALTER TABLE public.laporansensusharianri_sedangrawatinap_v OWNER TO postgres;
            ');

            $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienkeluarpindahrslain_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporansensusharianri_pasienkeluarpindahrslain_v\" AS
            SELECT pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_admisi,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            kamar_keluar.kamarruangan_nokamar AS kamar,
            tempattidur_keluar.no_tempattidur AS tempattidur,
            asesmenmedis_t.diagnosa_id AS diagnosa_nama,
            penjamin_m.penjamin_nama,
            (to_char(masukkamar.tgl_masukkamar, 'YYYY-MM-DD'::text))::date AS tgl_masukkamar,
            masukkamar.lamadirawat_kamar AS lama_rawat,
            pegawai_m.nama_pegawai AS nama_dokter,
            rujukanpulang_t.rujukan_dituju AS rumahsakit_rujukan,
            pasienpulang_t.tglpasienpulang AS tgl_pasienplg,
            masukkamar.tgl_masukkamar AS tgl_masukkamar_1,
            masukkamar.jam_masukkamar,
            masukkamar.tgl_keluarkamar,
            masukkamar.jam_keluarkamar
            FROM (((((((((((((((((pendaftaran_t
            JOIN ( SELECT a.pasienadmisi_id,
            a.tgl_admisi,
            a.ruangan_id,
            a.pasienpulang_id,
            a.pegawai_id,
            a.penjamin_id
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
            FROM kelaspelayanan_m a) kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ( SELECT a.pasienpulang_id,
            a.carakeluar_id,
            a.kondisikeluar_id,
            a.pasiendirujukkeluar_id,
            a.tglpasienpulang
            FROM pasienpulang_t a) pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN ( SELECT a.carakeluar_id
            FROM carakeluar_m a) carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            LEFT JOIN ( SELECT a.kondisikeluar_id
            FROM kondisikeluar_m a) kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
            JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.diagnosa_id
            FROM asesmenmedis_t a) asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (pendaftaran_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id))))
            JOIN ( SELECT DISTINCT ON (a.pasienadmisi_id) a.pasienadmisi_id,
            a.masukkamar_id,
            a.tgl_masukkamar,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            a.lamadirawat_kamar,
            a.jam_masukkamar,
            a.tgl_keluarkamar,
            a.jam_keluarkamar
            FROM masukkamar_t a) masukkamar ON ((pasienadmisi_t.pasienadmisi_id = masukkamar.pasienadmisi_id)))
            JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
            FROM kamarruangan_m a) kamar_keluar ON ((masukkamar.kamarruangan_id = kamar_keluar.kamarruangan_id)))
            JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
            FROM kamartempattidur_m a) tempattidur_keluar ON ((masukkamar.kamartempattidur_id = tempattidur_keluar.kamartempattidur_id)))
            LEFT JOIN ( SELECT a.pasiendirujukkeluar_id,
            a.rujukankeluar_id
            FROM pasiendirujukkeluar_t a) pasiendirujukkeluar_t ON ((pasienpulang_t.pasiendirujukkeluar_id = pasiendirujukkeluar_t.pasiendirujukkeluar_id)))
            LEFT JOIN ( SELECT a.rujukankeluar_id,
            a.rumahsakit_rujukan
            FROM rujukankeluar_m a) rujukankeluar_m ON ((pasiendirujukkeluar_t.rujukankeluar_id = rujukankeluar_m.rujukankeluar_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.rujukan_dituju
            FROM rujukanpulang_t a) rujukanpulang_t ON ((pendaftaran_t.pendaftaran_id = rujukanpulang_t.pendaftaran_id)))
            WHERE ((pendaftaran_t.is_deleted IS FALSE) AND (pasienpulang_t.carakeluar_id = 2) AND (pasienpulang_t.kondisikeluar_id = 3))
            ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
            $this->execute('
                ALTER TABLE public.laporansensusharianri_pasienkeluarpindahrslain_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211028_033949_migrate_US1714_RM_laporansensusharianri cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211028_033949_migrate_US1714_RM_laporansensusharianri cannot be reverted.\n";

        return false;
    }
    */
}
