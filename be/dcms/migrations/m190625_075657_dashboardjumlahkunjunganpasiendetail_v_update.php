<?php

use yii\db\Migration;

/**
 * Class m190625_075657_dashboardjumlahkunjunganpasiendetail_v_update
 */
class m190625_075657_dashboardjumlahkunjunganpasiendetail_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
   DROP VIEW public.dashboardjumlahkunjunganpasiendetail_v;
        ');

          $this->execute("
    CREATE OR REPLACE VIEW public.dashboardjumlahkunjunganpasiendetail_v AS 
 SELECT x.nama_pasien,
    x.no_rm,
    x.instalasi_id,
    x.instalasi_nama,
    x.no_pendaftaran,
    x.tgl_pendaftaran,
    x.bulan,
    x.tahun
   FROM ( SELECT fgetpasien_nama(pendaftaran_t.pasien_id) AS nama_pasien,
            fgetpasien_rm(pendaftaran_t.pasien_id) AS no_rm,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text)::character varying AS bulan,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text)::character varying AS tahun
           FROM pendaftaran_t
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
          WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])) AND pendaftaran_t.is_deleted = false AND pendaftaran_t.status_periksa::integer <> 402
        UNION ALL
         SELECT fgetpasien_nama(pasienadmisi_t.pasien_id) AS nama_pasien,
            fgetpasien_rm(pasienadmisi_t.pasien_id) AS no_rm,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_pendaftaran,
            to_char(pasienadmisi_t.tgl_pendaftaran, 'MM'::text)::character varying AS bulan,
            to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY'::text)::character varying AS tahun
           FROM pasienadmisi_t
             LEFT JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
          WHERE pasienadmisi_t.is_deleted = false AND pasienadmisi_t.status_ranap <> 453
        UNION ALL
         SELECT fgetpasien_nama(pasienmasukpenunjang_t.pasien_id) AS nama_pasien,
            fgetpasien_rm(pasienmasukpenunjang_t.pasien_id) AS no_rm,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            COALESCE(pasienmasukpenunjang_t.no_masukpenunjang, pendaftaran_t.no_pendaftaran) AS no_pendaftaran,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_pendaftaran,
            to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'MM'::text)::character varying AS bulan,
            to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY'::text)::character varying AS tahun
           FROM pasienmasukpenunjang_t
             LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
          WHERE pasienmasukpenunjang_t.is_deleted = false AND (pasienmasukpenunjang_t.status_periksa::integer <> ALL (ARRAY[541, 472]))) x
  ORDER BY x.bulan, x.tahun, x.instalasi_id;
        ");

           $this->execute('
    ALTER TABLE public.dashboardjumlahkunjunganpasiendetail_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190625_075657_dashboardjumlahkunjunganpasiendetail_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190625_075657_dashboardjumlahkunjunganpasiendetail_v_update cannot be reverted.\n";

        return false;
    }
    */
}
