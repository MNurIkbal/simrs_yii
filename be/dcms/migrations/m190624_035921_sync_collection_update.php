<?php

use yii\db\Migration;

/**
 * Class m190624_035921_sync_collection_update
 */
class m190624_035921_sync_collection_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
     ALTER TABLE syncakuntansi_r ADD pengembalianuangmuka_id int4;
        ');

        $this->execute('
     DROP VIEW "public"."sync_collection";
        ');

        $this->execute("
     CREATE OR REPLACE VIEW public.sync_collection AS 
 SELECT carabayar_m.metode_pembayaran AS paymentmethod_id,
        CASE carabayar_m.metode_pembayaran
            WHEN 403 THEN 'Cash'::text
            ELSE 'Bank'::text
        END AS paymentmethod_name,
    ''::character varying(10) AS bank_id,
    pembayaranpelayanan_t.pembayaranpelayanan_id,
    pendaftaran_t.pendaftaran_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    pembayaranpelayanan_t.no_pembayaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    ruangan_m.instalasi_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    concat(pembayaranpelayanan_t.no_pembayaran, '-', pasien_m.nama_pasien) AS notes,
        CASE pembayaranpelayanan_t.carabayar_id
            WHEN 5 THEN tandabuktibayar_t.uangditerima
            ELSE COALESCE(piutangasuransi_t.jmlpiutangasuransi, 0::double precision)
        END AS total_pelayanan,
    pembayaranpelayanan_t.biaya_administrasi,
    pembayaranpelayanan_t.pembulatan,
    0::double precision AS kembalian,
        CASE pembayaranpelayanan_t.carabayar_id
            WHEN 5 THEN 'Umum'::text
            WHEN 2 THEN 'Asuransi'::text
            WHEN 3 THEN 'Asuransi'::text
            WHEN 6 THEN 'Asuransi'::text
            WHEN 7 THEN 'Perusahaan'::text
            ELSE 'Lainnya'::text
        END AS paymentmode_name,
    COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, 0::double precision) AS uangmuka,
        CASE COALESCE(uangmuka.is_uangmuka, 0::bigint)
            WHEN 0 THEN 0
            ELSE 1
        END AS is_uangmuka,
        CASE pembayaranpelayanan_t.carabayar_id
            WHEN 5 THEN
            CASE COALESCE(uangmuka.is_uangmuka, 0::bigint)
                WHEN 0 THEN pembayaranpelayanan_t.total_biayapelayanan + pembayaranpelayanan_t.biaya_administrasi + pembayaranpelayanan_t.pembulatan - tandabuktibayar_t.uangditerima
                ELSE
                CASE COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, 0::double precision)
                    WHEN 0 THEN pembayaranpelayanan_t.total_biayapelayanan + pembayaranpelayanan_t.biaya_administrasi + pembayaranpelayanan_t.pembulatan - tandabuktibayar_t.uangditerima
                    ELSE pembayaranpelayanan_t.total_biayapelayanan + pembayaranpelayanan_t.biaya_administrasi + pembayaranpelayanan_t.pembulatan - COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, 0::double precision)
                END
            END
            ELSE pembayaranpelayanan_t.total_biayapelayanan - tandabuktibayar_t.jmlpembayaran
        END AS sisatagihan
   FROM pembayaranpelayanan_t
     JOIN tandabuktibayar_t ON pembayaranpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
     LEFT JOIN pemakaianuangmuka_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = pemakaianuangmuka_t.pembayaranpelayanan_id
     LEFT JOIN piutangasuransi_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = piutangasuransi_t.pembayaranpelayanan_id
     JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT bayaruangmuka_t.pendaftaran_id,
            count(*) AS is_uangmuka
           FROM bayaruangmuka_t
          GROUP BY bayaruangmuka_t.pendaftaran_id) uangmuka ON pembayaranpelayanan_t.pendaftaran_id = uangmuka.pendaftaran_id
  WHERE NOT (pembayaranpelayanan_t.pembayaranpelayanan_id IN ( SELECT COALESCE(syncakuntansi_r.pembayaranpelayanan_id, 0) AS pembayaranpelayanan_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT carabayar_m.metode_pembayaran AS paymentmethod_id,
        CASE carabayar_m.metode_pembayaran
            WHEN 403 THEN 'Cash'::text
            ELSE 'Bank'::text
        END AS paymentmethod_name,
    ''::character varying(10) AS bank_id,
    pembayaranpelayanan_t.pembayaranpelayanan_id,
    COALESCE(pendaftaran_t.pendaftaran_id, penjualanresep_t.penjualanresep_id) AS pendaftaran_id,
    COALESCE(pasien_m.nama_pasien, pegawai_m.nama_pegawai) AS nama_pasien,
    COALESCE(pasien_m.no_rekam_medik, pegawai_m.nomorindukpegawai) AS no_rekam_medik,
    COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_pendaftaran,
    pembayaranpelayanan_t.no_pembayaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    ruangan_m.instalasi_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    concat(pembayaranpelayanan_t.no_pembayaran, '-', COALESCE(pasien_m.nama_pasien, pegawai_m.nama_pegawai)) AS notes,
        CASE pembayaranpelayanan_t.carabayar_id
            WHEN 5 THEN tandabuktibayar_t.uangditerima
            ELSE COALESCE(piutangasuransi_t.jmlpiutangasuransi, 0::double precision)
        END AS total_pelayanan,
    pembayaranpelayanan_t.biaya_administrasi,
    pembayaranpelayanan_t.pembulatan,
    0::double precision AS kembalian,
        CASE pembayaranpelayanan_t.carabayar_id
            WHEN 5 THEN 'Umum'::text
            WHEN 2 THEN 'Asuransi'::text
            WHEN 3 THEN 'Asuransi'::text
            WHEN 6 THEN 'Asuransi'::text
            WHEN 7 THEN 'Perusahaan'::text
            ELSE 'Lainnya'::text
        END AS paymentmode_name,
    COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, 0::double precision) AS uangmuka,
        CASE COALESCE(uangmuka.is_uangmuka, 0::bigint)
            WHEN 0 THEN 0
            ELSE 1
        END AS is_uangmuka,
        CASE pembayaranpelayanan_t.carabayar_id
            WHEN 5 THEN
            CASE COALESCE(uangmuka.is_uangmuka, 0::bigint)
                WHEN 0 THEN pembayaranpelayanan_t.total_biayapelayanan + pembayaranpelayanan_t.biaya_administrasi + pembayaranpelayanan_t.pembulatan - tandabuktibayar_t.uangditerima
                ELSE
                CASE COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, 0::double precision)
                    WHEN 0 THEN pembayaranpelayanan_t.total_biayapelayanan + pembayaranpelayanan_t.biaya_administrasi + pembayaranpelayanan_t.pembulatan - tandabuktibayar_t.uangditerima
                    ELSE pembayaranpelayanan_t.total_biayapelayanan + pembayaranpelayanan_t.biaya_administrasi + pembayaranpelayanan_t.pembulatan - COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, 0::double precision)
                END
            END
            ELSE pembayaranpelayanan_t.total_biayapelayanan - tandabuktibayar_t.jmlpembayaran
        END AS sisatagihan
   FROM pembayaranpelayanan_t
     JOIN tandabuktibayar_t ON pembayaranpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
     LEFT JOIN piutangasuransi_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = piutangasuransi_t.pembayaranpelayanan_id
     JOIN penjualanresep_t ON pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN pemakaianuangmuka_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = pemakaianuangmuka_t.pembayaranpelayanan_id
     LEFT JOIN bayaruangmuka_t ON tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id
     LEFT JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT bayaruangmuka_t_1.pendaftaran_id,
            count(*) AS is_uangmuka
           FROM bayaruangmuka_t bayaruangmuka_t_1
          GROUP BY bayaruangmuka_t_1.pendaftaran_id) uangmuka ON pembayaranpelayanan_t.pendaftaran_id = uangmuka.pendaftaran_id
  WHERE NOT (pembayaranpelayanan_t.pembayaranpelayanan_id IN ( SELECT COALESCE(syncakuntansi_r.pembayaranpelayanan_id, 0) AS pembayaranpelayanan_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT carabayar_m.metode_pembayaran AS paymentmethod_id,
        CASE carabayar_m.metode_pembayaran
            WHEN 403 THEN 'Cash'::text
            ELSE 'Bank'::text
        END AS paymentmethod_name,
    ''::character varying(10) AS bank_id,
    bayaruangmuka_t.bayaruangmuka_id AS pembayaranpelayanan_id,
    pendaftaran_t.pendaftaran_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    bayaruangmuka_t.no_uangmuka AS no_pembayaran,
    bayaruangmuka_t.tgl_uangmuka AS tgl_pembayaran,
    ruangan_m.instalasi_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    concat(bayaruangmuka_t.no_uangmuka, '-', pasien_m.nama_pasien) AS notes,
    bayaruangmuka_t.jumlah_uangmuka AS total_pelayanan,
    tandabuktibayar_t.biayaadministrasi AS biaya_administrasi,
    tandabuktibayar_t.jmlpembulatan AS pembulatan,
    0::numeric(15,0) AS kembalian,
    'Uangmuka'::text AS paymentmode_name,
    0::numeric(15,0) AS uangmuka,
    0 AS is_uangmuka,
    0::double precision AS sisatagihan
   FROM bayaruangmuka_t
     JOIN tandabuktibayar_t ON bayaruangmuka_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
     LEFT JOIN pendaftaran_t ON bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
  WHERE NOT (bayaruangmuka_t.bayaruangmuka_id IN ( SELECT COALESCE(syncakuntansi_r.bayaruangmuka_id, 0) AS bayaruangmuka_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE));
        ");

        $this->execute('
     ALTER TABLE "public"."sync_collection" OWNER TO "postgres";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190624_035921_sync_collection_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190624_035921_sync_collection_update cannot be reverted.\n";

        return false;
    }
    */
}
