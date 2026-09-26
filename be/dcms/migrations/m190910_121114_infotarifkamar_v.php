<?php

use yii\db\Migration;

/**
 * Class m190910_121114_infotarifkamar_v
 */
class m190910_121114_infotarifkamar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists public.infotarifkamar_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.infotarifkamar_v AS 
 SELECT tindakanruangan_mp.ruangan_id,
    r_tindakan.ruangan_nama,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kamar.jeniskasuspenyakit_id,
    kamar.jeniskasuspenyakit_nama,
    kamar.kamarruangan_id,
    tariftindakan_m.penjamin_id,
    kamar.kamarruangan_jenis,
    kamar.kamarruangan_nokamar,
    kamar.kamartempattidur_id,
    kamar.no_tempattidur,
    kamar.kettempattidur_id,
    kamar.status_isi,
    kamar.kode_warna,
    daftartindakan_m.is_akomodasi,
    ( SELECT pasien_m.jeniskelamin
           FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
          WHERE pasienadmisi_t.kamarruangan_id = kamar.kamarruangan_id AND pasienadmisi_t.pasienpulang_id IS NULL AND (pasienadmisi_t.status_ranap = ANY (ARRAY[440, 441]))
         LIMIT 1) AS isi_jk
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
     JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id AND tindakanruangan_mp.is_deleted = false AND tindakanruangan_mp.is_active = true
     JOIN ruangan_m r_tindakan ON tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id AND r_tindakan.is_deleted = false AND r_tindakan.is_active = true
     JOIN ( SELECT kamarruangan_m.ruangan_id,
            kamarruangan_m.kamarruangan_id,
            kamarruangan_m.kelaspelayanan_id,
            kamarruangan_m.kamarruangan_nokamar,
            kamarruangan_m.kamarruangan_jenis,
            kamartempattidur_m.kamartempattidur_id,
            kamartempattidur_m.no_tempattidur,
            kamartempattidur_m.status_isi,
            kamartempattidur_m.kettempattidur_id,
            kettempattidur_m.kode_warna,
            kamarruangan_m.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama
           FROM kamarruangan_m
             JOIN kamartempattidur_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id AND kamartempattidur_m.is_deleted = false AND kamartempattidur_m.is_active = true
             JOIN kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id
             JOIN jeniskasuspenyakit_m ON kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
          WHERE kamarruangan_m.is_deleted = false AND kamarruangan_m.is_active = true) kamar ON tindakanruangan_mp.ruangan_id = kamar.ruangan_id AND tariftindakan_m.kelaspelayanan_id = kamar.kelaspelayanan_id
  WHERE tindakanruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true AND tariftindakan_m.komponentarif_id = 6
  GROUP BY tindakanruangan_mp.ruangan_id, r_tindakan.ruangan_nama, tariftindakan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, kamar.jeniskasuspenyakit_id, kamar.jeniskasuspenyakit_nama, kamar.kamarruangan_id, tariftindakan_m.penjamin_id, kamar.kamarruangan_jenis, kamar.kamarruangan_nokamar, kamar.kamartempattidur_id, kamar.no_tempattidur, kamar.kettempattidur_id, kamar.status_isi, kamar.kode_warna, daftartindakan_m.is_akomodasi, (( SELECT pasien_m.jeniskelamin
           FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
          WHERE pasienadmisi_t.kamarruangan_id = kamar.kamarruangan_id AND pasienadmisi_t.pasienpulang_id IS NULL AND (pasienadmisi_t.status_ranap = ANY (ARRAY[440, 441]))
         LIMIT 1));");

         $this->execute('ALTER TABLE public.infotarifkamar_v
  OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190910_121114_infotarifkamar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190910_121114_infotarifkamar_v cannot be reverted.\n";

        return false;
    }
    */
}
