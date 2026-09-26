<?php

use yii\db\Migration;

/**
 * Class m210127_075020_migrate_20210127_3073_func_f_cronsensuspasienranap
 */
class m210127_075020_migrate_20210127_3073_func_f_cronsensuspasienranap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
            SELECT
            ruangan_id,
            kelaspelayanan_id,
            COUNT(ruangan_id) AS pasien_masuk
            FROM pasienadmisi_t
            WHERE tgl_admisi::DATE = CURRENT_DATE
            GROUP BY ruangan_id, kelaspelayanan_id
            LOOP
            -- get pasien akhir dengan ruangan dan kelas yang ada di hari sebelumnya
            SELECT
            pasien_akhir INTO vpasien_awal
            FROM sensuspasienranap_r
            WHERE ruangan_id = vruangan_id
            AND kelaspelayanan_id = vkelaspelayanan_id
            AND tgl_sensus = CURRENT_DATE - 1;

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
            SELECT
            pasienadmisi_t.ruangan_id,
            pasienadmisi_t.kelaspelayanan_id,
            COUNT(pasienadmisi_t.ruangan_id) AS pasien_pindahan
            FROM pasienadmisi_t
            LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pindahkamar_t.tgl_pindahkamar::DATE = CURRENT_DATE
            GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id
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
            SELECT
            pasienadmisi_t.ruangan_id,
            pasienadmisi_t.kelaspelayanan_id,
            COUNT(pasienadmisi_t.ruangan_id) AS pasien_keluarhidup
            FROM pasienadmisi_t
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = CURRENT_DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id IN (1,2,3,5,6,7)
            GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id
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
            SELECT
            pasienadmisi_t.ruangan_id,
            pasienadmisi_t.kelaspelayanan_id,
            COUNT(pasienadmisi_t.ruangan_id) AS pasien_keluardipindahkan
            FROM pasienadmisi_t
            LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN masukkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
            WHERE pindahkamar_t.tgl_pindahkamar::DATE = CURRENT_DATE
            AND masukkamar_t.pindahkamar_id IS NOT NULL
            GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id
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
            SELECT
            pasienadmisi_t.ruangan_id,
            pasienadmisi_t.kelaspelayanan_id,
            COUNT(pasienadmisi_t.ruangan_id) AS pasien_keluarmeninggalkur48
            FROM pasienadmisi_t
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = CURRENT_DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id = 4
            AND pasienpulang_t.kondisikeluar_id = 7
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
            SELECT
            pasienadmisi_t.ruangan_id,
            pasienadmisi_t.kelaspelayanan_id,
            COUNT(pasienadmisi_t.ruangan_id) AS pasien_keluarmeninggalleb48
            FROM pasienadmisi_t
            LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pasienpulang_t.tglpasienpulang::DATE = CURRENT_DATE
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id = 4
            AND pasienpulang_t.kondisikeluar_id = 5
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
        echo "m210127_075020_migrate_20210127_3073_func_f_cronsensuspasienranap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_075020_migrate_20210127_3073_func_f_cronsensuspasienranap cannot be reverted.\n";

        return false;
    }
    */
}
