<?php

use yii\db\Migration;

/**
 * Class m190430_075405_infopenjualanresep_v_update
 */
class m190430_075405_infopenjualanresep_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
         DROP VIEW infopenjualanresep_v;
        ');

        $this->execute('
          CREATE OR REPLACE VIEW infopenjualanresep_v AS 
 SELECT penjualanresep_t.penjualanresep_id,
    penjualanresep_t.jenispenjualan,
    jenispenjualan.lookup_name AS jenis_penjualan,
    penjualanresep_t.tglresep,
    penjualanresep_t.noresep,
    penjualanresep_t.totharganetto,
    penjualanresep_t.totalhargajual,
    penjualanresep_t.totaltarifservice,
    penjualanresep_t.biayaadministrasi,
    penjualanresep_t.biayakonseling,
    penjualanresep_t.pembulatanharga,
    penjualanresep_t.jasadokterresep,
    penjualanresep_t.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    penjualanresep_t.penjamin_id,
    penjualanresep_t.tglpenjualan,
    penjualanresep_t.discount,
    penjualanresep_t.subsidiasuransi,
    penjualanresep_t.subsidipemerintah,
    penjualanresep_t.subsidirs,
    penjualanresep_t.iurbiaya,
    penjualanresep_t.lamapelayanan,
    penjualanresep_t.pasienadmisi_id,
    penjualanresep_t.reseptur_id,
    penjualanresep_t.nama_pembeli,
    penjualanresep_t.pendaftaran_id,
    penjualanresep_t.pasien_id,
    penjualanresep_t.pegawai_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.nama_pasien,
    pegawai_m.nama_pegawai,
    penjualanresep_t.ruangan_id,
    penjualanresep_t.karyawan_id,
    karyawan.nama_pegawai AS nama_karyawan,
    reseptur.antrian_id,
    antrian.no_antrian,
    peg_reseptur.nama_pegawai AS pegawai_reseptur,
    penjualanresep_t.catatan,
    fgetstatuspembayaranresep(penjualanresep_t.penjualanresep_id) AS status_pembayaran,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    penjualanresep_t.iter
   FROM penjualanresep_t
     JOIN lookup_m jenispenjualan ON penjualanresep_t.jenispenjualan::integer = jenispenjualan.lookup_id
     JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pegawai_m karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
     LEFT JOIN reseptur_t reseptur ON penjualanresep_t.reseptur_id = reseptur.reseptur_id
     LEFT JOIN antrian_t antrian ON reseptur.antrian_id = antrian.antrian_id
     LEFT JOIN pegawai_m peg_reseptur ON reseptur.pegawai_id = peg_reseptur.pegawai_id
     LEFT JOIN ruangan_m ON reseptur.ruanganreseptur_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE penjualanresep_t.is_active = true AND penjualanresep_t.is_deleted = false;
        ');

        $this->execute('
           ALTER TABLE infopenjualanresep_v
            OWNER TO postgres;
        ');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190430_075405_infopenjualanresep_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190430_075405_infopenjualanresep_v_update cannot be reverted.\n";

        return false;
    }
    */
}
