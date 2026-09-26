<?php

use yii\db\Migration;

/**
 * Class m230207_121320_migrate_GB822_f_getpasien
 */
class m230207_121320_migrate_GB822_f_getpasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION public.f_getpasien(xtanggalawal date, xtanggalakhir date, xruangan_id integer, xjeniskelamin integer, xkunjungan character varying, xcarabayar_id integer, xjenisruangan integer)
        RETURNS TABLE(pasienbaru integer)
        LANGUAGE plpgsql
        IMMUTABLE
       AS \$function\$
       
       DECLARE 
       pasienbaru_pm int4;
       pasienbaru_order int4;
       pasienbaru_order_rj int4;
       pasienbaru_order_mcu int4;
       
       BEGIN 
       IF (xjenisruangan = 722) --Poliklinik
       THEN
           SELECT 
               COUNT(*) INTO pasienbaru
           FROM (
               SELECT 
                   pendaftaran_t.pendaftaran_id
               FROM pendaftaran_t
                   JOIN (SELECT 
                               a.pasien_id,
                               a.jeniskelamin
                           FROM pasien_m a) pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
                   JOIN (SELECT 
                               a.ruangan_id
                           FROM ruangan_m a) ruangan_m on pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                   LEFT JOIN (SELECT 
                                   a.pasienpulang_id
                               FROM pasienpulang_t a) pasienpulang_t on pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
               WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
               AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
               AND pendaftaran_t.carabayar_id = xcarabayar_id
               AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
               AND pasien_m.jeniskelamin::TEXT = xjeniskelamin::TEXT
               AND pendaftaran_t.status_periksa::INTEGER NOT IN (402, 628)
               AND pendaftaran_t.pendaftaran_id NOT IN (
                   SELECT 
                       pendaftaran_t.pendaftaran_id
                   FROM pendaftaranol_t
                       JOIN (
                           SELECT 
                               a.pendaftaran_id
                           FROM pendaftaran_t a
                           WHERE a.tgl_pendaftaran::date BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE) pendaftaran_t on pendaftaranol_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)
               UNION ALL
               SELECT 
                   pendaftaran_t.pendaftaran_id
               FROM pendaftaran_t
                   JOIN (SELECT
                               a.pendaftaran_id,
                               a.tgl_konsulpoli,
                               a.ruangan_id
                           FROM konsulpoli_t a) konsulpoli_t on pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id
                   JOIN (SELECT 
                               a.pasien_id,
                               a.jeniskelamin
                           FROM pasien_m a) pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
                   JOIN (SELECT 
                               a.ruangan_id
                           FROM ruangan_m a) ruangan_m on pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                   LEFT JOIN (SELECT 
                                   a.pasienpulang_id
                               FROM pasienpulang_t a) pasienpulang_t on pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
               WHERE konsulpoli_t.tgl_konsulpoli::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
               AND pendaftaran_t.ruangan_id NOT IN (
                   SELECT 
                       kode_id
                   FROM lookuptransaksi_m
                   WHERE kode_transaksi = 'ruangan_mcu_rj')
               AND pendaftaran_t.instalasi_id = 1
               AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
               AND pendaftaran_t.carabayar_id = xcarabayar_id
               AND konsulpoli_t.ruangan_id::INTEGER = xruangan_id::INTEGER
               AND pasien_m.jeniskelamin::TEXT = xjeniskelamin::TEXT
               AND pendaftaran_t.status_periksa::INTEGER NOT IN (402, 628)
               ) as x;
       END IF;
       
       IF (xjenisruangan = 724) -- IGD UMUM RAWAT
       THEN 
           IF(xruangan_id = 931) 
           THEN
               SELECT 
                   COUNT(pendaftaran_t.pendaftaran_id) INTO pasienbaru
               FROM pendaftaran_t
               JOIN (SELECT 
                           a.pasien_id,
                           a.jeniskelamin
                       FROM pasien_m a) pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
               JOIN (SELECT 
                           a.ruangan_id
                       FROM ruangan_m a) ruangan_m on pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
               JOIN (SELECT 
                           a.pasienpulang_id,
                           a.carakeluar_id
                       FROM pasienpulang_t a) pasienpulang_t on pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
               WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
               AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
               AND pendaftaran_t.carabayar_id = xcarabayar_id
               AND ruangan_m.ruangan_id::INTEGER = 122
               AND pasien_m.jeniskelamin::TEXT = xjeniskelamin::TEXT
               AND pendaftaran_t.pasienpulang_id IS NOT NULL
               AND pendaftaran_t.pendaftaran_id NOT IN (
                   SELECT 
                       pendaftaranol_t.pendaftaran_id
                   FROM pendaftaranol_t
                   JOIN (SELECT
                               a.pendaftaran_id
                           FROM pendaftaran_t a
                           WHERE a.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE) pendaftaran_t on pendaftaranol_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)
               AND pasienpulang_t.carakeluar_id::INTEGER = 5;
       --DI RUJUK RAWAT INAP    
           ELSE IF (xruangan_id = 932) -- IGD UMUM TIDAK RAWAT
           THEN
               SELECT 
                   COUNT(pendaftaran_t.pendaftaran_id) INTO pasienbaru
               FROM pendaftaran_t
               JOIN (SELECT 
                           a.pasien_id,
                           a.jeniskelamin
                       FROM pasien_m a) pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
               JOIN (SELECT 
                           a.ruangan_id
                       FROM ruangan_m a) ruangan_m on pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
               JOIN (SELECT 
                           a.pasienpulang_id,
                           a.carakeluar_id
                       FROM pasienpulang_t a) pasienpulang_t on pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
               WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
               AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
               AND pendaftaran_t.carabayar_id = xcarabayar_id
               AND ruangan_m.ruangan_id::INTEGER = 122
               AND pasien_m.jeniskelamin::TEXT = xjeniskelamin::TEXT
               AND pendaftaran_t.pendaftaran_id NOT IN (
                   SELECT 
                       pendaftaranol_t.pendaftaran_id
                   FROM pendaftaranol_t
                   JOIN (SELECT
                               a.pendaftaran_id
                           FROM pendaftaran_t a
                           WHERE a.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE) pendaftaran_t on pendaftaranol_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)
               AND pendaftaran_t.pasienpulang_id IS NOT NULL
               AND pasienpulang_t.carakeluar_id::INTEGER <> 5;
           END IF;
           END IF;
       END IF;
       
       IF (xjenisruangan = 725) --Penunjang Medis
       THEN
           SELECT 
               COUNT(pasienmasukpenunjang_t.pendaftaran_id) INTO pasienbaru_pm
           FROM pendaftaran_t
           JOIN (SELECT 
                       a.pendaftaran_id,
                       a.is_bayar,
                       a.pasienmasukpenunjang_id,
                       a.status_periksa
                   FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t on pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
           JOIN (SELECT 
                       a.pasien_id,
                       a.jeniskelamin
                   FROM pasien_m a) pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
           JOIN (SELECT 
                       a.ruangan_id
                   FROM ruangan_m a) ruangan_m on pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
           WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
           AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
           AND pendaftaran_t.carabayar_id = xcarabayar_id
           AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
           AND pasien_m.jeniskelamin::TEXT = xjeniskelamin::TEXT
           AND pasienmasukpenunjang_t.is_bayar = true
           AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL
           AND pasienmasukpenunjang_t.status_periksa::INTEGER NOT IN (476,477);
       
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
           --  AND pasien_m.jeniskelamin::TEXT = xjeniskelamin::TEXT
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
           --  AND pasien_m.jeniskelamin::TEXT = xjeniskelamin::TEXT
           --  AND pendaftaran_t.is_aps = FALSE
           --  AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL;
       
           --Penunjang Medis Order dari RI
           SELECT 
               COUNT(pendaftaran_t.pendaftaran_id) INTO pasienbaru_order
           FROM pendaftaran_t
           JOIN (SELECT 
                       a.pasien_id,
                       a.jeniskelamin
                   FROM pasien_m a) pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
           JOIN (SELECT 
                       a.pendaftaran_id,
                       a.ruangan_id,
                       a.tglmasukpenunjang,
                       a.pasienmasukpenunjang_id,
                       a.status_periksa
                   FROM pasienmasukpenunjang_t a
                   JOIN (SELECT 
                               max(b.pasienmasukpenunjang_id) as max_penunjang_id,
                               b.pendaftaran_id,
                               b.ruangan_id
                           FROM pasienmasukpenunjang_t b
                           WHERE b.status_periksa::INTEGER NOT IN (476,477)
                           AND b.pasienadmisi_id IS NOT NULL
                           GROUP BY b.pendaftaran_id,b.ruangan_id,b.tglmasukpenunjang::date) max_penunjang on a.pendaftaran_id = max_penunjang.pendaftaran_id AND a.pasienmasukpenunjang_id = max_penunjang.max_penunjang_id AND a.ruangan_id = max_penunjang.ruangan_id) pasienmasukpenunjang_t on pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
           JOIN (SELECT 
                       a.ruangan_id
                   FROM ruangan_m a) ruangan_m on pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
           WHERE pasienmasukpenunjang_t.tglmasukpenunjang::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
           AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
           AND pendaftaran_t.carabayar_id = xcarabayar_id
           AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
           AND pasien_m.jeniskelamin::TEXT = xjeniskelamin::TEXT
           AND pendaftaran_t.is_aps = false
           AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL;
       
           --Penunjang Medis Order dari RJ RD
           SELECT 
               COUNT(pendaftaran_t.pendaftaran_id) INTO pasienbaru_order_rj 
           FROM pendaftaran_t
           JOIN (SELECT 
                       a.pasien_id,
                       a.jeniskelamin
                   FROM pasien_m a) pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
           JOIN (SELECT 
                       a.pendaftaran_id,
                       a.ruangan_id,
                       a.pasienmasukpenunjang_id,
                       a.tglmasukpenunjang,
                       a.status_periksa
                   FROM pasienmasukpenunjang_t a
                   JOIN (SELECT 
                               max(b.pasienmasukpenunjang_id) as max_id,
                               b.pendaftaran_id,
                               b.ruangan_id
                           FROM pasienmasukpenunjang_t b
                           WHERE b.status_periksa::INTEGER NOT IN (476,477)
                           AND b.pasienadmisi_id IS NULL
                           GROUP BY b.pendaftaran_id,b.ruangan_id,b.tglmasukpenunjang::date) max_penunjang on a.pendaftaran_id = max_penunjang.pendaftaran_id AND a.pasienmasukpenunjang_id = max_penunjang.max_id AND a.ruangan_id = max_penunjang.ruangan_id) pasienmasukpenunjang_t on pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
           JOIN (SELECT 
                       a.ruangan_id
                   FROM ruangan_m a) ruangan_m on pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
           WHERE pasienmasukpenunjang_t.tglmasukpenunjang::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
           AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
           AND pendaftaran_t.carabayar_id = xcarabayar_id
           AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
           AND pasien_m.jeniskelamin::TEXT = xjeniskelamin::TEXT
           AND pendaftaran_t.is_aps = false
           AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL;
       
           --Penunjang Medis Order dari MCU
           SELECT 
               count(pasienmasukpenunjang_t.pasienmasukpenunjang_id) INTO pasienbaru_order_mcu
           FROM pendaftaran_t
           JOIN (SELECT 
                       a.pasien_id,
                       a.jeniskelamin
                   FROM pasien_m a) pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
           JOIN (SELECT 
                       a.pasienmasukpenunjang_id,
                       a.pendaftaran_id,
                       a.tglmasukpenunjang,
                       a.ruangan_id,
                       a.status_periksa
                   FROM pasienmasukpenunjang_t a
                   JOIN (SELECT 
                               max(b.pasienmasukpenunjang_id) as max_penunjang_id,
                               b.pendaftaran_id,
                               b.ruangan_id
                           FROM pasienmasukpenunjang_t b
                           WHERE b.status_periksa::INTEGER NOT IN (476,477)
                           GROUP BY b.pendaftaran_id,b.ruangan_id,b.tglmasukpenunjang::date) max_penunjang on a.pendaftaran_id = max_penunjang.pendaftaran_id AND a.pasienmasukpenunjang_id = max_penunjang.max_penunjang_id AND a.ruangan_id = max_penunjang.ruangan_id) pasienmasukpenunjang_t on pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
           JOIN (SELECT 
                       a.ruangan_id
                   FROM ruangan_m a) ruangan_m on pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
           WHERE pasienmasukpenunjang_t.tglmasukpenunjang::date BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
           AND pendaftaran_t.instalasi_id = 21
           AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
           AND pendaftaran_t.carabayar_id = xcarabayar_id
           AND pasien_m.jeniskelamin::TEXT = xjeniskelamin::TEXT
           AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
           AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL;
       
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
           --  AND pasien_m.jeniskelamin::TEXT = xjeniskelamin::TEXT
           --  AND pendaftaran_t.is_aps = FALSE
           --  AND pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NOT NULL;
           
       pasienbaru := coalesce(pasienbaru_pm, 0) + coalesce(pasienbaru_order, 0) + coalesce(pasienbaru_order_rj, 0) + coalesce(pasienbaru_order_mcu, 0);
       END IF;
       
       IF (xjenisruangan = 723) --MCU
       THEN
           SELECT 
               COUNT(pendaftaran_t.pendaftaran_id) INTO pasienbaru
           FROM pendaftaran_t
           JOIN (SELECT 
                       a.pasien_id,
                       a.jeniskelamin
                   FROM pasien_m a) pasien_m on pendaftaran_t.pasien_id = pasien_m.pasien_id
           JOIN (SELECT 
                       a.ruangan_id
                   FROM ruangan_m a) ruangan_m on pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
           WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE
           AND pendaftaran_t.kunjungan::VARCHAR = xkunjungan
           AND pendaftaran_t.carabayar_id = xcarabayar_id
           AND ruangan_m.ruangan_id::INTEGER = xruangan_id::INTEGER
           AND pendaftaran_t.pendaftaran_id NOT IN (
               SELECT 
                   pendaftaranol_t.pendaftaran_id
               FROM pendaftaranol_t
               JOIN (SELECT 
                           a.pendaftaran_id,
                           a.tgl_pendaftaran
                       FROM pendaftaran_t a) pendaftaran_t on pendaftaranol_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
               WHERE pendaftaran_t.tgl_pendaftaran::DATE BETWEEN xtanggalawal::DATE AND xtanggalakhir::DATE)
           AND pasien_m.jeniskelamin::TEXT = xjeniskelamin::TEXT;
       END IF;
       RETURN NEXT;
       END 
       \$function\$
       ;
       ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230207_121320_migrate_GB822_f_getpasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230207_121320_migrate_GB822_f_getpasien cannot be reverted.\n";

        return false;
    }
    */
}
