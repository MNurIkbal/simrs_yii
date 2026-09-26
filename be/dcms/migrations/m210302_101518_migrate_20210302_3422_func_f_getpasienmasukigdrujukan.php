<?php

use yii\db\Migration;

/**
 * Class m210302_101518_migrate_20210302_3422_func_f_getpasienmasukigdrujukan
 */
class m210302_101518_migrate_20210302_3422_func_f_getpasienmasukigdrujukan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.f_getpasienmasukigdrujukan;
        ");
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"f_getpasienmasukigdrujukan\"(\"xtanggalawal\" date, \"xtanggalakhir\" date, \"xjenispelayanan\" varchar, \"xrujukan\" int4)
  RETURNS TABLE(\"pasienmasukigd\" int4) AS \$BODY\$
    
DECLARE
--  pasienmasukigd int4;
    
BEGIN

IF (xjenispelayanan = 'non_trauma' AND xrujukan IS NOT NULL) --Non Trauma
THEN
    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd 
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
--  JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
    JOIN ( SELECT triase_t.pendaftaran_id,
                        triase_t.trauma
           FROM (triase_t
             JOIN ( SELECT max(pk.triase_id) AS triase_id,
                    pk.pendaftaran_id
                   FROM triase_t pk
                  WHERE (pk.is_deleted = false)
                  GROUP BY pk.pendaftaran_id) max_pk ON (((triase_t.triase_id = max_pk.triase_id) AND (triase_t.pendaftaran_id = max_pk.pendaftaran_id))))) triase_t ON ((pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id))
    WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND triase_t.trauma = 'non_trauma'
    AND pendaftaran_t.instalasi_id = 2
    AND pendaftaran_t.rujukan_id IS NOT NULL
    GROUP BY pasien_m.pasien_id;
END IF;

IF (xjenispelayanan = 'trauma' AND xrujukan IS NOT NULL) --Trauma
THEN
    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
--  JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
    JOIN ( SELECT triase_t.pendaftaran_id,
                        triase_t.trauma
           FROM (triase_t
             JOIN ( SELECT max(pk.triase_id) AS triase_id,
                    pk.pendaftaran_id
                   FROM triase_t pk
                  WHERE (pk.is_deleted = false)
                  GROUP BY pk.pendaftaran_id) max_pk ON (((triase_t.triase_id = max_pk.triase_id) AND (triase_t.pendaftaran_id = max_pk.pendaftaran_id))))) triase_t ON ((pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id))
    WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND triase_t.trauma = 'trauma'
    AND pendaftaran_t.instalasi_id = 2
    AND pendaftaran_t.rujukan_id IS NOT NULL
    GROUP BY pasien_m.pasien_id;
END IF;

IF (xjenispelayanan = 'kebidanan' AND xrujukan IS NOT NULL) --Kebidanan
THEN
    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
--  JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
    JOIN ( SELECT triase_t.pendaftaran_id,
                        triase_t.trauma
           FROM (triase_t
             JOIN ( SELECT max(pk.triase_id) AS triase_id,
                    pk.pendaftaran_id
                   FROM triase_t pk
                  WHERE (pk.is_deleted = false)
                  GROUP BY pk.pendaftaran_id) max_pk ON (((triase_t.triase_id = max_pk.triase_id) AND (triase_t.pendaftaran_id = max_pk.pendaftaran_id))))) triase_t ON ((pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id))
    WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND triase_t.trauma = 'kebidanan'
    AND pendaftaran_t.instalasi_id = 2
    AND pendaftaran_t.rujukan_id IS NOT NULL
    GROUP BY pasien_m.pasien_id;
END IF;

IF (xjenispelayanan = 'non_bedah' AND xrujukan IS NOT NULL) --Non Bedah
THEN
    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
--  JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
    JOIN ( SELECT triase_t.pendaftaran_id,
                        triase_t.trauma
           FROM (triase_t
             JOIN ( SELECT max(pk.triase_id) AS triase_id,
                    pk.pendaftaran_id
                   FROM triase_t pk
                  WHERE (pk.is_deleted = false)
                  GROUP BY pk.pendaftaran_id) max_pk ON (((triase_t.triase_id = max_pk.triase_id) AND (triase_t.pendaftaran_id = max_pk.pendaftaran_id))))) triase_t ON ((pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id))
    WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND triase_t.trauma = 'non_bedah'
    AND pendaftaran_t.instalasi_id = 2
    AND pendaftaran_t.rujukan_id IS NOT NULL
    GROUP BY pasien_m.pasien_id;
END IF;

IF (xjenispelayanan = 'psikiatri' AND xrujukan IS NOT NULL) --Psikiatri
THEN
    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
--  JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
    JOIN ( SELECT triase_t.pendaftaran_id,
                        triase_t.trauma
           FROM (triase_t
             JOIN ( SELECT max(pk.triase_id) AS triase_id,
                    pk.pendaftaran_id
                   FROM triase_t pk
                  WHERE (pk.is_deleted = false)
                  GROUP BY pk.pendaftaran_id) max_pk ON (((triase_t.triase_id = max_pk.triase_id) AND (triase_t.pendaftaran_id = max_pk.pendaftaran_id))))) triase_t ON ((pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id))
    WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND triase_t.trauma = 'psikiatri'
    AND pendaftaran_t.instalasi_id = 2
    AND pendaftaran_t.rujukan_id IS NOT NULL
    GROUP BY pasien_m.pasien_id;
END IF;

IF (xjenispelayanan = 'anak' AND xrujukan IS NOT NULL) --Anak
THEN
    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
--  JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
    JOIN ( SELECT triase_t.pendaftaran_id,
                        triase_t.trauma
           FROM (triase_t
             JOIN ( SELECT max(pk.triase_id) AS triase_id,
                    pk.pendaftaran_id
                   FROM triase_t pk
                  WHERE (pk.is_deleted = false)
                  GROUP BY pk.pendaftaran_id) max_pk ON (((triase_t.triase_id = max_pk.triase_id) AND (triase_t.pendaftaran_id = max_pk.pendaftaran_id))))) triase_t ON ((pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id))
    WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND triase_t.trauma = 'anak'
    AND pendaftaran_t.instalasi_id = 2
    AND pendaftaran_t.rujukan_id IS NOT NULL
    GROUP BY pasien_m.pasien_id;
END IF;

IF (xjenispelayanan = 'bedah_trauma_kll' AND xrujukan IS NOT NULL) --Bedah Trauma Kll
THEN
    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
--  JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
    JOIN ( SELECT triase_t.pendaftaran_id,
                        triase_t.trauma
           FROM (triase_t
             JOIN ( SELECT max(pk.triase_id) AS triase_id,
                    pk.pendaftaran_id
                   FROM triase_t pk
                  WHERE (pk.is_deleted = false)
                  GROUP BY pk.pendaftaran_id) max_pk ON (((triase_t.triase_id = max_pk.triase_id) AND (triase_t.pendaftaran_id = max_pk.pendaftaran_id))))) triase_t ON ((pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id))
    WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND triase_t.trauma = 'bedah_trauma_kll'
    AND pendaftaran_t.instalasi_id = 2
    AND pendaftaran_t.rujukan_id IS NOT NULL
    GROUP BY pasien_m.pasien_id;
END IF;

IF (xjenispelayanan = 'bedah_trauma_non_kll' AND xrujukan IS NOT NULL) --Bedah Trauma Non Kll
THEN
    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
--  JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
    JOIN ( SELECT triase_t.pendaftaran_id,
                        triase_t.trauma
           FROM (triase_t
             JOIN ( SELECT max(pk.triase_id) AS triase_id,
                    pk.pendaftaran_id
                   FROM triase_t pk
                  WHERE (pk.is_deleted = false)
                  GROUP BY pk.pendaftaran_id) max_pk ON (((triase_t.triase_id = max_pk.triase_id) AND (triase_t.pendaftaran_id = max_pk.pendaftaran_id))))) triase_t ON ((pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id))
    WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND triase_t.trauma = 'bedah_trauma_non_kll'
    AND pendaftaran_t.instalasi_id = 2
    AND pendaftaran_t.rujukan_id IS NOT NULL
    GROUP BY pasien_m.pasien_id;
END IF;

IF (xjenispelayanan = 'bedah_non_trauma' AND xrujukan IS NOT NULL) --Bedah Non Trauma 
THEN
    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienmasukigd
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
--  JOIN triase_t ON pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id
    JOIN ( SELECT triase_t.pendaftaran_id,
                        triase_t.trauma
           FROM (triase_t
             JOIN ( SELECT max(pk.triase_id) AS triase_id,
                    pk.pendaftaran_id
                   FROM triase_t pk
                  WHERE (pk.is_deleted = false)
                  GROUP BY pk.pendaftaran_id) max_pk ON (((triase_t.triase_id = max_pk.triase_id) AND (triase_t.pendaftaran_id = max_pk.pendaftaran_id))))) triase_t ON ((pendaftaran_t.pendaftaran_id = triase_t.pendaftaran_id))
    WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND triase_t.trauma = 'bedah_non_trauma'
    AND pendaftaran_t.instalasi_id = 2
    AND pendaftaran_t.rujukan_id IS NOT NULL
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
        echo "m210302_101518_migrate_20210302_3422_func_f_getpasienmasukigdrujukan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210302_101518_migrate_20210302_3422_func_f_getpasienmasukigdrujukan cannot be reverted.\n";

        return false;
    }
    */
}
