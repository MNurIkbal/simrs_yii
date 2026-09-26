<?php

use yii\db\Migration;

/**
 * Class m210302_100545_migrate_20210302_3422_func_f_getpasienmasukigdtriaseemergent
 */
class m210302_100545_migrate_20210302_3422_func_f_getpasienmasukigdtriaseemergent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.f_getpasienmasukigdtriaseemergent;
        ");
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"f_getpasienmasukigdtriaseemergent\"(\"xtanggalawal\" date, \"xtanggalakhir\" date, \"xjenispelayanan\" varchar, \"xhasiltriase\" varchar)
            RETURNS TABLE(\"pasienmasukigd\" int4) AS \$BODY\$

            DECLARE
            --  pasienmasukigd int4;

            BEGIN

            IF (xjenispelayanan = 'non_trauma' AND xhasiltriase = 'emergent') --Non Trauma
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd 
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND triase_t.trauma = 'non_trauma'
            AND triase_t.hasil_triase = 'emergent'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'trauma' AND xhasiltriase = 'emergent') --Trauma
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND triase_t.trauma = 'trauma'
            AND triase_t.hasil_triase = 'emergent'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'kebidanan' AND xhasiltriase = 'emergent') --Kebidanan
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND triase_t.trauma = 'kebidanan'
            AND triase_t.hasil_triase = 'emergent'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'non_bedah' AND xhasiltriase = 'emergent') --Non Bedah
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND triase_t.trauma = 'non_bedah'
            AND triase_t.hasil_triase = 'emergent'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'psikiatri' AND xhasiltriase = 'emergent') --Psikiatri
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND triase_t.trauma = 'psikiatri'
            AND triase_t.hasil_triase = 'emergent'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'anak' AND xhasiltriase = 'emergent') --Anak
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND triase_t.trauma = 'anak'
            AND triase_t.hasil_triase = 'emergent'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'bedah_trauma_kll' AND xhasiltriase = 'emergent') --Bedah Trauma Kll
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND triase_t.trauma = 'bedah_trauma_kll'
            AND triase_t.hasil_triase = 'emergent'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'bedah_trauma_non_kll' AND xhasiltriase = 'emergent') --Bedah Trauma Non Kll
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND triase_t.trauma = 'bedah_trauma_non_kll'
            AND triase_t.hasil_triase = 'emergent'
            AND pendaftaran_t.instalasi_id = 2
            GROUP BY pasien_m.pasien_id;
            END IF;

            IF (xjenispelayanan = 'bedah_non_trauma' AND xhasiltriase = 'emergent') --Bedah Non Trauma 
            THEN
            SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
            FROM pendaftaran_t
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND triase_t.trauma = 'bedah_non_trauma'
            AND triase_t.hasil_triase = 'emergent'
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
        echo "m210302_100545_migrate_20210302_3422_func_f_getpasienmasukigdtriaseemergent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210302_100545_migrate_20210302_3422_func_f_getpasienmasukigdtriaseemergent cannot be reverted.\n";

        return false;
    }
    */
}
