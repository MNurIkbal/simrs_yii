<?php

use yii\db\Migration;

/**
 * Class m201124_105519_migrate_mhkn_20201124_trg_laporansensusbulananpasienranap_fn_20201124
 */
class m201124_105519_migrate_mhkn_20201124_trg_laporansensusbulananpasienranap_fn_20201124 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"laporansensusbulananpasienranap_fn\"(\"xtanggal\" date, \"xkelaspelayanan_id\" int4, \"xruangan_id\" int4)
  RETURNS TABLE(\"tanggal\" text, \"pasien_masuk\" int8, \"pasien_pindahan\" int8, \"keluar_hidup\" int8, \"keluar_dipindahkan\" int8, \"keluar_meninggalkur48\" int8, \"keluar_meninggalleb48\" int8, \"pasien_akhir\" int8) AS \$BODY\$BEGIN
    
RETURN QUERY
    
SELECT d.nominal_day AS tanggal,
(
        SELECT COUNT(pasienadmisi_id)
        FROM pasienadmisi_t
        WHERE (is_deleted = false)
        AND (tgl_admisi BETWEEN CAST(d.nominal_day || ' 00:00:00' AS TIMESTAMP) AND CAST(d.nominal_day || ' 23:59:59' AS TIMESTAMP))
        AND status_ranap != 453
        AND kelaspelayanan_id = xkelaspelayanan_id
        AND ruangan_id = xruangan_id
) AS pasien_masuk,
(
        SELECT COUNT(pasienadmisi_t.pasienadmisi_id)
        FROM pasienadmisi_t
        LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        WHERE (pasienadmisi_t.is_deleted = false)
        AND (pindahkamar_t.tgl_pindahkamar BETWEEN CAST(d.nominal_day || ' 00:00:00' AS TIMESTAMP) AND CAST(d.nominal_day || ' 23:59:59' AS TIMESTAMP))
        AND pasienadmisi_t.status_ranap != 453
        AND pindahkamar_t.kelaspelayanan_id = xkelaspelayanan_id
        AND pindahkamar_t.ruangan_id = xruangan_id
) AS pasien_pindahan,
(
        SELECT COUNT(pasienpulang_t.pasienadmisi_id)
        FROM pasienpulang_t
        LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pasienpulang_t.pasienadmisi_id
        WHERE (pasienpulang_t.tglpasienpulang BETWEEN CAST(d.nominal_day || ' 00:00:00' AS TIMESTAMP) AND CAST(d.nominal_day || ' 23:59:59' AS TIMESTAMP))
        AND pasienpulang_t.pasienadmisi_id IS NOT NULL
        AND pasienpulang_t.carakeluar_id IN (1,2,3,5,6,7)
        AND pasienadmisi_t.status_ranap != 453
        AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
        AND pasienadmisi_t.ruangan_id = xruangan_id
) AS keluar_hidup,
(
        SELECT COUNT(pasienadmisi_t.pasienadmisi_id)
        FROM pasienadmisi_t
        LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        LEFT JOIN masukkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
        WHERE (pindahkamar_t.tgl_pindahkamar BETWEEN CAST(d.nominal_day || ' 00:00:00' AS TIMESTAMP) AND CAST(d.nominal_day || ' 23:59:59' AS TIMESTAMP))
        AND masukkamar_t.pindahkamar_id IS NOT NULL
        AND pasienadmisi_t.status_ranap != 453
        AND masukkamar_t.kelaspelayanan_id = xkelaspelayanan_id
        AND masukkamar_t.ruangan_id = xruangan_id
) AS keluar_dipindahkan,
(
        SELECT COUNT(pasienadmisi_t.pasienadmisi_id)
        FROM pasienadmisi_t
        LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        WHERE (pasienpulang_t.tglpasienpulang BETWEEN CAST(d.nominal_day || ' 00:00:00' AS TIMESTAMP) AND CAST(d.nominal_day || ' 23:59:59' AS TIMESTAMP))
        AND pasienpulang_t.pasienadmisi_id IS NOT NULL
        AND pasienpulang_t.carakeluar_id = 4
        AND pasienpulang_t.kondisikeluar_id = 7
        AND pasienpulang_t.ruanganakhir_id = xruangan_id
        AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
) AS keluar_meninggalkur48,
(
        SELECT COUNT(pasienadmisi_t.pasienadmisi_id)
        FROM pasienadmisi_t
        LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        WHERE (pasienpulang_t.tglpasienpulang BETWEEN CAST(d.nominal_day || ' 00:00:00' AS TIMESTAMP) AND CAST(d.nominal_day || ' 23:59:59' AS TIMESTAMP))
        AND pasienpulang_t.pasienadmisi_id IS NOT NULL
        AND pasienpulang_t.carakeluar_id = 4
        AND pasienpulang_t.kondisikeluar_id = 5
        AND pasienpulang_t.ruanganakhir_id = xruangan_id
        AND pasienadmisi_t.kelaspelayanan_id = xkelaspelayanan_id
) AS keluar_meninggalleb48,
(
        SELECT COUNT(masukkamar_t.masukkamar_id) -- revisi jumlah ady (20201123)
        FROM pasienadmisi_t
            JOIN (SELECT
                            masukkamar_t.pasienadmisi_id,
                            masukkamar_t.masukkamar_id,
                            masukkamar_t.kelaspelayanan_id,
                            masukkamar_t.ruangan_id,
                            masukkamar_t.tgl_masukkamar                         
                        FROM masukkamar_t
                            JOIN (SELECT
                                                MIN(masukkamar_t.masukkamar_id) as id,
                                                masukkamar_t.pasienadmisi_id
                                        FROM masukkamar_t
                                        WHERE masukkamar_t.is_deleted=FALSE
                                        GROUP BY masukkamar_t.pasienadmisi_id
                                        ) histori_awal ON masukkamar_t.masukkamar_id = histori_awal.id
                        WHERE masukkamar_t.is_deleted=FALSE) masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id -- revisi jumlah ady (20201123)
        WHERE (pasienadmisi_t.is_deleted = false)  
--      AND masukkamar_t.pindahkamar_id is null
        AND (masukkamar_t.tgl_masukkamar < CAST(d.nominal_day || ' 00:00:00' AS TIMESTAMP))
        AND ((pasienadmisi_t.tgl_pulang IS NULL) OR (pasienadmisi_t.tgl_pulang > CAST(d.nominal_day || ' 23:59:59' AS TIMESTAMP)))
        AND pasienadmisi_t.status_ranap != 453
        AND masukkamar_t.kelaspelayanan_id = xkelaspelayanan_id
        AND masukkamar_t.ruangan_id = xruangan_id
) AS pasien_akhir
FROM (
        SELECT SUBSTR(CAST(generate_series AS VARCHAR),1,10) AS nominal_day
        FROM generate_series(xtanggal,(xtanggal::date + '1 month'::interval - '1 day'::interval )::date,'1 day')
) AS d;

    RETURN;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000
        ;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201124_105519_migrate_mhkn_20201124_trg_laporansensusbulananpasienranap_fn_20201124 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201124_105519_migrate_mhkn_20201124_trg_laporansensusbulananpasienranap_fn_20201124 cannot be reverted.\n";

        return false;
    }
    */
}
