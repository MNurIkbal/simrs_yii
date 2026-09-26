<?php

use yii\db\Migration;

/**
 * Class m210608_081019_migrate_20210608_hotfix_func_f_getpasien
 */
class m210608_081019_migrate_20210608_hotfix_func_f_getpasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.f_getpasien;
        ");
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"f_getpasien\"(\"xtanggalawal\" date, \"xtanggalakhir\" date, \"xruangan_id\" int4, \"xjeniskelamin\" int4, \"xkunjungan\" varchar, \"xcarabayar_id\" int4, \"xjenisruangan\" int4)
  RETURNS TABLE(\"pasienbaru\" int4) AS \$BODY\$
    
DECLARE
    pasienbaru_pm int4;
    pasienbaru_order int4;
    pasienbaru_order_rj int4;
    
    --vpasien_id int4;
    
BEGIN
-- IF (xjenisruangan = 722 OR xjenisruangan = 724) --Poliklinik dan IGD UMUM
IF (xjenisruangan = 722) --Poliklinik
THEN
    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienbaru
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
    JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
    --LEFT JOIN pendaftaranol_t ON pendaftaran_t.pendaftaran_id = pendaftaranol_t.pendaftaran_id 
    WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
    AND pendaftaran_t.carabayar_id = xcarabayar_id
    AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
    AND pasien_m.jeniskelamin::text = xjeniskelamin::text
    --AND pendaftaranol_t.pendaftaranol_id IS NULL
    AND pendaftaran_t.pasienpulang_id IS NOT NULL;
END IF;

IF (xjenisruangan = 724) -- IGD UMUM RAWAT
THEN
    IF(xruangan_id = 931)
    THEN
        SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienbaru
        FROM pendaftaran_t
        JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
        JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
        JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
        --LEFT JOIN pendaftaranol_t ON pendaftaran_t.pendaftaran_id = pendaftaranol_t.pendaftaran_id 
        WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
        AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
        AND pendaftaran_t.carabayar_id = xcarabayar_id
        AND ruangan_m.ruangan_id::INTEGER = 122
        AND pasien_m.jeniskelamin::text = xjeniskelamin::text
        --AND pendaftaranol_t.pendaftaranol_id IS NULL
        AND pendaftaran_t.pasienpulang_id IS NOT NULL
        AND pasienpulang_t.carakeluar_id::INTEGER = 5; --DI RUJUK RAWAT INAP    
    ELSE IF (xruangan_id = 932) -- IGD UMUM TIDAK RAWAT
    THEN
        SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienbaru
        FROM pendaftaran_t
        JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
        JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
        JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
        --LEFT JOIN pendaftaranol_t ON pendaftaran_t.pendaftaran_id = pendaftaranol_t.pendaftaran_id 
        WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
        AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
        AND pendaftaran_t.carabayar_id = xcarabayar_id
        AND ruangan_m.ruangan_id::INTEGER = 122
        AND pasien_m.jeniskelamin::text = xjeniskelamin::text
        --AND pendaftaranol_t.pendaftaranol_id IS NULL
        AND pendaftaran_t.pasienpulang_id IS NOT NULL
        AND pasienpulang_t.carakeluar_id::INTEGER <> 5;
        END IF;
    END IF;
END IF;

IF (xjenisruangan = 725) --Penunjang Medis
THEN
SELECT COUNT(pasienmasukpenunjang_t.pendaftaran_id) INTO pasienbaru_pm
            FROM pendaftaran_t
            JOIN  pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id   
            JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
            JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
            LEFT JOIN pendaftaranol_t ON pendaftaran_t.pendaftaran_id = pendaftaranol_t.pendaftaran_id 
            WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
            AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
            AND pendaftaran_t.carabayar_id = xcarabayar_id
            AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
            AND pasien_m.jeniskelamin::text = xjeniskelamin::text
                        AND pasienmasukpenunjang_t.is_bayar = TRUE
            AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL;
-- END IF;


-- IF (xjenisruangan = 725) --Penunjang Medis Order dari RI
--  SELECT COUNT(pasienadmisi_t.pasienadmisi_id) INTO pasienbaru_order_ri
--                  --pasienmasukpenunjang_t.pasienmasukpenunjang_id
--  FROM pasienadmisi_t
--  JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
--  JOIN ( SELECT pasienmasukpenunjang_t.pasienadmisi_id,
--                  pasienmasukpenunjang_t.pasien_id,
--                  pasienmasukpenunjang_t.ruangan_id,
--                  pasienmasukpenunjang_t.tglmasukpenunjang,
--                  pasienmasukpenunjang_t.pasienmasukpenunjang_id
--                  FROM pasienmasukpenunjang_t
--                          JOIN ( SELECT pasienmasukpenunjang_t.pasienadmisi_id,
--                                          ruangan_m.instalasi_id,
--                                          MAX(pasienmasukpenunjang_t.pasienmasukpenunjang_id) as pasienmasukpenunjang_id
--                                          FROM pasienmasukpenunjang_t
--                                          JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
--                                          GROUP BY pasienmasukpenunjang_t.pasienadmisi_id,
--                                          ruangan_m.instalasi_id
--                                      ) max_penunjang ON pasienmasukpenunjang_t.pasienadmisi_id = max_penunjang.pasienadmisi_id 
--                                      AND pasienmasukpenunjang_t.pasienmasukpenunjang_id = max_penunjang.pasienmasukpenunjang_id
--          ) pasienmasukpenunjang_t ON pasienadmisi_t.pasienadmisi_id = pasienmasukpenunjang_t.pasienadmisi_id 
--  JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
--  JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
--  JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
--  WHERE pasienmasukpenunjang_t.tglmasukpenunjang::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
--  AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
--  AND pasienadmisi_t.carabayar_id = xcarabayar_id
--  AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
--  AND pasien_m.jeniskelamin::text = xjeniskelamin::text
--  AND pendaftaran_t.is_aps = FALSE
--  AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL
--  GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id;
    
    -- IF (xjenisruangan = 725) --Penunjang Medis Order dari RJ RD baru comment

--  SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienbaru_order_ri
--  FROM pendaftaran_t
--  --JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
--  JOIN ( SELECT pasienmasukpenunjang_t.pendaftaran_id,
--                  pasienmasukpenunjang_t.pasien_id,
--                  pasienmasukpenunjang_t.ruangan_id,
--                  pasienmasukpenunjang_t.tglmasukpenunjang,
--                  pasienmasukpenunjang_t.pasienmasukpenunjang_id
--                  FROM pasienmasukpenunjang_t
--                          JOIN ( SELECT pasienmasukpenunjang_t.pendaftaran_id,
--                                          ruangan_m.instalasi_id,
--                                          MAX(pasienmasukpenunjang_t.pasienmasukpenunjang_id) as pasienmasukpenunjang_id
--                                          FROM pasienmasukpenunjang_t
--                                          JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
--                                          --WHERE pasienmasukpenunjang_t.kunjungan <> 181
--                                          GROUP BY pasienmasukpenunjang_t.pendaftaran_id, ruangan_m.instalasi_id
--                                      ) max_penunjang ON pasienmasukpenunjang_t.pendaftaran_id = max_penunjang.pendaftaran_id 
--                                      AND pasienmasukpenunjang_t.pasienmasukpenunjang_id = max_penunjang.pasienmasukpenunjang_id
--          ) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id 
--  JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
--  JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
--  WHERE pasienmasukpenunjang_t.tglmasukpenunjang::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
--  AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
--  AND pendaftaran_t.carabayar_id = xcarabayar_id
--  AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
--  AND pasien_m.jeniskelamin::text = xjeniskelamin::text
--  AND pendaftaran_t.is_aps = FALSE
--  AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL;
    

-- IF (xjenisruangan = 725) --Penunjang Medis Order dari RI

    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienbaru_order
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
    JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
    WHERE pasienmasukpenunjang_t.tglmasukpenunjang::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
    AND pendaftaran_t.carabayar_id = xcarabayar_id
    AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
    AND pasien_m.jeniskelamin::text = xjeniskelamin::text
    AND pendaftaran_t.is_aps = FALSE
    AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL
    AND pendaftaran_t.pasienadmisi_id IS NULL;
    
    -- IF (xjenisruangan = 725) --Penunjang Medis Order dari RJ RD

    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienbaru_order_rj
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN (SELECT
            pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        pasienmasukpenunjang_t.tglmasukpenunjang
        FROM pasienmasukpenunjang_t
        JOIN (SELECT 
                  max(pasienmasukpenunjang_t.pasienmasukpenunjang_id) as max_id,
                  pasienmasukpenunjang_t.pendaftaran_id,
                                    pasienmasukpenunjang_t.ruangan_id
              FROM pasienmasukpenunjang_t
              GROUP BY pasienmasukpenunjang_t.pendaftaran_id,
                        pasienmasukpenunjang_t.ruangan_id) max_penunjang ON pasienmasukpenunjang_t.pendaftaran_id = max_penunjang.pendaftaran_id 
                        and pasienmasukpenunjang_t.pasienmasukpenunjang_id = max_penunjang.max_id
                        and pasienmasukpenunjang_t.ruangan_id = max_penunjang.ruangan_id
        ) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
    JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
    WHERE pasienmasukpenunjang_t.tglmasukpenunjang::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
    AND pendaftaran_t.carabayar_id = xcarabayar_id
    AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
    AND pasien_m.jeniskelamin::text = xjeniskelamin::text
    AND pendaftaran_t.is_aps = FALSE
    AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL
    AND pendaftaran_t.pasienadmisi_id IS NOT NULL;
    
--  -- IF (xjenisruangan = 725) --Penunjang Medis Order dari RI

--  SELECT COUNT(pasienadmisi_t.pendaftaran_id) INTO pasienbaru_order_ri
--  FROM pendaftaran_t
--  JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
--  JOIN pasienmasukpenunjang_t ON pasienadmisi_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
--  JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
--  JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
--  WHERE pasienmasukpenunjang_t.tglmasukpenunjang::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
--  AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
--  AND pasienadmisi_t.carabayar_id = xcarabayar_id
--  AND pasienadmisi_t.ruangan_id::INTEGER = xruangan_id::INTEGER
--  AND pasien_m.jeniskelamin::text = xjeniskelamin::text
--  AND pendaftaran_t.is_aps = FALSE
--  AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL;
    
    pasienbaru := COALESCE(pasienbaru_pm, 0) + COALESCE(pasienbaru_order, 0) + COALESCE(pasienbaru_order_rj, 0);
END IF;

IF (xjenisruangan = 723) --MCU
THEN
    SELECT COUNT(pendaftaran_t.pendaftaran_id) INTO pasienbaru
    FROM pendaftaran_t
    JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
    JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
    LEFT JOIN pendaftaranol_t ON pendaftaran_t.pendaftaran_id = pendaftaranol_t.pendaftaran_id 
    WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
    AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
    AND pendaftaran_t.carabayar_id = xcarabayar_id
    AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
    AND pendaftaranol_t.pendaftaranol_id IS NULL
    AND pasien_m.jeniskelamin::text = xjeniskelamin::text;
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
        echo "m210608_081019_migrate_20210608_hotfix_func_f_getpasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210608_081019_migrate_20210608_hotfix_func_f_getpasien cannot be reverted.\n";

        return false;
    }
    */
}
