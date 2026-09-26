<?php

use yii\db\Migration;

/**
 * Class m210814_072849_migrate_US1004_view_laporansensusharianri_optimize
 */
class m210814_072849_migrate_US1004_view_laporansensusharianri_optimize extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienkeluar_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporansensusharianri_pasienkeluar_v\" AS
            SELECT pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_admisi,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasienpulang_t.ruanganakhir_id AS ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            kamar_keluar.kamarruangan_nokamar AS kamar,
            tempattidur_keluar.no_tempattidur AS tempattidur,
            asesmenmedis_t.diagnosa_id AS diagnosa_nama,
            penjamin_m.penjamin_nama,
            (to_char(masukkamar_t.tgl_masukkamar, 'YYYY-MM-DD'::text))::date AS tgl_masukkamar,
            masukkamar_t.lamadirawat_kamar AS lama_rawat,
            pegawai_m.nama_pegawai AS nama_dokter,
            masukkamar_t.tgl_masukkamar AS tgl_masukkamar_1,
            pasienpulang_t.tglpasienpulang AS tgl_pasienplg,
            masukkamar_t.jam_masukkamar,
            masukkamar_t.tgl_keluarkamar,
            masukkamar_t.jam_keluarkamar
            FROM ((((((((((((((pendaftaran_t
            JOIN ( SELECT a.pasienadmisi_id,
            a.tgl_admisi,
            a.kelaspelayanan_id,
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
            FROM kelaspelayanan_m a) kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ( SELECT a.pasienpulang_id,
            a.ruanganakhir_id,
            a.carakeluar_id,
            a.kondisikeluar_id,
            a.pasienadmisi_id,
            a.tglpasienpulang
            FROM pasienpulang_t a) pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
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
            JOIN ( SELECT a.pasienadmisi_id,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            a.tgl_masukkamar,
            a.lamadirawat_kamar,
            a.jam_masukkamar,
            a.tgl_keluarkamar,
            a.jam_keluarkamar
            FROM masukkamar_t a) masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
            JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
            FROM kamarruangan_m a) kamar_keluar ON ((masukkamar_t.kamarruangan_id = kamar_keluar.kamarruangan_id)))
            JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
            FROM kamartempattidur_m a) tempattidur_keluar ON ((masukkamar_t.kamartempattidur_id = tempattidur_keluar.kamartempattidur_id)))
            WHERE ((pendaftaran_t.is_deleted IS FALSE) AND (pasienpulang_t.pasienadmisi_id IS NOT NULL) AND (masukkamar_t.jam_keluarkamar IS NULL) AND (pasienpulang_t.carakeluar_id = ANY (ARRAY[1, 3, 5, 6, 7])))
            ORDER BY pasienpulang_t.tglpasienpulang DESC
            ;");
        $this->execute('
            ALTER TABLE public.laporansensusharianri_pasienkeluar_v OWNER TO postgres;
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
            rujukankeluar_m.rumahsakit_rujukan,
            pasienpulang_t.tglpasienpulang AS tgl_pasienplg,
            masukkamar.tgl_masukkamar AS tgl_masukkamar_1,
            masukkamar.jam_masukkamar,
            masukkamar.tgl_keluarkamar,
            masukkamar.jam_keluarkamar
            FROM ((((((((((((((((pendaftaran_t
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
            WHERE ((pendaftaran_t.is_deleted IS FALSE) AND (pasienpulang_t.carakeluar_id = 2) AND (pasienpulang_t.kondisikeluar_id = 3))
            ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
        $this->execute('
            ALTER TABLE public.laporansensusharianri_pasienkeluarpindahrslain_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienmasuk_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporansensusharianri_pasienmasuk_v\" AS
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
            asesmenmedis_t.diagnosa_id AS diagnosa_nama
            FROM (((((((((((pendaftaran_t
            JOIN ( SELECT a.pasienadmisi_id,
            a.tgl_admisi,
            a.pasien_id,
            a.kamartempattidur_id,
            a.penjamin_id,
            a.pegawai_id
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN ( SELECT a.pasienadmisi_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kelaspelayanan_id,
            a.tgl_masukkamar
            FROM masukkamar_t a) masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik
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
            WHERE ((pendaftaran_t.is_deleted IS FALSE) AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
            ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
        $this->execute('
            ALTER TABLE public.laporansensusharianri_pasienmasuk_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienmeninggal_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporansensusharianri_pasienmeninggal_v\" AS
            SELECT pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_admisi,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            kelaspelayanan_m.kelaspelayanan_id,
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
            CASE
            WHEN (pasienpulang_t.lama_rawat <= 2) THEN (pasienpulang_t.lama_rawat)::integer
            ELSE 0
            END AS lama_rawat_kur48,
            CASE
            WHEN (pasienpulang_t.lama_rawat > 2) THEN (pasienpulang_t.lama_rawat)::integer
            ELSE 0
            END AS lama_rawat_leb48,
            pegawai_m.nama_pegawai AS nama_dokter,
            pasienpulang_t.tglpasienpulang AS tgl_pasienplg,
            pasienpulang_t.tgl_meninggal AS tgl_pasienmeninggal,
            pasienpulang_t.lama_rawat,
            kondisikeluar_m.kondisikeluar_id AS kondisi_keluar_id,
            kondisikeluar_m.kondisikeluar_nama AS kondisi_keluar_nama
            FROM ((((((((((((((pendaftaran_t
            LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pasien_id,
            a.kelaspelayanan_id,
            a.ruangan_id,
            a.pegawai_id,
            a.penjamin_id,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            a.tgl_admisi
            FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik
            FROM pasien_m a) pasien_m ON ((pasienadmisi_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT DISTINCT ON (a.pasienadmisi_id) a.pasienadmisi_id,
            a.masukkamar_id,
            a.tgl_masukkamar
            FROM masukkamar_t a) masukkamar ON ((pasienadmisi_t.pasienadmisi_id = masukkamar.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
            FROM kelaspelayanan_m a) kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.carakeluar_id,
            a.kondisikeluar_id,
            a.lama_rawat,
            a.tglpasienpulang,
            a.tgl_meninggal
            FROM pasienpulang_t a
            WHERE (a.carakeluar_id = 4)) pasienpulang_t ON ((pasienadmisi_t.pasienadmisi_id = pasienpulang_t.pasienadmisi_id)))
            LEFT JOIN ( SELECT a.carakeluar_id,
            a.carakeluar_nama
            FROM carakeluar_m a) carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            JOIN ( SELECT a.kondisikeluar_id,
            a.kondisikeluar_nama
            FROM kondisikeluar_m a) kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.diagnosa_id
            FROM asesmenmedis_t a) asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (pendaftaran_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id))))
            LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
            FROM kamarruangan_m a) kamar_keluar ON ((pasienadmisi_t.kamarruangan_id = kamar_keluar.kamarruangan_id)))
            LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
            FROM kamartempattidur_m a) tempattidur_keluar ON ((pasienadmisi_t.kamartempattidur_id = tempattidur_keluar.kamartempattidur_id)))
            WHERE (pendaftaran_t.is_deleted IS FALSE)
            ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
        $this->execute('
            ALTER TABLE public.laporansensusharianri_pasienmeninggal_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienpindahan_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporansensusharianri_pasienpindahan_v\" AS
            SELECT pasienadmisi_t.tgl_admisi,
            pindahkamar_t.tgl_pindahkamar,
            pasienadmisi_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pindahkamar_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            ruangan_skrg.ruangan_nama AS ruangan_skrg,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            asesmenmedis_t.diagnosa_id AS diagnosa_nama,
            (to_char(masukkamar_t.tgl_masukkamar, 'YYYY-MM-DD'::text))::date AS tgl_masukkamar,
            pasienpulang_t.lama_rawat,
            ruangan_asal.ruangan_id,
            ruangan_asal.ruangan_nama AS ruangan_dari,
            instalasi_asal.instalasi_id,
            instalasi_asal.instalasi_nama AS instalasi_dari,
            kamar_asal.kamarruangan_nokamar AS kamar_dari,
            tempattidur_asal.no_tempattidur AS tempattidur_dari,
            dokter_admisi.nama_pegawai AS dokter_admisi,
            pasienpulang_t.tglpasienpulang AS tgl_pasienplg
            FROM ((((((((((((((pasienadmisi_t
            JOIN ( SELECT a.pasienadmisi_id,
            a.pasien_id,
            a.pegawai_id,
            a.carabayar_id,
            a.penjamin_id,
            a.jeniskasuspenyakit_id,
            a.pendaftaran_id
            FROM pendaftaran_t a) pendaftaran_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id)))
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.pasienadmisi_id,
            a.pindahkamar_id,
            a.ruangan_id,
            a.tgl_masukkamar
            FROM masukkamar_t a) masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
            JOIN ( SELECT a.pindahkamar_id,
            a.kelaspelayanan_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            a.is_active,
            a.is_deleted,
            a.tgl_pindahkamar
            FROM pindahkamar_t a) pindahkamar_t ON ((masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id)))
            JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dokter_admisi ON ((pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id)))
            JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
            FROM kelaspelayanan_m a) kelaspelayanan_m ON ((pindahkamar_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_skrg ON ((pindahkamar_t.ruangan_id = ruangan_skrg.ruangan_id)))
            JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_asal ON ((masukkamar_t.ruangan_id = ruangan_asal.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_asal ON ((ruangan_asal.instalasi_id = instalasi_asal.instalasi_id)))
            JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
            FROM kamarruangan_m a) kamar_asal ON ((pindahkamar_t.kamarruangan_id = kamar_asal.kamarruangan_id)))
            JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
            FROM kamartempattidur_m a) tempattidur_asal ON ((pindahkamar_t.kamartempattidur_id = tempattidur_asal.kamartempattidur_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.diagnosa_id
            FROM asesmenmedis_t a) asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (pendaftaran_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id))))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.lama_rawat,
            a.tglpasienpulang
            FROM pasienpulang_t a
            WHERE (a.pasienadmisi_id IS NOT NULL)) pasienpulang_t ON ((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id)))
            WHERE ((pindahkamar_t.is_active = true) AND (pindahkamar_t.is_deleted = false))
            ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
        $this->execute('
            ALTER TABLE public.laporansensusharianri_pasienpindahan_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienpindahkan_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporansensusharianri_pasienpindahkan_v\" AS
            SELECT pasienadmisi_t.tgl_admisi,
            pindahkamar_t.tgl_pindahkamar,
            pasienadmisi_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            masukkamar_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            ruangan_skrg.ruangan_nama AS ruangan_skrg,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            asesmenmedis_t.diagnosa_id AS diagnosa_nama,
            (to_char(masukkamar.tgl_masukkamar, 'YYYY-MM-DD'::text))::date AS tgl_masukkamar,
            masukkamar_t.lamadirawat_kamar AS lama_rawat,
            masukkamar_t.ruangan_id,
            ruangan_pindah.ruangan_nama AS ruangan_ke,
            instalasi_pindah.instalasi_id,
            instalasi_pindah.instalasi_nama AS instalasi_ke,
            kamar_pindah.kamarruangan_nokamar AS kamar_ke,
            kamar_skrg.kamarruangan_nokamar AS kamar_skrg,
            tempattidur_pindah.no_tempattidur AS tempattidur_ke,
            tempattidur_skrg.no_tempattidur AS tempattidur_skrg,
            dokter_admisi.nama_pegawai AS dokter_admisi,
            pasienpulang_t.tglpasienpulang AS tgl_pasienplg,
            masukkamar_t.tgl_masukkamar AS tgl_masukkamar_1,
            masukkamar_t.jam_masukkamar,
            masukkamar_t.tgl_keluarkamar,
            masukkamar_t.jam_keluarkamar
            FROM (((((((((((((((((pasienadmisi_t
            JOIN ( SELECT a.pasienadmisi_id,
            a.pegawai_id,
            a.carabayar_id,
            a.penjamin_id,
            a.pendaftaran_id,
            a.pasien_id
            FROM pendaftaran_t a) pendaftaran_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id)))
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.pasienadmisi_id,
            a.pindahkamar_id,
            a.kelaspelayanan_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            a.lamadirawat_kamar,
            a.tgl_masukkamar,
            a.jam_masukkamar,
            a.tgl_keluarkamar,
            a.jam_keluarkamar
            FROM masukkamar_t a) masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
            JOIN ( SELECT DISTINCT ON (a.pasienadmisi_id) a.pasienadmisi_id,
            a.masukkamar_id,
            a.tgl_masukkamar
            FROM masukkamar_t a) masukkamar ON ((pasienadmisi_t.pasienadmisi_id = masukkamar.pasienadmisi_id)))
            JOIN ( SELECT a.pindahkamar_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.is_active,
            a.is_deleted,
            a.tgl_pindahkamar
            FROM pindahkamar_t a) pindahkamar_t ON ((masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id)))
            JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dokter_admisi ON ((pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id)))
            JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
            FROM kelaspelayanan_m a) kelaspelayanan_m ON ((masukkamar_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_skrg ON ((masukkamar_t.ruangan_id = ruangan_skrg.ruangan_id)))
            JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_pindah ON ((pindahkamar_t.ruangan_id = ruangan_pindah.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_pindah ON ((ruangan_pindah.instalasi_id = instalasi_pindah.instalasi_id)))
            JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
            FROM kamarruangan_m a) kamar_pindah ON ((pindahkamar_t.kamarruangan_id = kamar_pindah.kamarruangan_id)))
            JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
            FROM kamarruangan_m a) kamar_skrg ON ((masukkamar_t.kamarruangan_id = kamar_skrg.kamarruangan_id)))
            JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
            FROM kamartempattidur_m a) tempattidur_pindah ON ((masukkamar_t.kamartempattidur_id = tempattidur_pindah.kamartempattidur_id)))
            JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
            FROM kamartempattidur_m a) tempattidur_skrg ON ((masukkamar_t.kamartempattidur_id = tempattidur_skrg.kamartempattidur_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.diagnosa_id
            FROM asesmenmedis_t a) asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (pendaftaran_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id))))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.tglpasienpulang
            FROM pasienpulang_t a
            WHERE (a.pasienadmisi_id IS NOT NULL)) pasienpulang_t ON ((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id)))
            WHERE ((pindahkamar_t.is_active = true) AND (pindahkamar_t.is_deleted = false))
            ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
        $this->execute('
            ALTER TABLE public.laporansensusharianri_pasienpindahkan_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210814_072849_migrate_US1004_view_laporansensusharianri_optimize cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210814_072849_migrate_US1004_view_laporansensusharianri_optimize cannot be reverted.\n";

        return false;
    }
    */
}
