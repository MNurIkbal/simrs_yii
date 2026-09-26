<?php

use yii\db\Migration;

/**
 * Class m210302_094312_migrate_20210302_3422_func_f_getpasienmasukigdtindaklanjut_melarikandiri
 */
class m210302_094312_migrate_20210302_3422_func_f_getpasienmasukigdtindaklanjut_melarikandiri extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.f_getpasienmasukigdtindaklanjut_melarikandiri;
        ");
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"f_getpasienmasukigdtindaklanjut_melarikandiri\"(\"xtanggalawal\" date, \"xtanggalakhir\" date, \"xjenispelayanan\" varchar, \"xcarakeluar\" int4)
            RETURNS TABLE(\"pasienmasukigd\" int4) AS \$BODY\$

            DECLARE
            --  pasienmasukigd int4;

            BEGIN

            IF (xjenispelayanan = 'non_trauma' AND xcarakeluar = 7) --Non Trauma
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            LEFT JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND pasienpulang_t.carakeluar_id = 7
            AND triase_t.trauma = 'non_trauma'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'trauma' AND xcarakeluar = 7) --Trauma
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            LEFT JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND pasienpulang_t.carakeluar_id = 7
            AND triase_t.trauma = 'trauma'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'kebidanan' AND xcarakeluar = 7) --Kebidanan
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            LEFT JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND pasienpulang_t.carakeluar_id = 7
            AND triase_t.trauma = 'kebidanan'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'non_bedah' AND xcarakeluar = 7) --Non Bedah
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            LEFT JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND pasienpulang_t.carakeluar_id = 7
            AND triase_t.trauma = 'non_bedah'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'psikiatri' AND xcarakeluar = 7) --Psikiatri
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            LEFT JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND pasienpulang_t.carakeluar_id = 7
            AND triase_t.trauma = 'psikiatri'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'anak' AND xcarakeluar = 7) --Anak
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            LEFT JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND pasienpulang_t.carakeluar_id = 7
            AND triase_t.trauma = 'anak'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'bedah_trauma_kll' AND xcarakeluar = 7) --Bedah Trauma Kll
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            LEFT JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND pasienpulang_t.carakeluar_id = 7
            AND triase_t.trauma = 'bedah_trauma_kll'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'bedah_trauma_non_kll' AND xcarakeluar = 7) --Bedah Trauma Non Kll
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            LEFT JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND pasienpulang_t.carakeluar_id = 7
            AND triase_t.trauma = 'bedah_trauma_non_kll'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'bedah_non_trauma' AND xcarakeluar = 7) --Bedah Non Trauma 
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            LEFT JOIN pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND pasienpulang_t.carakeluar_id = 7
            AND triase_t.trauma = 'bedah_non_trauma'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
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
        echo "m210302_094312_migrate_20210302_3422_func_f_getpasienmasukigdtindaklanjut_melarikandiri cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210302_094312_migrate_20210302_3422_func_f_getpasienmasukigdtindaklanjut_melarikandiri cannot be reverted.\n";

        return false;
    }
    */
}
