<?php

use yii\db\Migration;

/**
 * Class m200123_072050_infopemberianpiutang_v
 */
class m200123_072050_infopemberianpiutang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopemberianpiutang_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopemberianpiutang_v AS 
 SELECT pemberianpiutang_t.pemberianpiutang_id,
    pemberianpiutang_t.no_pemberianpiutang,
    pemberianpiutang_t.tgl_pemberianpiutang,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    tagihan.total_tagihan AS tagihan,
    pemberianpiutang_t.total_piutang,
    pemberianpiutang_t.total_sisapiutang,
    pemberianpiutang_t.total_bayarpiutang,
    pemberianpiutang_t.status_piutang,
    fgetnamalookup(pemberianpiutang_t.status_piutang::integer) AS status_piutang_nama,
    pemberianpiutang_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pemberianpiutang_t.catatan,
    pasien_m.tanggal_lahir,
    pendaftaran_t.tgl_pendaftaran
   FROM pemberianpiutang_t
     JOIN pendaftaran_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pemberianpiutang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT gabung.pendaftaran_id,
            (COALESCE(sum(gabung.total_tindakan::integer)::double precision, 0::double precision) + COALESCE(sum(gabung.total_obat::integer)::double precision, 0::double precision))::integer AS total_tagihan
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan,
                    NULL::double precision AS total_obat,
                    tindakanpelayanan_t.tindakansudahbayar_id AS sudah_bayar
                   FROM pendaftaran_t pendaftaran_t_1
                     LEFT JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                     LEFT JOIN pasienadmisi_t ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
                  WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL
                  GROUP BY pendaftaran_t_1.pendaftaran_id, tindakanpelayanan_t.tindakansudahbayar_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    NULL::double precision AS total_tindakan,
                    sum(obatalkespasien_t.hargajual_oa) AS total_obat,
                    obatalkespasien_t.obatsudahbayar_id
                   FROM pendaftaran_t pendaftaran_t_1
                     LEFT JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                     LEFT JOIN pasienadmisi_t ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
                  WHERE obatalkespasien_t.obatsudahbayar_id IS NULL
                  GROUP BY pendaftaran_t_1.pendaftaran_id, obatalkespasien_t.obatsudahbayar_id) gabung
             LEFT JOIN pemberianpiutang_t pemberianpiutang_t_1 ON gabung.pendaftaran_id = pemberianpiutang_t_1.pendaftaran_id AND pemberianpiutang_t_1.is_deleted = false
          WHERE gabung.sudah_bayar IS NULL
          GROUP BY gabung.pendaftaran_id) tagihan ON pemberianpiutang_t.pendaftaran_id = tagihan.pendaftaran_id
  WHERE pemberianpiutang_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.infopemberianpiutang_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200123_072050_infopemberianpiutang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200123_072050_infopemberianpiutang_v cannot be reverted.\n";

        return false;
    }
    */
}
