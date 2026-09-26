<?php

use yii\db\Migration;

/**
 * Class m190814_054526_pemberianobat_t
 */
class m190814_054526_pemberianobat_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.pemberianobat_v;');

        $this->execute('ALTER TABLE "public"."pemberianobat_t" 
                         ALTER COLUMN "diagnosa_id" TYPE text USING "diagnosa_id"::text;');

        $this->execute("CREATE OR REPLACE VIEW public.pemberianobat_v AS 
 SELECT pemberianobat_t.pemberianobat_id,
    pemberianobat_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    jk.lookup_name AS jenis_kelamin,
    pendaftaran_t.umur,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    kelaspelayanan_m.kelaspelayanan_nama,
    dok_dpjp.nama_pegawai AS dokter_dpjp,
    penjamin_m.penjamin_nama,
    pemberianobat_t.berat_badan,
    pemberianobat_t.tinggi_badan,
    pemberianobat_t.luas_tubuh,
    pemberianobat_t.is_hamil,
    pemberianobat_t.is_alergi,
    pemberianobat_t.diagnosa_id AS diagnosa_namalainnya,
    pemberianobat_t.dokterdpjp_id,
    pemberianobat_t.diagnosa_id,
    pemberianobat_t.diagnosa_id AS diagnosa_nama
   FROM pemberianobat_t
     JOIN pendaftaran_t ON pemberianobat_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN lookup_m jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN pegawai_m dok_dpjp ON pasienadmisi_t.pegawai_id = dok_dpjp.pegawai_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id;");


    $this->execute('ALTER TABLE public.pemberianobat_v
  OWNER TO postgres;');
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190814_054526_pemberianobat_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190814_054526_pemberianobat_t cannot be reverted.\n";

        return false;
    }
    */
}
