<?php

use yii\db\Migration;

/**
 * Class m210218_101024_migrate_20210218_3362_func_f_cronsensuspasienranap
 */
class m210218_101024_migrate_20210218_3362_func_f_cronsensuspasienranap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.f_cronsensuspasienranap;
        ");
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"f_cronsensuspasienranap\"()
            RETURNS TABLE(\"return\" bool) AS \$BODY\$

            DECLARE
            vid int8;
            vruangan_id int4;
            vkelaspelayanan_id int4;
            vpasien_awal int4;
            vpasien_masuk int4;
            vpasien_pindahan int4;
            vpasien_keluarhidup int4;
            vpasien_keluardipindahkan int4;
            vpasien_keluarmeninggalkur48 int4;
            vpasien_keluarmeninggalleb48 int4;
            vpasien_akhir int4;

            BEGIN
            -- looping untuk pasien awal dan pasien masuk
            FOR
            vruangan_id,
            vkelaspelayanan_id,
            vpasien_masuk
            IN
            -- Original Kang Sigit --
            --      SELECT
            --          ruangan_id,
            --          kelaspelayanan_id,
            --          COUNT(ruangan_id) AS pasien_masuk
            --      FROM pasienadmisi_t
            --      WHERE tgl_admisi::DATE = CURRENT_DATE
            --      GROUP BY ruangan_id, kelaspelayanan_id
            -- Improve by Rido --
            SELECT
            masukkamar_t.ruangan_id,
            masukkamar_t.kelaspelayanan_id,
            COUNT(masukkamar_t.ruangan_id) AS pasien_masuk
            FROM pendaftaran_t
            JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
            LEFT JOIN kamarruangan_m ON masukkamar_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
            WHERE pasienadmisi_t.tgl_admisi::DATE = CURRENT_DATE
            AND pendaftaran_t.is_deleted IS FALSE
            AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone) 
            AND pendaftaran_t.pasienbatalperiksa_id IS NULL
            GROUP BY masukkamar_t.ruangan_id, masukkamar_t.kelaspelayanan_id
            LOOP
            -- get pasien akhir dengan ruangan dan kelas yang ada di hari sebelumnya
            SELECT pasien_akhir INTO vpasien_awal
            FROM sensuspasienranap_r
            WHERE ruangan_id = vruangan_id
            AND kelaspelayanan_id = vkelaspelayanan_id
            AND tgl_sensus::DATE != CURRENT_DATE
            ORDER BY tgl_sensus DESC
            LIMIT 1;

            IF (vpasien_awal IS NULL) THEN
            vpasien_awal := 0;
            END IF;

            vpasien_akhir := vpasien_awal + vpasien_masuk;

            -- insert data
            INSERT INTO sensuspasienranap_r (
            ruangan_id,
            kelaspelayanan_id,
            tgl_sensus,
            pasien_awal,
            pasien_masuk,
            pasien_akhir
            ) VALUES (
            vruangan_id,
            vkelaspelayanan_id,
            CURRENT_DATE,
            vpasien_awal,
            vpasien_masuk,
            vpasien_akhir
            );
            END LOOP;

            -- looping untuk pasien pindahan
            FOR
            vruangan_id,
            vkelaspelayanan_id,
            vpasien_pindahan
            IN
            -- Original Kang Sigit --
            --      SELECT
            --          pasienadmisi_t.ruangan_id,
            --          pasienadmisi_t.kelaspelayanan_id,
            --          COUNT(pasienadmisi_t.ruangan_id) AS pasien_pindahan
            --      FROM pasienadmisi_t
            --      LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            --      WHERE pindahkamar_t.tgl_pindahkamar::DATE = CURRENT_DATE
            --      GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id
            -- Improve by Rido --
            --  SELECT
            --          masukkamar.ruangan_id,
            --          masukkamar.kelaspelayanan_id,
            --          COUNT(masukkamar.ruangan_id) AS pasien_pindahan
            --      FROM pendaftaran_t
            --      JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            --      JOIN ( SELECT masukkamar_t_1.pasienadmisi_id,
            --             masukkamar_t_1.masukkamar_id,
            --                      masukkamar_t_1.kelaspelayanan_id,
            --                      masukkamar_t_1.ruangan_id,
            --             masukkamar_t_1.tgl_masukkamar
            --            FROM (masukkamar_t masukkamar_t_1
            --              JOIN ( SELECT min(masukkamar_t_2.masukkamar_id) AS max_id,
            --                     masukkamar_t_2.pasienadmisi_id
            --                    FROM masukkamar_t masukkamar_t_2
            --                   GROUP BY masukkamar_t_2.pasienadmisi_id) max_masuk ON (((masukkamar_t_1.pasienadmisi_id = max_masuk.pasienadmisi_id) AND (masukkamar_t_1.masukkamar_id = max_masuk.max_id))))) masukkamar ON pasienadmisi_t.pasienadmisi_id = masukkamar.pasienadmisi_id
            --      LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            --      WHERE pindahkamar_t.tgl_pindahkamar::DATE = CURRENT_DATE
            --      GROUP BY masukkamar.ruangan_id, masukkamar.kelaspelayanan_id

            SELECT
            masukkamar_t.ruangan_id,
            masukkamar_t.kelaspelayanan_id,
            COUNT(pasienadmisi_t.ruangan_id) AS pasien_pindahan
            FROM pasienadmisi_t
            JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
            WHERE masukkamar_t.tgl_masukkamar::DATE = CURRENT_DATE
            AND masukkamar_t.pindahkamar_id IS NOT NULL
            GROUP BY masukkamar_t.ruangan_id, masukkamar_t.kelaspelayanan_id
            LOOP
            -- get id
            SELECT id, pasien_akhir INTO vid, vpasien_akhir
            FROM sensuspasienranap_r
            WHERE ruangan_id = vruangan_id
            AND kelaspelayanan_id = vkelaspelayanan_id
            AND tgl_sensus = CURRENT_DATE;

            IF (vpasien_pindahan IS NULL) THEN
            vpasien_pindahan := 0;
            END IF;

            IF (vpasien_akhir IS NULL) THEN
            vpasien_akhir := 0;
            END IF;

            vpasien_akhir := vpasien_akhir + vpasien_pindahan;

            -- cek id, kalau ada insert, kalau tidak update
            IF (vid IS NULL) THEN
            -- insert data
            INSERT INTO sensuspasienranap_r (
            ruangan_id,
            kelaspelayanan_id,
            tgl_sensus,
            pasien_pindahan,
            pasien_akhir
            ) VALUES (
            vruangan_id,
            vkelaspelayanan_id,
            CURRENT_DATE,
            vpasien_pindahan,
            vpasien_akhir
            );
            ELSE
            -- update data
            UPDATE sensuspasienranap_r SET 
            pasien_pindahan = vpasien_pindahan,
            pasien_akhir = vpasien_akhir
            WHERE id = vid;
            END IF;
            END LOOP;

            -- looping untuk pasien keluar hidup
            FOR
            vruangan_id,
            vkelaspelayanan_id,
            vpasien_keluarhidup 
            IN
            -- Original Kang Sigit --
            --      SELECT
            --          pasienadmisi_t.ruangan_id,
            --          pasienadmisi_t.kelaspelayanan_id,
            --          COUNT(pasienadmisi_t.ruangan_id) AS pasien_keluarhidup
            --      FROM pasienadmisi_t
            --      LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            --      WHERE pasienpulang_t.tglpasienpulang::DATE = CURRENT_DATE
            --      AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            --      AND pasienpulang_t.carakeluar_id IN (1,2,3,5,6,7)
            --      GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id
            -- Improve by Rido --
            SELECT
            pasienadmisi_t.ruangan_id,
            pendaftaran_t.kelaspelayanan_id,
            COUNT(pasienadmisi_t.ruangan_id) AS pasien_keluarhidup
            FROM pendaftaran_t
            JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = CURRENT_DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id IN (1,2,3,5,6,7)
            GROUP BY pasienadmisi_t.ruangan_id, pendaftaran_t.kelaspelayanan_id
            LOOP
            -- get id
            SELECT id, pasien_akhir INTO vid, vpasien_akhir
            FROM sensuspasienranap_r
            WHERE ruangan_id = vruangan_id
            AND kelaspelayanan_id = vkelaspelayanan_id
            AND tgl_sensus = CURRENT_DATE;

            IF (vpasien_keluarhidup IS NULL) THEN
            vpasien_keluarhidup := 0;
            END IF;

            IF (vpasien_akhir IS NULL) THEN
            vpasien_akhir := 0;
            END IF;

            vpasien_akhir := vpasien_akhir - vpasien_keluarhidup;

            -- cek id, kalau ada insert, kalau tidak update
            IF (vid IS NULL) THEN
            -- insert data
            INSERT INTO sensuspasienranap_r (
            ruangan_id,
            kelaspelayanan_id,
            tgl_sensus,
            pasien_keluarhidup,
            pasien_akhir
            ) VALUES (
            vruangan_id,
            vkelaspelayanan_id,
            CURRENT_DATE,
            vpasien_keluarhidup,
            vpasien_akhir
            );
            ELSE
            -- update data
            UPDATE sensuspasienranap_r SET 
            pasien_keluarhidup = vpasien_keluarhidup,
            pasien_akhir = vpasien_akhir
            WHERE id = vid;
            END IF;
            END LOOP;

            -- looping untuk pasien keluar dipindahkan
            FOR
            vruangan_id,
            vkelaspelayanan_id,
            vpasien_keluardipindahkan
            IN
            -- Original Kang Sigit
            --      SELECT
            --          pasienadmisi_t.ruangan_id,
            --          pasienadmisi_t.kelaspelayanan_id,
            --          COUNT(pasienadmisi_t.ruangan_id) AS pasien_keluardipindahkan
            --      FROM pasienadmisi_t
            --      LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            --      LEFT JOIN masukkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
            --      WHERE pindahkamar_t.tgl_pindahkamar::DATE = CURRENT_DATE
            --      AND masukkamar_t.pindahkamar_id IS NOT NULL
            --      GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id
            -- Improve By Rido --
            SELECT
            masukkamar_t.ruangan_id,
            pindahkamar_t.kelaspelayanan_id,
            COUNT(pasienadmisi_t.ruangan_id) AS pasien_keluardipindahkan
            FROM pasienadmisi_t
            LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN masukkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
            WHERE pindahkamar_t.tgl_pindahkamar::DATE = CURRENT_DATE
            AND masukkamar_t.pindahkamar_id IS NOT NULL
            GROUP BY masukkamar_t.ruangan_id, pindahkamar_t.kelaspelayanan_id
            LOOP
            -- get id
            SELECT id, pasien_akhir INTO vid, vpasien_akhir
            FROM sensuspasienranap_r
            WHERE ruangan_id = vruangan_id
            AND kelaspelayanan_id = vkelaspelayanan_id
            AND tgl_sensus = CURRENT_DATE;

            IF (vpasien_keluardipindahkan IS NULL) THEN
            vpasien_keluardipindahkan := 0;
            END IF;

            IF (vpasien_akhir IS NULL) THEN
            vpasien_akhir := 0;
            END IF;

            vpasien_akhir := vpasien_akhir - vpasien_keluardipindahkan;

            -- cek id, kalau ada insert, kalau tidak update
            IF (vid IS NULL) THEN
            -- insert data
            INSERT INTO sensuspasienranap_r (
            ruangan_id,
            kelaspelayanan_id,
            tgl_sensus,
            pasien_keluardipindahkan,
            pasien_akhir
            ) VALUES (
            vruangan_id,
            vkelaspelayanan_id,
            CURRENT_DATE,
            vpasien_keluardipindahkan,
            vpasien_akhir
            );
            ELSE
            -- update data
            UPDATE sensuspasienranap_r SET 
            pasien_keluardipindahkan = vpasien_keluardipindahkan,
            pasien_akhir = vpasien_akhir
            WHERE id = vid;
            END IF;
            END LOOP;

            -- looping untuk pasien keluar meninggal kurang dari 48 jam
            FOR
            vruangan_id,
            vkelaspelayanan_id,
            vpasien_keluarmeninggalkur48
            IN
            -- Original Kang Sigit --
            --      SELECT
            --          pasienadmisi_t.ruangan_id,
            --          pasienadmisi_t.kelaspelayanan_id,
            --          COUNT(pasienadmisi_t.ruangan_id) AS pasien_keluarmeninggalkur48
            --      FROM pasienadmisi_t
            --      LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            --      WHERE pasienpulang_t.tglpasienpulang::DATE = CURRENT_DATE
            --      AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            --      AND pasienpulang_t.carakeluar_id = 4
            --      AND pasienpulang_t.kondisikeluar_id = 7
            --      GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id
            -- Improve by Rido --
            SELECT
            pasienadmisi_t.ruangan_id,
            pasienadmisi_t.kelaspelayanan_id,
            COUNT(pasienadmisi_t.ruangan_id) AS pasien_keluarmeninggalkur48
            FROM pendaftaran_t
            JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = CURRENT_DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id = 4 --meninggal
            AND pasienpulang_t.kondisikeluar_id = 7 --meninggal < 48 jam
            GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id
            LOOP
            -- get id
            SELECT id, pasien_akhir INTO vid, vpasien_akhir
            FROM sensuspasienranap_r
            WHERE ruangan_id = vruangan_id
            AND kelaspelayanan_id = vkelaspelayanan_id
            AND tgl_sensus = CURRENT_DATE;

            IF (vpasien_keluarmeninggalkur48 IS NULL) THEN
            vpasien_keluarmeninggalkur48 := 0;
            END IF;

            IF (vpasien_akhir IS NULL) THEN
            vpasien_akhir := 0;
            END IF;

            vpasien_akhir := vpasien_akhir - vpasien_keluarmeninggalkur48;

            -- cek id, kalau ada insert, kalau tidak update
            IF (vid IS NULL) THEN
            -- insert data
            INSERT INTO sensuspasienranap_r (
            ruangan_id,
            kelaspelayanan_id,
            tgl_sensus,
            pasien_keluarmeninggalkur48,
            pasien_akhir
            ) VALUES (
            vruangan_id,
            vkelaspelayanan_id,
            CURRENT_DATE,
            vpasien_keluarmeninggalkur48,
            vpasien_akhir
            );
            ELSE
            -- update data
            UPDATE sensuspasienranap_r SET 
            pasien_keluarmeninggalkur48 = vpasien_keluarmeninggalkur48,
            pasien_akhir = vpasien_akhir
            WHERE id = vid;
            END IF;
            END LOOP;

            -- looping untuk pasien keluar meninggal lebih dari 48 jam
            FOR
            vruangan_id,
            vkelaspelayanan_id,
            vpasien_keluarmeninggalleb48
            IN
            -- Original Kang Sigit --
            --      SELECT
            --          pasienadmisi_t.ruangan_id,
            --          pasienadmisi_t.kelaspelayanan_id,
            --          COUNT(pasienadmisi_t.ruangan_id) AS pasien_keluarmeninggalleb48
            --      FROM pasienadmisi_t
            --      LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            --      WHERE pasienpulang_t.tglpasienpulang::DATE = CURRENT_DATE
            --      AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            --      AND pasienpulang_t.carakeluar_id = 4
            --      AND pasienpulang_t.kondisikeluar_id = 5
            --      GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id
            -- Improve By Rido --
            SELECT
            pasienadmisi_t.ruangan_id,
            pasienadmisi_t.kelaspelayanan_id,
            COUNT(pasienadmisi_t.ruangan_id) AS pasien_keluarmeninggalleb48
            FROM pendaftaran_t
            JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = CURRENT_DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id = 4 --meninggal
            AND pasienpulang_t.kondisikeluar_id = 5 --meninggal > 48 jam
            GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id
            LOOP
            -- get id
            SELECT id, pasien_akhir INTO vid, vpasien_akhir
            FROM sensuspasienranap_r
            WHERE ruangan_id = vruangan_id
            AND kelaspelayanan_id = vkelaspelayanan_id
            AND tgl_sensus = CURRENT_DATE;

            IF (vpasien_keluarmeninggalleb48 IS NULL) THEN
            vpasien_keluarmeninggalleb48 := 0;
            END IF;

            IF (vpasien_akhir IS NULL) THEN
            vpasien_akhir := 0;
            END IF;

            vpasien_akhir := vpasien_akhir - vpasien_keluarmeninggalleb48;

            -- cek id, kalau ada insert, kalau tidak update
            IF (vid IS NULL) THEN
            -- insert data
            INSERT INTO sensuspasienranap_r (
            ruangan_id,
            kelaspelayanan_id,
            tgl_sensus,
            pasien_keluarmeninggalleb48,
            pasien_akhir
            ) VALUES (
            vruangan_id,
            vkelaspelayanan_id,
            CURRENT_DATE,
            vpasien_keluarmeninggalleb48,
            vpasien_akhir
            );
            ELSE
            -- update data
            UPDATE sensuspasienranap_r SET 
            pasien_keluarmeninggalleb48 = vpasien_keluarmeninggalleb48,
            pasien_akhir = vpasien_akhir
            WHERE id = vid;
            END IF;
            END LOOP;

            -- return
            RETURN NEXT;

            END
            \$BODY\$
            LANGUAGE plpgsql VOLATILE
            COST 100
            ROWS 1000
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210218_101024_migrate_20210218_3362_func_f_cronsensuspasienranap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210218_101024_migrate_20210218_3362_func_f_cronsensuspasienranap cannot be reverted.\n";

        return false;
    }
    */
}
