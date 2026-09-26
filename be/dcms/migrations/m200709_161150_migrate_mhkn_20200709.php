<?php

use yii\db\Migration;

/**
 * Class m200709_161150_migrate_mhkn_20200709
 */
class m200709_161150_migrate_mhkn_20200709 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->execute('DROP VIEW if exists "public"."infoclosingkasirheader_v";');

            $this->execute("
                CREATE VIEW \"public\".\"infoclosingkasirheader_v\" AS  SELECT closingkasir_t.closingkasir_id,
    closingkasir_t.tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.total_setoran,
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    closingkasir_t.closing_saldoawal,
    closingkasir_t.terima_uangpelayanan
   FROM (((((closingkasir_t
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
  WHERE (closingkasir_t.is_deleted = false);");

            $this->execute('DROP VIEW if exists "public"."rencanaoperasi_v";');

            $this->execute("
                CREATE VIEW \"public\".\"rencanaoperasi_v\" AS  SELECT rencanaoperasi_t.rencanaoperasi_id,
    rencanaoperasi_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.pendaftaran_id,
    rencanaoperasi_t.pasienadmisi_id,
    pasienkirimkeunitlain_t.no_orderkeunitlain,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.jeniskelamin,
    pasien_m.tanggal_lahir,
    pasien_m.photopasien,
    pendaftaran_t.umur,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    rencanaoperasi_t.ruangan_id,
    ruangan_m.ruangan_nama,
    rencanaoperasi_t.tgl_permintaan,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer) AS status,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dr_pemeriksa.nama_pegawai
            ELSE dr_pemeriksa_admisi.nama_pegawai
        END AS dok_pemeriksa,
    inpostoperasi_t.mulai_operasi,
    inpostoperasi_t.selesai_operasi
   FROM (((((((((((((((rencanaoperasi_t
     JOIN pasienkirimkeunitlain_t ON ((rencanaoperasi_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN pendaftaran_t ON ((rencanaoperasi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((rencanaoperasi_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
     LEFT JOIN pegawai_m dr_perujuk ON ((pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id)))
     LEFT JOIN pegawai_m dr_pemeriksa ON ((pendaftaran_t.pegawai_id = dr_pemeriksa.pegawai_id)))
     LEFT JOIN pegawai_m dr_pemeriksa_admisi ON ((pasienadmisi_t.pegawai_id = dr_pemeriksa_admisi.pegawai_id)))
     LEFT JOIN inpostoperasi_t ON ((inpostoperasi_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id)));");

            $this->execute('DROP VIEW if exists "public"."inforiwayatresep_v";');

            $this->execute("
                CREATE VIEW \"public\".\"inforiwayatresep_v\" AS  SELECT obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    obatalkespasien_t.tglpelayanan AS tgl_transaksi,
    penjualanresep_t.noresep AS no_resep,
    signaobat_m.signa_id,
    signaobat_m.signa_nama,
    obatalkespasien_t.qty_oa AS qty,
    satuan_kecil.satuanunit_id,
    satuan_kecil.satuanunit_nama,
    instalasi_m.instalasi_nama,
    ruangan_tujuan.ruangan_nama,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama
   FROM (((((((((((((obatalkespasien_t
     JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m satuan_kecil ON ((obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN ruangan_m ruangan_tujuan ON ((penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id)))
     LEFT JOIN instalasi_m ON ((ruangan_tujuan.instalasi_id = instalasi_m.instalasi_id)))
     JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
     LEFT JOIN penjamin_m ON ((obatalkespasien_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN carabayar_m ON ((obatalkespasien_t.carabayar_id = carabayar_m.carabayar_id)))
  WHERE ((obatalkespasien_t.is_deleted = false) AND (obatalkespasien_t.is_active = true) AND (penjualanresep_t.status_reseptur = 660));");

       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200709_161150_migrate_mhkn_20200709 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200709_161150_migrate_mhkn_20200709 cannot be reverted.\n";

        return false;
    }
    */
}
