<?php

use yii\db\Migration;

/**
 * Class m241128_111633_migrate_GLS876_laporanrekapkinerjaprofesional_fn
 */
class m241128_111633_migrate_GLS876_laporanrekapkinerjaprofesional_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS public.laporanrekapkinerjaprofesional_fn(date, date, int4, int4);");
        $this->execute("DROP FUNCTION IF EXISTS public.laporanrekapkinerjaprofesional_fn(date, date, int4);");
        $this->execute("CREATE OR REPLACE FUNCTION public.laporanrekapkinerjaprofesional_fn(xstart_date date, xend_date date, xruangan_id integer DEFAULT NULL::integer)
 RETURNS TABLE(kelaspelayanan_nama character varying, jumlah_bed numeric, pasien_awal bigint, pasien_masuk numeric, pindah_ke bigint, jumlah_pasien_masuk numeric, keluar_hidup bigint, dipindahkan_dari bigint, rujuk_rs_lain bigint, meninggal_kurang_48 bigint, meninggal_lebih_48 bigint, jumlah_pasien_keluar bigint, hp numeric, los bigint, alos numeric, bor_today numeric, toi numeric, bto bigint, ndr numeric, gdr numeric)
 LANGUAGE plpgsql
AS \$function\$

BEGIN

	RETURN query

	SELECT
		kinerjaprofesional.kelaspelayanan_nama,
		sum(kinerjaprofesional.jumlah_bed) AS jumlah_bed,
		sum(kinerjaprofesional.pasien_awal) AS pasien_awal,
		sum(kinerjaprofesional.pasien_masuk) AS pasien_masuk,
		sum(kinerjaprofesional.pindah_ke) AS pindah_ke,
		sum(kinerjaprofesional.jumlah_pasien_masuk) AS jumlah_pasien_masuk,
		sum(kinerjaprofesional.keluar_hidup) AS keluar_hidup,
		sum(kinerjaprofesional.dipindahkan_dari) AS dipindahkan_dari,
		sum(kinerjaprofesional.rujuk_rs_lain) AS rujuk_rs_lain,
		sum(kinerjaprofesional.meninggal_kurang_48) AS meninggal_kurang_48,
		sum(kinerjaprofesional.meninggal_lebih_48) AS meninggal_lebih_48,
		sum(kinerjaprofesional.jumlah_pasien_keluar) AS jumlah_pasien_keluar,
		sum(kinerjaprofesional.hp) AS hp,
		sum(kinerjaprofesional.los) AS los,
		sum(kinerjaprofesional.alos) AS alos,
		round(sum(kinerjaprofesional.bor_today) / count(kinerjaprofesional.ruangan_nama)) AS bor_today,
		sum(kinerjaprofesional.toi) AS toi,
		sum(kinerjaprofesional.bto) AS bto,
		sum(kinerjaprofesional.ndr) AS ndr,
		sum(kinerjaprofesional.gdr) AS gdr
	FROM (
		WITH ruangan_kelas AS (
		----- RUANGAN DAN KELAS
			SELECT
				ruangan_m.ruangan_id,
				ruangan_m.ruangan_nama,
				kelaspelayanan_m.kelaspelayanan_id,
				kelaspelayanan_m.kelaspelayanan_nama,
				count(kamartempattidur_m.kamartempattidur_id) AS jumlah_bed
			FROM kamarruangan_m
			JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.instalasi_id = 3 
			AND (xruangan_id IS NULL OR ruangan_m.ruangan_id = xruangan_id)
			JOIN kamartempattidur_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id AND kamartempattidur_m.is_deleted = FALSE AND kamartempattidur_m.is_active = TRUE
			JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
			WHERE kamarruangan_m.is_rekapkinerjaprofesi = TRUE
			AND kamarruangan_m.is_deleted = FALSE
			AND kamarruangan_m.is_active = TRUE
			GROUP BY ruangan_m.ruangan_id, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama
		),
		----- PASIEN MASUK
		pasien_masuk AS (
			SELECT
				masukkamar_t.ruangan_id,
				masukkamar_t.kelaspelayanan_id,
				count(*) AS jumlah
			FROM masukkamar_t
			JOIN pasienadmisi_t ON masukkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
			AND pasienadmisi_t.status_ranap <> 453
			AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
			WHERE masukkamar_t.tgl_masukkamar::date BETWEEN xstart_date AND xend_date
			GROUP BY masukkamar_t.ruangan_id, masukkamar_t.kelaspelayanan_id
		),
		----- PASIEN PINDAH KE
		pindah_ke AS (
			SELECT
				pindahkamar_t.ruangan_id,
				pindahkamar_t.kelaspelayanan_id,
				count(*) AS jumlah
			FROM pindahkamar_t
			WHERE pindahkamar_t.tgl_pindahkamar::date BETWEEN xstart_date AND xend_date
			GROUP BY pindahkamar_t.ruangan_id, pindahkamar_t.kelaspelayanan_id
		),
		----- PASIEN KELUAR HIDUP
		keluar_hidup AS (
			SELECT
				pasienadmisi_t.ruangan_id,
		        pasienadmisi_t.kelaspelayanan_id,
		        count(*) AS jumlah
		    FROM pasienadmisi_t
		    JOIN (SELECT
		                a.pasienpulang_id,
		                a.tglpasienpulang
		            FROM pasienpulang_t a
		            WHERE a.is_deleted = FALSE
		            AND a.pasienbatalpulang_id IS NULL
		            AND a.carakeluar_id IN (1,3,6)
					AND a.tglpasienpulang::date BETWEEN xstart_date AND xend_date) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
		    GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id
		)
		SELECT
			ruangan_kelas.ruangan_nama,
			ruangan_kelas.kelaspelayanan_nama,
			ruangan_kelas.jumlah_bed,
			COALESCE(f_getpasiensebelum(xstart_date, COALESCE(xruangan_id, ruangan_kelas.ruangan_id), ruangan_kelas.kelaspelayanan_id), 0) AS pasien_awal,
			COALESCE(pasien_masuk.jumlah, 0) AS pasien_masuk,
			COALESCE(pindah_ke.jumlah::integer, 0) AS pindah_ke,
			COALESCE(f_getpasiensebelum(xstart_date, COALESCE(xruangan_id, ruangan_kelas.ruangan_id), ruangan_kelas.kelaspelayanan_id), 0) + COALESCE(pasien_masuk.jumlah, 0) + COALESCE(pindah_ke.jumlah::integer, 0) AS jumlah_pasien_masuk,
			COALESCE(keluar_hidup.jumlah::integer, 0) AS keluar_hidup,
			COALESCE(dipindahkan_dari.jumlah::integer, 0) AS dipindahkan_dari,
			COALESCE(meninggal_kurang_48.jumlah::integer, 0) AS meninggal_kurang_48,
			COALESCE(meninggal_lebih_48.jumlah::integer, 0) AS meninggal_lebih_48,
			COALESCE(rujuk_rs_lain.jumlah::integer, 0) AS rujuk_rs_lain,
			COALESCE(keluar_hidup.jumlah::integer, 0) + COALESCE(dipindahkan_dari.jumlah::integer, 0) + COALESCE(meninggal_kurang_48.jumlah::integer, 0) + COALESCE(meninggal_lebih_48.jumlah::integer, 0) + COALESCE(rujuk_rs_lain.jumlah::integer, 0) AS jumlah_pasien_keluar,
			(COALESCE(f_getpasiensebelum(xstart_date, COALESCE(xruangan_id, ruangan_kelas.ruangan_id), ruangan_kelas.kelaspelayanan_id), 0) + COALESCE(pasien_masuk.jumlah, 0) + COALESCE(pindah_ke.jumlah::integer, 0)) - (COALESCE(keluar_hidup.jumlah::integer, 0) + COALESCE(dipindahkan_dari.jumlah::integer, 0) + COALESCE(meninggal_kurang_48.jumlah::integer, 0) + COALESCE(meninggal_lebih_48.jumlah::integer, 0) + COALESCE(rujuk_rs_lain.jumlah::integer, 0)) AS hp,
			COALESCE(lama_rawat.jumlah::integer, 0) AS los,
		    CASE
		        WHEN COALESCE(keluar_hidup.jumlah::integer, 0) + COALESCE(dipindahkan_dari.jumlah::integer, 0) + COALESCE(meninggal_kurang_48.jumlah::integer, 0) + COALESCE(meninggal_lebih_48.jumlah::integer, 0) + COALESCE(rujuk_rs_lain.jumlah::integer, 0) = 0 THEN 0
		        ELSE COALESCE(round(lama_rawat.jumlah / (COALESCE(keluar_hidup.jumlah::integer, 0) + COALESCE(dipindahkan_dari.jumlah::integer, 0) + COALESCE(meninggal_kurang_48.jumlah::integer, 0) + COALESCE(meninggal_lebih_48.jumlah::integer, 0) + COALESCE(rujuk_rs_lain.jumlah::integer, 0))::NUMERIC), 0)
		    END AS alos,
			round(((COALESCE(f_getpasiensebelum(xstart_date, COALESCE(xruangan_id, ruangan_kelas.ruangan_id), ruangan_kelas.kelaspelayanan_id), 0) + COALESCE(pasien_masuk.jumlah, 0) + COALESCE(pindah_ke.jumlah::integer, 0)) - (COALESCE(keluar_hidup.jumlah::integer, 0) + COALESCE(dipindahkan_dari.jumlah::integer, 0) + COALESCE(meninggal_kurang_48.jumlah::integer, 0) + COALESCE(meninggal_lebih_48.jumlah::integer, 0) + COALESCE(rujuk_rs_lain.jumlah::integer, 0)))::NUMERIC / (ruangan_kelas.jumlah_bed * (xend_date::date - xstart_date::date + 1)) * 100) AS bor_today,
		    CASE
		        WHEN (COALESCE(keluar_hidup.jumlah::integer, 0) + COALESCE(dipindahkan_dari.jumlah::integer, 0) + COALESCE(meninggal_kurang_48.jumlah::integer, 0) + COALESCE(meninggal_lebih_48.jumlah::integer, 0) + COALESCE(rujuk_rs_lain.jumlah::integer, 0)) = 0 THEN 0
		        ELSE ((1 * ruangan_kelas.jumlah_bed) - ((COALESCE(f_getpasiensebelum(xstart_date, COALESCE(xruangan_id, ruangan_kelas.ruangan_id), ruangan_kelas.kelaspelayanan_id), 0) + COALESCE(pasien_masuk.jumlah, 0) + COALESCE(pindah_ke.jumlah::integer, 0)) - (COALESCE(keluar_hidup.jumlah::integer, 0) + COALESCE(dipindahkan_dari.jumlah::integer, 0) + COALESCE(meninggal_kurang_48.jumlah::integer, 0) + COALESCE(meninggal_lebih_48.jumlah::integer, 0) + COALESCE(rujuk_rs_lain.jumlah::integer, 0)))) / (COALESCE(keluar_hidup.jumlah::integer, 0) + COALESCE(dipindahkan_dari.jumlah::integer, 0) + COALESCE(meninggal_kurang_48.jumlah::integer, 0) + COALESCE(meninggal_lebih_48.jumlah::integer, 0) + COALESCE(rujuk_rs_lain.jumlah::integer, 0))::NUMERIC
		    END AS toi,
		    0 AS bto,
		    CASE
		        WHEN (COALESCE(keluar_hidup.jumlah::integer, 0) + COALESCE(dipindahkan_dari.jumlah::integer, 0) + COALESCE(meninggal_kurang_48.jumlah::integer, 0) + COALESCE(meninggal_lebih_48.jumlah::integer, 0) + COALESCE(rujuk_rs_lain.jumlah::integer, 0)) = 0 THEN 0
		        ELSE COALESCE(meninggal_lebih_48.jumlah::integer, 0) / (COALESCE(keluar_hidup.jumlah::integer, 0) + COALESCE(dipindahkan_dari.jumlah::integer, 0) + COALESCE(meninggal_kurang_48.jumlah::integer, 0) + COALESCE(meninggal_lebih_48.jumlah::integer, 0) + COALESCE(rujuk_rs_lain.jumlah::integer, 0))::NUMERIC
		    END AS ndr,
		    CASE
		        WHEN (COALESCE(keluar_hidup.jumlah::integer, 0) + COALESCE(dipindahkan_dari.jumlah::integer, 0) + COALESCE(meninggal_kurang_48.jumlah::integer, 0) + COALESCE(meninggal_lebih_48.jumlah::integer, 0) + COALESCE(rujuk_rs_lain.jumlah::integer, 0)) = 0 THEN 0
		        ELSE (COALESCE(meninggal_kurang_48.jumlah::integer, 0) + COALESCE(meninggal_lebih_48.jumlah::integer, 0)) / (COALESCE(keluar_hidup.jumlah::integer, 0) + COALESCE(dipindahkan_dari.jumlah::integer, 0) + COALESCE(meninggal_kurang_48.jumlah::integer, 0) + COALESCE(meninggal_lebih_48.jumlah::integer, 0) + COALESCE(rujuk_rs_lain.jumlah::integer, 0))::NUMERIC
		    END AS gdr
		FROM ruangan_kelas
		LEFT JOIN pasien_masuk ON ruangan_kelas.kelaspelayanan_id = pasien_masuk.kelaspelayanan_id AND ruangan_kelas.ruangan_id = pasien_masuk.ruangan_id
		LEFT JOIN pindah_ke ON ruangan_kelas.kelaspelayanan_id = pindah_ke.kelaspelayanan_id AND ruangan_kelas.ruangan_id = pindah_ke.ruangan_id
		LEFT JOIN keluar_hidup ON ruangan_kelas.kelaspelayanan_id = keluar_hidup.kelaspelayanan_id AND ruangan_kelas.ruangan_id = keluar_hidup.ruangan_id
		----- PASIEN DIPINDAHKAN DARI
		LEFT JOIN (SELECT
						masukkamar_t.ruangan_id,
						masukkamar_t.kelaspelayanan_id,
						count(*) AS jumlah
					FROM masukkamar_t
					JOIN pindahkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
					AND pindahkamar_t.tgl_pindahkamar::date BETWEEN xstart_date AND xend_date
					JOIN pasienadmisi_t ON masukkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
					AND pasienadmisi_t.status_ranap::integer <> 453
					AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
					GROUP BY masukkamar_t.ruangan_id, masukkamar_t.kelaspelayanan_id) dipindahkan_dari ON dipindahkan_dari.kelaspelayanan_id = ruangan_kelas.kelaspelayanan_id AND dipindahkan_dari.ruangan_id = ruangan_kelas.ruangan_id 
		----- PASIEN KELUAR MENINGGAL KURANG 48 JAM + DEATH ON ARRIVAL
		LEFT JOIN (SELECT
						pasienadmisi_t.ruangan_id,
		                pasienadmisi_t.kelaspelayanan_id,
		                count(*) AS jumlah
		            FROM pasienadmisi_t
		            JOIN (SELECT
		                        a.pasienpulang_id,
		                        a.tglpasienpulang
		                    FROM pasienpulang_t a
		                    WHERE a.is_deleted = FALSE
		                    AND a.pasienbatalpulang_id IS NULL
		                    AND a.carakeluar_id = 4
		                    AND a.kondisikeluar_id IN (6,7)
							AND a.tglpasienpulang::date BETWEEN xstart_date AND xend_date) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
		            GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id) meninggal_kurang_48 ON meninggal_kurang_48.kelaspelayanan_id = ruangan_kelas.kelaspelayanan_id AND meninggal_kurang_48.ruangan_id = ruangan_kelas.ruangan_id 
		----- PASIEN MENINGGAL LEBIH 48 JAM
		LEFT JOIN (SELECT
						pasienadmisi_t.ruangan_id,
		                pasienadmisi_t.kelaspelayanan_id,
		                count(*) AS jumlah
		            FROM pasienadmisi_t
		            JOIN (SELECT
		                        a.pasienpulang_id,
		                        a.tglpasienpulang
		                    FROM pasienpulang_t a
		                    WHERE a.is_deleted = FALSE
		                    AND a.pasienbatalpulang_id IS NULL
		                    AND a.carakeluar_id = 4
		                    AND a.kondisikeluar_id = 5
							AND a.tglpasienpulang::date BETWEEN xstart_date AND xend_date) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
		            GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id) meninggal_lebih_48 ON meninggal_lebih_48.kelaspelayanan_id = ruangan_kelas.kelaspelayanan_id AND meninggal_lebih_48.ruangan_id = ruangan_kelas.ruangan_id
		----- PASIEN RUJUK RS LAIN
		LEFT JOIN (SELECT
						pasienadmisi_t.ruangan_id,
		                pasienadmisi_t.kelaspelayanan_id,
		                count(*) AS jumlah
		            FROM pasienadmisi_t
		            JOIN (SELECT
		                        a.pasienpulang_id,
		                        a.tglpasienpulang
		                    FROM pasienpulang_t a
		                    WHERE a.is_deleted = FALSE
		                    AND a.pasienbatalpulang_id IS NULL
		                    AND a.carakeluar_id = 2
							AND a.tglpasienpulang::date BETWEEN xstart_date AND xend_date) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
		            GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id) rujuk_rs_lain ON rujuk_rs_lain.kelaspelayanan_id = ruangan_kelas.kelaspelayanan_id AND rujuk_rs_lain.ruangan_id = ruangan_kelas.ruangan_id
		----- LAMA RAWAT (LOS)
		LEFT JOIN (SELECT
						pasienadmisi_t.ruangan_id,
		                pasienadmisi_t.kelaspelayanan_id,
		                sum(pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) AS jumlah
		            FROM pasienadmisi_t
		            JOIN (SELECT
		                        a.pasienpulang_id,
		                        a.tglpasienpulang
		                    FROM pasienpulang_t a
		                    WHERE a.is_deleted = FALSE
		                    AND a.pasienbatalpulang_id IS NULL) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
					WHERE pasienadmisi_t.tgl_admisi::date BETWEEN xstart_date AND xend_date
		            GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id) lama_rawat ON lama_rawat.kelaspelayanan_id = ruangan_kelas.kelaspelayanan_id AND lama_rawat.ruangan_id = ruangan_kelas.ruangan_id
	) kinerjaprofesional
	GROUP BY kinerjaprofesional.kelaspelayanan_nama;

END
\$function\$
;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241128_111633_migrate_GLS876_laporanrekapkinerjaprofesional_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241128_111633_migrate_GLS876_laporanrekapkinerjaprofesional_fn cannot be reverted.\n";

        return false;
    }
    */
}
