<?php

use yii\db\Migration;

/**
 * Class m220130_020314_migrate_BTS85_RM_sensushariandanbulananranap
 */
class m220130_020314_migrate_BTS85_RM_sensushariandanbulananranap extends Migration
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
            look_jenkel.lookup_name AS jeniskelamin_nama,
            pasienadmisi_t.pasienpulang_id,
            pasienadmisi_t.tgl_pulang AS tgl_pasienplg,
            pasienadmisi_t.status_ranap_id,
            pasienadmisi_t.status_ranap_nama,
            (to_char(masukkamar_t.tgl_masukkamar, 'YYYY-MM-DD'::text))::date AS tgl_masukkamar,
            masukkamar_t.lamadirawat_kamar AS lama_rawat,
            masukkamar_t.tgl_masukkamar AS tgl_masukkamar_1,
            masukkamar_t.jam_masukkamar,
            masukkamar_t.tgl_keluarkamar,
            masukkamar_t.jam_keluarkamar
            FROM (((((((((((((((pendaftaran_t
            JOIN ( SELECT a.pasienadmisi_id,
            a.tgl_admisi,
            a.pasien_id,
            a.kamartempattidur_id,
            a.penjamin_id,
            a.pegawai_id,
            a.status_ranap AS status_ranap_id,
            look_status_pasien.lookup_name AS status_ranap_nama,
            a.pasienpulang_id,
            a.tgl_pulang
            FROM (pasienadmisi_t a
            LEFT JOIN lookup_m look_status_pasien ON ((a.status_ranap = look_status_pasien.lookup_id)))) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN ( SELECT a.pasienadmisi_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kelaspelayanan_id,
            a.tgl_masukkamar,
            a.masukkamar_id,
            a.kamartempattidur_id,
            a.lamadirawat_kamar,
            a.jam_masukkamar,
            a.tgl_keluarkamar,
            a.jam_keluarkamar
            FROM (masukkamar_t a
            JOIN ( SELECT max(b.masukkamar_id) AS max_masukkamar_id,
            b.pasienadmisi_id
            FROM masukkamar_t b
            GROUP BY b.pasienadmisi_id) max_kamar ON ((a.masukkamar_id = max_kamar.max_masukkamar_id)))) masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
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
            LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
            FROM kamarruangan_m a) kamar_keluar ON ((masukkamar_t.kamarruangan_id = kamar_keluar.kamarruangan_id)))
            LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
            FROM kamartempattidur_m a) tempattidur_keluar ON ((masukkamar_t.kamartempattidur_id = tempattidur_keluar.kamartempattidur_id)))
            LEFT JOIN ( SELECT a.pasienpulang_id,
            a.ruanganakhir_id,
            a.carakeluar_id,
            a.kondisikeluar_id,
            a.pasienadmisi_id,
            a.tglpasienpulang
            FROM pasienpulang_t a) pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            WHERE ((pendaftaran_t.is_deleted IS FALSE) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
            ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
            ;");
        $this->execute('
            ALTER TABLE public.laporansensusharianri_sedangrawatinap_v OWNER TO postgres;
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
            WHEN ((pasienpulang_t.kondisikeluar_id = 7) AND (((pasienpulang_t.tglpasienpulang)::date - (pasienadmisi_t.tgl_admisi)::date) <= 2)) THEN 1
            ELSE 0
            END AS lama_rawat_kur48,
            CASE
            WHEN ((pasienpulang_t.kondisikeluar_id = 5) AND (((pasienpulang_t.tglpasienpulang)::date - (pasienadmisi_t.tgl_admisi)::date) >= 2)) THEN 1
            ELSE 0
            END AS lama_rawat_leb48,
            pegawai_m.nama_pegawai AS nama_dokter,
            pasienpulang_t.tglpasienpulang AS tgl_pasienplg,
            pasienpulang_t.tgl_meninggal AS tgl_pasienmeninggal,
            ((pasienpulang_t.tglpasienpulang)::date - (pasienadmisi_t.tgl_admisi)::date) AS lama_rawat,
            kondisikeluar_m.kondisikeluar_id AS kondisi_keluar_id,
            kondisikeluar_m.kondisikeluar_nama AS kondisi_keluar_nama,
            pasienadmisi_t.status_ranap_id,
            pasienadmisi_t.status_ranap_nama
            FROM ((((((((((((((pendaftaran_t
            LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pasien_id,
            a.kelaspelayanan_id,
            a.ruangan_id,
            a.pegawai_id,
            a.penjamin_id,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            a.tgl_admisi,
            a.status_ranap AS status_ranap_id,
            look_status_pasien.lookup_name AS status_ranap_nama
            FROM (pasienadmisi_t a
            LEFT JOIN lookup_m look_status_pasien ON ((a.status_ranap = look_status_pasien.lookup_id)))) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
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

        $this->execute("
            DROP FUNCTION if exists public.laporansensusharianri_rekapitulasi_fn;
        ");
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"laporansensusharianri_rekapitulasi_fn\"(\"xfirstdate\" date, \"xlastdate\" date, \"xruangan_id\" int4, \"xkelaspelayanan_id\" int4, \"xstatus_pasien\" int4)
  RETURNS TABLE(\"vtanggal\" varchar, \"pasien_hari_sebelumnya\" int4, \"pasien_masuk\" int4, \"pasien_pindahan\" int4, \"jml_123\" int4, \"keluar_hidup\" int4, \"keluar_dipindahkan\" int4, \"keluar_meninggaljml\" int4, \"keluar_meninggalkur48\" int4, \"keluar_meninggalleb48\" int4, \"keluar_rujukrslain\" int4, \"jml_567\" int4) AS \$BODY\$

DECLARE
    vdays int4;
    vdate int4;
    tanggal date;
    vhari varchar;
    vtemp_pasien_awal int4;

BEGIN
SELECT (xlastdate::date - xfirstdate::date) + 1 INTO vdays;
vdate := 0;

WHILE vdate < vdays
LOOP
    IF vdate = 0
    THEN
        SELECT DATE_TRUNC('day', xfirstdate::TIMESTAMP) INTO vtanggal;
    ELSE
        SELECT DATE_TRUNC('day', vtanggal::TIMESTAMP) + '1 DAY'::INTERVAL INTO vtanggal;
    END IF;
    
    
    -- FORMAT TANGGAL
    vtanggal := vtanggal::DATE;
    
    -- PASIEN AWAL
    SELECT 0 INTO pasien_hari_sebelumnya;
    
--  IF (vdate = 0)
--  THEN
--      SELECT f_getpasienawal(vtanggal::DATE, xruangan_id, xkelaspelayanan_id) INTO pasien_hari_sebelumnya;
--  ELSE
--      pasien_hari_sebelumnya := vtemp_pasien_awal;
--  END IF; 
    
--  IF (vdate = 0)
--  THEN
--      SELECT
--          pasien_akhir INTO pasien_hari_sebelumnya
--      FROM sensuspasienranap_r
--      WHERE ruangan_id = xruangan_id
--      AND kelaspelayanan_id = xkelaspelayanan_id
--      AND tgl_sensus = vtanggal::DATE;
--  ELSE
--      SELECT
--          pasien_akhir INTO pasien_hari_sebelumnya
--      FROM sensuspasienranap_r
--      WHERE tgl_sensus = vtanggal::DATE;
        
    --END IF;
    
    -- PASIEN MASUK
    SELECT f_getpasienmasuk(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO pasien_masuk;
    
    -- PASIEN PINDAHAN
    SELECT f_getpasienpindahan(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO pasien_pindahan;
--  SELECT
--          pasien_pindahan INTO pasien_pindahan
--      FROM sensuspasienranap_r
--      WHERE ruangan_id = xruangan_id
--      AND kelaspelayanan_id = xkelaspelayanan_id
--      AND tgl_sensus = vtanggal::DATE;
    
    -- JUMLAH KOLOM 1+2+3
    --SELECT pasien_hari_sebelumnya + pasien_masuk + pasien_pindahan INTO jml_123;
    SELECT 0 INTO jml_123;
    
    -- KELUAR HIDUP
    SELECT f_getkeluarhidup(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO keluar_hidup;
    
    -- KELUAR DIPINDAHKAN
    SELECT f_getkeluardipindahkan(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO keluar_dipindahkan;
    
    -- KELUAR MENINGGAL KURANG DARI 48 JAM
    SELECT f_getkeluarmeninggalkur48(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO keluar_meninggalkur48;
    
    -- KELUAR MENINGGAL LEBIH DARI 48 JAM
    SELECT f_getkeluarmeninggalleb48(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO keluar_meninggalleb48;
    
    -- JUMLAH KELUAR MENINGGAL
    SELECT keluar_meninggalkur48 + keluar_meninggalleb48 INTO keluar_meninggaljml;
    
    
    SELECT f_getkelurrujukrslain(vtanggal::DATE, xruangan_id, xkelaspelayanan_id, xstatus_pasien) INTO keluar_rujukrslain;
    
    -- JUMLAH KOLOM 5+6+7
    --SELECT keluar_hidup + keluar_dipindahkan + keluar_meninggaljml INTO jml_567;
    SELECT 0 INTO jml_567;
    
    
    -- PASIEN AKHIR
    --SELECT jml_123 - jml_567 INTO pasien_akhir_rekap;
    
    -- SET TEMP UNTUK PASIEN AWAL
    --vtemp_pasien_awal := pasien_akhir_rekap;
    
    
    -- RETURN DATA ROW
    RETURN NEXT;
    
    vdate  := vdate + 1;
END LOOP;

END
\$BODY\$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000
        ");

        $this->execute("
            DROP FUNCTION if exists public.f_getkelurrujukrslain;
        ");
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"f_getkelurrujukrslain\"(\"xtanggal\" date, \"xruangan_id\" int4, \"xkelaspelayanan_id\" int4, \"xstatus_pasien\" int4)
  RETURNS TABLE(\"keluar_rujukrslain\" int4) AS \$BODY\$
BEGIN

IF (xruangan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_rujukrslain
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
--     WHERE pasienpulang_t.tglpasienpulang::DATE = '2022-01-05'
        WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 2
        AND pasienpulang_t.kondisikeluar_id = 3 
    AND pasienpulang_t.ruanganakhir_id = xruangan_id;
END IF;

IF (xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_rujukrslain
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
--     WHERE pasienpulang_t.tglpasienpulang::DATE = '2022-01-05'
        WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 2
        AND pasienpulang_t.kondisikeluar_id = 3 
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_rujukrslain
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
--     WHERE pasienpulang_t.tglpasienpulang::DATE = '2022-01-05'
        WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 2
        AND pasienpulang_t.kondisikeluar_id = 3 
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_rujukrslain
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
--     WHERE pasienpulang_t.tglpasienpulang::DATE = '2022-01-05'
        WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 2
        AND pasienpulang_t.kondisikeluar_id = 3 
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_rujukrslain
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
--     WHERE pasienpulang_t.tglpasienpulang::DATE = '2022-01-05'
        WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 2
        AND pasienpulang_t.kondisikeluar_id = 3 
    AND pasienpulang_t.ruanganakhir_id = xruangan_id
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_rujukrslain
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
--     WHERE pasienpulang_t.tglpasienpulang::DATE = '2022-01-05'
        WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 2
        AND pasienpulang_t.kondisikeluar_id = 3 
    AND pasienpulang_t.ruanganakhir_id = xruangan_id
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_rujukrslain
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
--     WHERE pasienpulang_t.tglpasienpulang::DATE = '2022-01-05'
        WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 2
        AND pasienpulang_t.kondisikeluar_id = 3 
    AND pasienpulang_t.ruanganakhir_id = xruangan_id;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_rujukrslain
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
--     WHERE pasienpulang_t.tglpasienpulang::DATE = '2022-01-05'
        WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 2
        AND pasienpulang_t.kondisikeluar_id = 3 
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_rujukrslain
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
--     WHERE pasienpulang_t.tglpasienpulang::DATE = '2022-01-05'
        WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 2
        AND pasienpulang_t.kondisikeluar_id = 3 ;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL AND xstatus_pasien IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_rujukrslain
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
--     WHERE pasienpulang_t.tglpasienpulang::DATE = '2022-01-05'
        WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 2
        AND pasienpulang_t.kondisikeluar_id = 3 
    AND pasienpulang_t.ruanganakhir_id = xruangan_id
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
    AND pasienadmisi_t.status_ranap = xstatus_pasien;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL AND xstatus_pasien IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_rujukrslain
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
--     WHERE pasienpulang_t.tglpasienpulang::DATE = '2022-01-05'
        WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 2
        AND pasienpulang_t.kondisikeluar_id = 3;
END IF;
    
-- RETURN DATA
RETURN NEXT;

END
\$BODY\$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220130_020314_migrate_BTS85_RM_sensushariandanbulananranap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220130_020314_migrate_BTS85_RM_sensushariandanbulananranap cannot be reverted.\n";

        return false;
    }
    */
}
