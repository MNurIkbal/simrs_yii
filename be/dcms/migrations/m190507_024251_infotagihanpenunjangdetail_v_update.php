<?php

use yii\db\Migration;

/**
 * Class m190507_024251_infotagihanpenunjangdetail_v_update
 */
class m190507_024251_infotagihanpenunjangdetail_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
      DROP VIEW infotagihanpenunjangdetail_v;
        ');

        $this->execute('
   CREATE OR REPLACE VIEW infotagihanpenunjangdetail_v AS 
 SELECT penunjang.pendaftaran_id,
    penunjang.tglmasukpenunjang,
    penunjang.no_pendaftaran,
    penunjang.instalasi_nama,
    penunjang.ruangan_id,
    penunjang.ruangan_nama,
    penunjang.no_rekam_medik,
    penunjang.nama_pasien,
    penunjang.tgl_tindakan,
    penunjang.daftartindakan_nama,
    penunjang.tarif_satuan,
    penunjang.cyto_tindakan,
    penunjang.tarifcyto_tindakan,
    penunjang.qty_tindakan,
    penunjang.tarif_tindakan,
    penunjang.kelompoktindakan_id,
    penunjang.kelompoktindakan_nama,
    penunjang.tindakanpelayanan_id,
    penunjang.obatalkespasien_id,
    penunjang.pasienmasukpenunjang_id
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pasienmasukpenunjang_t.tglmasukpenunjang,
            pendaftaran_t.no_pendaftaran,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            tindakanpelayanan_t.tgl_tindakan,
            daftartindakan_m.daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.qty_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            kelompoktindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            tindakanpelayanan_t.tindakanpelayanan_id,
            0 AS obatalkespasien_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.ruangan_id
           FROM pendaftaran_t
             JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
             JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pasienmasukpenunjang_t.tglmasukpenunjang,
            pendaftaran_t.no_pendaftaran,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            tindakanpelayanan_t.tgl_tindakan,
            daftartindakan_m.daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.qty_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            kelompoktindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            tindakanpelayanan_t.tindakanpelayanan_id,
            0 AS obatalkespasien_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.ruangan_id
           FROM pasienadmisi_t
             JOIN pasienmasukpenunjang_t ON pasienadmisi_t.pasienadmisi_id = pasienmasukpenunjang_t.pasienadmisi_id
             JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pasienmasukpenunjang_t.tglmasukpenunjang,
            pendaftaran_t.no_pendaftaran,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            tindakanpelayanan_t.tgl_tindakan,
            tipepaket_m.tipepaket_nama AS daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.qty_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            NULL::integer AS kelompoktindakan_id,
            NULL::character varying AS kelompoktindakan_nama,
            tindakanpelayanan_t.tindakanpelayanan_id,
            0 AS obatalkespasien_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.ruangan_id
           FROM pendaftaran_t
             JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
             JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pasienmasukpenunjang_t.tglmasukpenunjang,
            pendaftaran_t.no_pendaftaran,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            tindakanpelayanan_t.tgl_tindakan,
            tipepaket_m.tipepaket_nama AS daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.qty_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            NULL::integer AS kelompoktindakan_id,
            NULL::character varying AS kelompoktindakan_nama,
            tindakanpelayanan_t.tindakanpelayanan_id,
            0 AS obatalkespasien_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.ruangan_id
           FROM pasienadmisi_t
             JOIN pasienmasukpenunjang_t ON pasienadmisi_t.pasienadmisi_id = pasienmasukpenunjang_t.pasienadmisi_id
             JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pasienmasukpenunjang_t.tglmasukpenunjang,
            pendaftaran_t.no_pendaftaran,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            obatalkespasien_t.tglpelayanan AS tgl_tindakan,
            obatalkes_m.obatalkes_nama AS daftartindakan_nama,
            obatalkespasien_t.hargasatuan_oa::integer AS tarif_satuan,
            NULL::boolean AS cyto_tindakan,
            0 AS tarifcyto_tindakan,
            obatalkespasien_t.qty_oa AS qty_tindakan,
            obatalkespasien_t.hargajual_oa::integer AS tarif_tindakan,
            jenisobatalkes_m.jenisobatalkes_id AS kelompoktindakan_id,
            jenisobatalkes_m.jenisobatalkes_nama AS kelompoktindakan_nama,
            NULL::integer AS tindakanpelayanan_id,
            obatalkespasien_t.obatalkespasien_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.ruangan_id
           FROM pendaftaran_t
             JOIN pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
             JOIN obatalkespasien_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = obatalkespasien_t.pasienmasukpenunjang_id AND obatalkespasien_t.obatsudahbayar_id IS NULL AND obatalkespasien_t.is_deleted = false
             JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id) penunjang
  GROUP BY penunjang.pendaftaran_id, penunjang.tglmasukpenunjang, penunjang.no_pendaftaran, penunjang.instalasi_nama, penunjang.ruangan_nama, penunjang.no_rekam_medik, penunjang.nama_pasien, penunjang.tgl_tindakan, penunjang.daftartindakan_nama, penunjang.tarif_satuan, penunjang.cyto_tindakan, penunjang.tarifcyto_tindakan, penunjang.qty_tindakan, penunjang.tarif_tindakan, penunjang.kelompoktindakan_id, penunjang.kelompoktindakan_nama, penunjang.tindakanpelayanan_id, penunjang.pasienmasukpenunjang_id, penunjang.ruangan_id, penunjang.obatalkespasien_id;

        ');

        $this->execute('
 ALTER TABLE infotagihanpenunjangdetail_v
  OWNER TO postgres;

        ');
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190507_024251_infotagihanpenunjangdetail_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190507_024251_infotagihanpenunjangdetail_v_update cannot be reverted.\n";

        return false;
    }
    */
}
