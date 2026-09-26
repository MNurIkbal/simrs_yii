<?php

use yii\db\Migration;

/**
 * Class m190624_041217_sync_disbursment_update
 */
class m190624_041217_sync_disbursment_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
    DROP VIEW IF exists public.sync_disbursment;
        ');

          $this->execute("
    CREATE OR REPLACE VIEW public.sync_disbursment AS 
 SELECT carabayar_m.metode_pembayaran AS paymentmethod_id,
        CASE carabayar_m.metode_pembayaran
            WHEN 403 THEN 'Cash'::text
            ELSE 'Bank'::text
        END AS paymentmethod_name,
    ''::character varying(10) AS bank_id,
    pengembalianuangmuka_t.pengembalianuangmuka_id AS pembayaranpelayanan_id,
    pendaftaran_t.pendaftaran_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pendaftaran_t.no_pendaftaran,
    tandabuktikeluar_t.no_buktikeluar AS no_pembayaran,
    tandabuktikeluar_t.tgl_buktikeluar AS tgl_pembayaran,
    ruangan_m.instalasi_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    concat(tandabuktikeluar_t.no_buktikeluar, '-', pasien_m.nama_pasien) AS notes,
    pengembalianuangmuka_t.total_pengembalian - tandabuktikeluar_t.biaya_administrasi - tandabuktikeluar_t.jml_pembulatan AS total_pelayanan,
    tandabuktikeluar_t.biaya_administrasi,
    tandabuktikeluar_t.jml_pembulatan AS pembulatan,
    0::numeric(15,0) AS kembalian,
    'Uangmuka'::text AS paymentmode_name,
    0::numeric(15,0) AS uangmuka,
    0 AS is_uangmuka,
    0::double precision AS sisatagihan
   FROM pengembalianuangmuka_t
     JOIN tandabuktikeluar_t ON pengembalianuangmuka_t.tandabuktikeluar_id = tandabuktikeluar_t.tandabuktikeluar_id
     LEFT JOIN pendaftaran_t ON pengembalianuangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
  WHERE NOT (pengembalianuangmuka_t.pengembalianuangmuka_id IN ( SELECT COALESCE(syncakuntansi_r.pengembalianuangmuka_id, 0) AS pengembalianuangmuka_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE));
        ");

           $this->execute('
    ALTER TABLE public.sync_disbursment
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190624_041217_sync_disbursment_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190624_041217_sync_disbursment_update cannot be reverted.\n";

        return false;
    }
    */
}
