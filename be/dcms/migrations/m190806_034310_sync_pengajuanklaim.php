<?php

use yii\db\Migration;

/**
 * Class m190806_034310_sync_pengajuanklaim
 */
class m190806_034310_sync_pengajuanklaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
      DROP VIEW if exists public.sync_pengajuanklaim;
        ');

          $this->execute("
      CREATE OR REPLACE VIEW public.sync_pengajuanklaim AS 
 SELECT pengajuanklaim_t.pengajuanklaim_id AS id,
    pengajuanklaim_t.no_pengajuanklaim,
    pengajuanklaim_t.tgl_pengajuanklaim,
    pengajuanklaim_t.tgl_jatuhtempo,
    ruangan_m.instalasi_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    penjamin_m.penjamin_id,
        CASE carabayar_m.carabayar_id
            WHEN 5 THEN 'Umum'::text
            WHEN 2 THEN 'Asuransi'::text
            WHEN 3 THEN 'Asuransi'::text
            WHEN 6 THEN 'Asuransi'::text
            WHEN 7 THEN 'Perusahaan'::text
            ELSE 'Lainnya'::text
        END AS carabayar_kode,
    (carabayar_m.carabayar_nama::text || ' '::text) || penjamin_m.penjamin_nama::text AS penjamin_nama,
    pengajuanklaim_t.total_piutang,
    (((('Pengajuan klaim '::text || carabayar_m.carabayar_nama::text) || ' '::text) || penjamin_m.penjamin_nama::text) || ' no : '::text) || pengajuanklaim_t.no_pengajuanklaim::text AS keterangan,
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasienadmisi_id,
                    pendaftaran_t.carabayar_id,
                        CASE pendaftaran_t.carabayar_id
                            WHEN 6 THEN (((('Klaim no Pendaftaran '::text || pendaftaran_t.no_pendaftaran::text) || ' Atas Nama '::text) || COALESCE(pasien_m.nama_pasien, ''::character varying)::text) || ' No BPJS : '::text) || COALESCE(bpjs_t.nokartuasuransi, ''::character varying)::text
                            ELSE (((('Klaim no Pendaftaran '::text || pendaftaran_t.no_pendaftaran::text) || ' Atas Nama '::text) || COALESCE(asuransipasien_m.namapemilikasuransi, ''::character varying)::text) || ' No Asuransi : '::text) || COALESCE(asuransipasien_m.nokartuasuransi, ''::character varying)::text
                        END AS keterangan,
                    pengajuanklaimdetail_t.jumlah_piutang
                   FROM pengajuanklaimdetail_t
                     LEFT JOIN pendaftaran_t ON pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                     LEFT JOIN asuransipasien_m ON pendaftaran_t.pasien_id = asuransipasien_m.pasien_id AND pendaftaran_t.penjamin_id = asuransipasien_m.penjamin_id
                     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
                  WHERE pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id AND pengajuanklaimdetail_t.is_deleted IS FALSE) d1) AS detail
   FROM pengajuanklaim_t
     JOIN ruangan_m ON COALESCE(pengajuanklaim_t.ruangan_id, 54) = ruangan_m.ruangan_id
     JOIN penjamin_m ON pengajuanklaim_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
  WHERE NOT (pengajuanklaim_t.pengajuanklaim_id IN ( SELECT COALESCE(syncakuntansi_r.pengajuanklaim_id, 0) AS pengajuanklaim_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE));
        ");

           $this->execute('
     ALTER TABLE public.sync_pengajuanklaim
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190806_034310_sync_pengajuanklaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190806_034310_sync_pengajuanklaim cannot be reverted.\n";

        return false;
    }
    */
}
