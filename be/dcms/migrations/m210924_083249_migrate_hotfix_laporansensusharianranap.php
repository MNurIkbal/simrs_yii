<?php

use yii\db\Migration;

/**
 * Class m210924_083249_migrate_hotfix_laporansensusharianranap
 */
class m210924_083249_migrate_hotfix_laporansensusharianranap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"f_getkeluarmeninggalkur48\"(\"xtanggal\" date, \"xruangan_id\" int4, \"xkelaspelayanan_id\" int4)
  RETURNS TABLE(\"keluar_meninggalkur48\" int4) AS \$BODY\$
BEGIN

IF (xruangan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
    AND  COALESCE(pasienpulang_t.lama_rawat,0) <= 2
    AND pasienpulang_t.ruanganakhir_id = xruangan_id;
END IF;

IF (xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
    AND  COALESCE(pasienpulang_t.lama_rawat,0) <= 2
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
    AND  COALESCE(pasienpulang_t.lama_rawat,0) <= 2
    AND pasienpulang_t.ruanganakhir_id = xruangan_id
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalkur48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND  COALESCE(pasienpulang_t.lama_rawat,0) <= 2
    AND pasienpulang_t.carakeluar_id = 4;
END IF;
    
-- RETURN DATA
RETURN NEXT;

END
\$BODY\$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000");

        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"f_getkeluarmeninggalleb48\"(\"xtanggal\" date, \"xruangan_id\" int4, \"xkelaspelayanan_id\" int4)
  RETURNS TABLE(\"keluar_meninggalleb48\" int4) AS \$BODY\$
BEGIN

IF (xruangan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalleb48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
    AND COALESCE(pasienpulang_t.lama_rawat,0) > 2
    AND pasienpulang_t.ruanganakhir_id = xruangan_id;
END IF;

IF (xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalleb48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
    AND COALESCE(pasienpulang_t.lama_rawat,0) > 2
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalleb48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND pasienpulang_t.carakeluar_id = 4
    AND COALESCE(pasienpulang_t.lama_rawat,0) > 2
    AND pasienpulang_t.ruanganakhir_id = xruangan_id
    AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
END IF;

IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL)
THEN
    SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_meninggalleb48
    FROM pasienadmisi_t
    LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
    WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
    AND pasienpulang_t.pasienadmisi_id IS NOT NULL
    AND COALESCE(pasienpulang_t.lama_rawat,0) > 2
    AND pasienpulang_t.carakeluar_id = 4;
END IF;
    
-- RETURN DATA
RETURN NEXT;

END
\$BODY\$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000");

        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"pindahkamar_rekapsensuspasienranap_lastroom\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
        vid INTEGER;
        vruangan_id INTEGER;
        vkelaspelayanan_id INTEGER;
        vpasien_awal INTEGER;
        vpasien_keluardipindahkan INTEGER;
        vpasien_akhir INTEGER;
        vpasien_akhir_last INTEGER;
        
BEGIN
                    IF (NEW.pindahkamar_id IS NULL) THEN
                            RETURN NEW;
                    END IF;
                    vruangan_id := NEW.ruangan_id;
                    vkelaspelayanan_id := NEW.kelaspelayanan_id;
--         SELECT k.ruangan_id, k.kelaspelayanan_id INTO vruangan_id, vkelaspelayanan_id
--         FROM masukkamar_t AS m
--         LEFT JOIN pindahkamar_t AS p ON (
--             (p.ruangan_id = m.ruangan_id) AND (p.pasienadmisi_id = m.pasienadmisi_id) AND (p.kelaspelayanan_id = m.kelaspelayanan_id)
--         )
--         LEFT JOIN masukkamar_t AS k ON (k.pindahkamar_id = p.pindahkamar_id)
--         WHERE m.pasienadmisi_id = NEW.pasienadmisi_id
--         ORDER BY m.masukkamar_id DESC
--         LIMIT 1;
        
        -- identifikasi untuk data insert/update = vid
        SELECT id INTO vid
        FROM sensuspasienranap_r
        WHERE tgl_sensus::DATE = CURRENT_DATE
        AND ruangan_id = vruangan_id
        AND kelaspelayanan_id = vkelaspelayanan_id;

        -- cek vid untuk insert/update
        IF (vid IS NULL) THEN
                -- cari last pasien akhir
                SELECT pasien_akhir INTO vpasien_akhir_last
                FROM sensuspasienranap_r
                WHERE tgl_sensus::DATE < CURRENT_DATE
                AND ruangan_id = vruangan_id
                AND kelaspelayanan_id = vkelaspelayanan_id
                ORDER BY id DESC
                LIMIT 1;
                
                -- cek last pasien akhir jika null isi 0
                IF (vpasien_akhir_last IS NULL) THEN
                        vpasien_akhir_last := 0;
                END IF;
                
                -- set pasien akhir - 1
                vpasien_awal := vpasien_akhir_last;
                vpasien_keluardipindahkan := 1;
                vpasien_akhir := vpasien_awal - 1;
                
                INSERT INTO sensuspasienranap_r (       
                        ruangan_id,
                        kelaspelayanan_id,
                        tgl_sensus,
                        pasien_awal,
                        pasien_masuk,
                        pasien_pindahan,
                        pasien_keluarhidup,
                        pasien_keluardipindahkan,
                        pasien_keluarmeninggalkur48,
                        pasien_keluarmeninggalleb48,
                        pasien_akhir
                ) VALUES (
                        vruangan_id,
                        vkelaspelayanan_id,
                        CURRENT_DATE,
                        vpasien_awal,
                        0,
                        0,
                        0,
                        vpasien_keluardipindahkan,
                        0,
                        0,
                        vpasien_akhir
                );
        ELSE
                -- cari last sensus
                SELECT pasien_keluardipindahkan, pasien_akhir INTO vpasien_keluardipindahkan, vpasien_akhir_last
                FROM sensuspasienranap_r
                WHERE id = vid;
                
                -- set pasien akhir - 1
                vpasien_awal := vpasien_akhir_last;
                vpasien_keluardipindahkan := 1;
                vpasien_akhir := vpasien_awal - 1;
                
                -- update sensus
                UPDATE sensuspasienranap_r SET
                        pasien_keluardipindahkan = pasien_keluardipindahkan + vpasien_keluardipindahkan,
                        pasien_akhir = vpasien_akhir
                WHERE id = vid;
        END IF;
        
        RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100");

        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"pindahkamar_rekapsensuspasienranap_newroom\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
        vid INTEGER;
        vruangan_id INTEGER;
        vkelaspelayanan_id INTEGER;
    vpasien_awal INTEGER;
        vpasien_pindahan INTEGER;
        vpasien_akhir INTEGER;
        vpasien_akhir_last INTEGER;
        
BEGIN
        vruangan_id := NEW.ruangan_id;
        vkelaspelayanan_id := NEW.kelaspelayanan_id;
        
        -- identifikasi untuk data insert/update = vid
        SELECT id INTO vid
        FROM sensuspasienranap_r
        WHERE tgl_sensus::DATE = CURRENT_DATE
        AND ruangan_id = vruangan_id
        AND kelaspelayanan_id = vkelaspelayanan_id;

        -- cek vid untuk insert/update
        IF (vid IS NULL) THEN
                -- cari last pasien akhir
                SELECT pasien_akhir INTO vpasien_akhir_last
                FROM sensuspasienranap_r
                WHERE tgl_sensus::DATE < CURRENT_DATE
                AND ruangan_id = vruangan_id
                AND kelaspelayanan_id = vkelaspelayanan_id
                ORDER BY id DESC
                LIMIT 1;
                
                -- cek last pasien akhir jika null isi 0
                IF (vpasien_akhir_last IS NULL) THEN
                        vpasien_akhir_last := 0;
                END IF;
                
                -- set pasien akhir - 1
                vpasien_awal := vpasien_akhir_last;
                vpasien_pindahan := 1;
                vpasien_akhir := vpasien_awal - 1;
                
                INSERT INTO sensuspasienranap_r (       
                        ruangan_id,
                        kelaspelayanan_id,
                        tgl_sensus,
                        pasien_awal,
                        pasien_masuk,
                        pasien_pindahan,
                        pasien_keluarhidup,
                        pasien_keluardipindahkan,
                        pasien_keluarmeninggalkur48,
                        pasien_keluarmeninggalleb48,
                        pasien_akhir
                ) VALUES (
                        vruangan_id,
                        vkelaspelayanan_id,
                        CURRENT_DATE,
                        vpasien_awal,
                        0,
                        vpasien_pindahan,
                        0,
                        0,
                        0,
                        0,
                        vpasien_akhir
                );
        ELSE
                -- cari last sensus
                SELECT pasien_pindahan, pasien_akhir INTO vpasien_pindahan, vpasien_akhir_last
                FROM sensuspasienranap_r
                WHERE id = vid;
                
                -- set pasien akhir - 1
                vpasien_awal := vpasien_akhir_last;
                vpasien_pindahan := 1;
                vpasien_akhir := vpasien_awal - 1;
                
                -- update sensus
                UPDATE sensuspasienranap_r SET
                        pasien_pindahan = pasien_pindahan + vpasien_pindahan,
                        pasien_akhir = vpasien_akhir
                WHERE id = vid;
        END IF;
        
        RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100");

        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"rekapsensuspasienranap_batal\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE

   vruangan_id INTEGER;
   vkelaspelayanan_id INTEGER;
   vtgl_admisi DATE;
     vid INTEGER;
BEGIN
        vruangan_id := NEW.ruangan_id;
        vkelaspelayanan_id := NEW.kelaspelayanan_id;
    vtgl_admisi := NEW.tgl_admisi::DATE;
        
        IF (NEW.status_ranap = 453) THEN
            SELECT id INTO vid
        FROM sensuspasienranap_r
        WHERE tgl_sensus::DATE = vtgl_admisi
        AND ruangan_id = vruangan_id
        AND kelaspelayanan_id = vkelaspelayanan_id;
                
                IF (vid IS NOT NULL) THEN
                UPDATE sensuspasienranap_r SET
                        pasien_masuk = pasien_masuk - 1,
                        pasien_akhir = pasien_akhir - 1
                WHERE id = vid;
                END IF;

        END IF;


    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100");

        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"f_getkeluarhidup\"(\"xtanggal\" date, \"xruangan_id\" int4, \"xkelaspelayanan_id\" int4)
  RETURNS TABLE(\"keluar_hidup\" int4) AS \$BODY\$
            BEGIN

            IF (xruangan_id IS NOT NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
            FROM pasienadmisi_t
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id NOT IN (4)
            AND pasienpulang_t.ruanganakhir_id = xruangan_id;
            END IF;

            IF (xkelaspelayanan_id IS NOT NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
            FROM pasienadmisi_t
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id NOT IN (4)
            AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
            END IF;

            IF (xruangan_id IS NOT NULL AND xkelaspelayanan_id IS NOT NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
            FROM pasienadmisi_t
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id NOT IN (4)
            AND pasienpulang_t.ruanganakhir_id = xruangan_id
            AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id;
            END IF;

            IF (xruangan_id IS NULL AND xkelaspelayanan_id IS NULL)
            THEN
            SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO keluar_hidup
            FROM pasienadmisi_t
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = xtanggal::DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id NOT IN (4);
            END IF;

            -- RETURN DATA
            RETURN NEXT;

            END
            \$BODY\$
  LANGUAGE plpgsql IMMUTABLE
  COST 100
  ROWS 1000");

        // $this->execute('DROP VIEW if exists public.laporansensusharianri_pasienkeluarpindahrslain_v;');
        // $this->execute("
        //     CREATE VIEW \"public\".\"laporansensusharianri_pasienkeluarpindahrslain_v\" AS
        //     SELECT pendaftaran_t.no_pendaftaran,
        //     pasienadmisi_t.tgl_admisi,
        //     pasien_m.pasien_id,
        //     pasien_m.nama_pasien,
        //     pasien_m.no_rekam_medik,
        //     pendaftaran_t.kelaspelayanan_id,
        //     kelaspelayanan_m.kelaspelayanan_nama,
        //     ruangan_m.ruangan_id,
        //     ruangan_m.ruangan_nama,
        //     instalasi_m.instalasi_id,
        //     instalasi_m.instalasi_nama,
        //     kamar_keluar.kamarruangan_nokamar AS kamar,
        //     tempattidur_keluar.no_tempattidur AS tempattidur,
        //     asesmenmedis_t.diagnosa_id AS diagnosa_nama,
        //     penjamin_m.penjamin_nama,
        //     (to_char(masukkamar.tgl_masukkamar, 'YYYY-MM-DD'::text))::date AS tgl_masukkamar,
        //     masukkamar.lamadirawat_kamar AS lama_rawat,
        //     pegawai_m.nama_pegawai AS nama_dokter,
        //     COALESCE(rujukankeluar_m.rumahsakit_rujukan, rujukanpulang_t.rujukan_dituju, NULL::character varying) AS rumahsakit_rujukan,
        //     pasienpulang_t.tglpasienpulang AS tgl_pasienplg,
        //     masukkamar.tgl_masukkamar AS tgl_masukkamar_1,
        //     masukkamar.jam_masukkamar,
        //     masukkamar.tgl_keluarkamar,
        //     masukkamar.jam_keluarkamar
        //     FROM (((((((((((((((((pendaftaran_t
        //     JOIN ( SELECT a.pasienadmisi_id,
        //     a.tgl_admisi,
        //     a.ruangan_id,
        //     a.pasienpulang_id,
        //     a.pegawai_id,
        //     a.penjamin_id
        //     FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
        //     JOIN ( SELECT a.pasien_id,
        //     a.nama_pasien,
        //     a.no_rekam_medik
        //     FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
        //     JOIN ( SELECT a.kelaspelayanan_id,
        //     a.kelaspelayanan_nama
        //     FROM kelaspelayanan_m a) kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
        //     JOIN ( SELECT a.ruangan_id,
        //     a.ruangan_nama
        //     FROM ruangan_m a) ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
        //     JOIN ( SELECT a.instalasi_id,
        //     a.instalasi_nama
        //     FROM instalasi_m a) instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
        //     JOIN ( SELECT a.pasienpulang_id,
        //     a.carakeluar_id,
        //     a.kondisikeluar_id,
        //     a.pasiendirujukkeluar_id,
        //     a.tglpasienpulang
        //     FROM pasienpulang_t a) pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
        //     LEFT JOIN ( SELECT a.carakeluar_id
        //     FROM carakeluar_m a) carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
        //     LEFT JOIN ( SELECT a.kondisikeluar_id
        //     FROM kondisikeluar_m a) kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
        //     JOIN ( SELECT a.pegawai_id,
        //     a.nama_pegawai
        //     FROM pegawai_m a) pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
        //     JOIN ( SELECT a.penjamin_id,
        //     a.penjamin_nama
        //     FROM penjamin_m a) penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
        //     LEFT JOIN ( SELECT a.pendaftaran_id,
        //     a.pasienadmisi_id,
        //     a.diagnosa_id
        //     FROM asesmenmedis_t a) asesmenmedis_t ON (((pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id) AND (pendaftaran_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id))))
        //     JOIN ( SELECT DISTINCT ON (a.pasienadmisi_id) a.pasienadmisi_id,
        //     a.masukkamar_id,
        //     a.tgl_masukkamar,
        //     a.kamarruangan_id,
        //     a.kamartempattidur_id,
        //     a.lamadirawat_kamar,
        //     a.jam_masukkamar,
        //     a.tgl_keluarkamar,
        //     a.jam_keluarkamar
        //     FROM masukkamar_t a) masukkamar ON ((pasienadmisi_t.pasienadmisi_id = masukkamar.pasienadmisi_id)))
        //     JOIN ( SELECT a.kamarruangan_id,
        //     a.kamarruangan_nokamar
        //     FROM kamarruangan_m a) kamar_keluar ON ((masukkamar.kamarruangan_id = kamar_keluar.kamarruangan_id)))
        //     JOIN ( SELECT a.kamartempattidur_id,
        //     a.no_tempattidur
        //     FROM kamartempattidur_m a) tempattidur_keluar ON ((masukkamar.kamartempattidur_id = tempattidur_keluar.kamartempattidur_id)))
        //     LEFT JOIN ( SELECT a.pasiendirujukkeluar_id,
        //     a.rujukankeluar_id
        //     FROM pasiendirujukkeluar_t a) pasiendirujukkeluar_t ON ((pasienpulang_t.pasiendirujukkeluar_id = pasiendirujukkeluar_t.pasiendirujukkeluar_id)))
        //     LEFT JOIN ( SELECT a.rujukankeluar_id,
        //     a.rumahsakit_rujukan
        //     FROM rujukankeluar_m a) rujukankeluar_m ON ((pasiendirujukkeluar_t.rujukankeluar_id = rujukankeluar_m.rujukankeluar_id)))
        //     LEFT JOIN ( SELECT a.pendaftaran_id,
        //     a.rujukan_dituju
        //     FROM rujukanpulang_t a) rujukanpulang_t ON ((rujukanpulang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
        //     WHERE ((pendaftaran_t.is_deleted IS FALSE) AND (pasienpulang_t.carakeluar_id = 2) AND (pasienpulang_t.kondisikeluar_id = 3))
        //     ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date DESC
        //     ;");
        // $this->execute('
        //     ALTER TABLE public.laporansensusharianri_pasienkeluarpindahrslain_v OWNER TO postgres;
        //     ');


        $this->execute('DROP TRIGGER if exists "rekapsensuspasienranap_batal" ON "public"."pasienadmisi_t";');
        
        $this->execute('DROP TRIGGER if exists "pindahkamar_rekapsensuspasienranap_lastroom" ON "public"."masukkamar_t";');
        

        $this->execute('CREATE TRIGGER "rekapsensuspasienranap_batal" AFTER UPDATE ON "public"."pasienadmisi_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."rekapsensuspasienranap_batal"();');

        $this->execute('CREATE TRIGGER "pindahkamar_rekapsensuspasienranap_lastroom" AFTER UPDATE ON "public"."masukkamar_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."pindahkamar_rekapsensuspasienranap_lastroom"();');

        $this->execute('
            ALTER TABLE "public"."pindahkamar_t" DISABLE TRIGGER "pindahkamar_rekapsensuspasienranap_lastroom";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210924_083249_migrate_hotfix_laporansensusharianranap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210924_083249_migrate_hotfix_laporansensusharianranap cannot be reverted.\n";

        return false;
    }
    */
}
