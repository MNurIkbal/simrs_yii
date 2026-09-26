<?php

use yii\db\Migration;

/**
 * Class m190912_034831_soaprj_t_typedata
 */
class m190912_034831_soaprj_t_typedata extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists public.cpptrjdetail_v;');

         $this->execute('DROP VIEW if exists public.cpptrj_v;');

          $this->execute('ALTER TABLE "public"."soaprj_t" 
                          ALTER COLUMN "a_diag_utama" TYPE json USING "a_diag_utama"::json,
                          ALTER COLUMN "a_diag_penyerta" TYPE json USING "a_diag_penyerta"::json;
                        ');

           $this->execute("
            CREATE OR REPLACE VIEW public.cpptrjdetail_v AS 
 SELECT 'NON_PAKET'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    soaprj_t.ruangan_id,
    ruangan_m.ruangan_nama,
    soaprj_t.pegawai_id,
    pegawai_m.nama_pegawai,
    kelompokpegawai_m.kelompokpegawai_nama,
    soaprj_t.subject,
    soaprj_t.object,
    soaprj_t.a_diag_utama,
    soaprj_t.a_diag_penyerta,
    tindakanpelayanan_t.tgl_tindakan,
    daftartindakan_m.daftartindakan_nama AS instruksi,
    soaprj_t.soaprj_id,
    soaprj_t.tgl_soaprj,
    soaprj_t.planning
   FROM pendaftaran_t
     JOIN soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
     JOIN ruangan_m ON soaprj_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON soaprj_t.pegawai_id = pegawai_m.pegawai_id
     JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
UNION ALL
 SELECT 'PAKET'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    soaprj_t.ruangan_id,
    ruangan_m.ruangan_nama,
    soaprj_t.pegawai_id,
    pegawai_m.nama_pegawai,
    kelompokpegawai_m.kelompokpegawai_nama,
    soaprj_t.subject,
    soaprj_t.object,
    soaprj_t.a_diag_utama,
    soaprj_t.a_diag_penyerta,
    tindakanpelayanan_t.tgl_tindakan,
    concat(tipepaket_m.tipepaket_nama, '-', daftartindakan_m.daftartindakan_nama) AS instruksi,
    soaprj_t.soaprj_id,
    soaprj_t.tgl_soaprj,
    soaprj_t.planning
   FROM pendaftaran_t
     JOIN soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
     JOIN ruangan_m ON soaprj_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON soaprj_t.pegawai_id = pegawai_m.pegawai_id
     JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
UNION ALL
 SELECT 'OBAT'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    soaprj_t.ruangan_id,
    ruangan_m.ruangan_nama,
    soaprj_t.pegawai_id,
    pegawai_m.nama_pegawai,
    kelompokpegawai_m.kelompokpegawai_nama,
    soaprj_t.subject,
    soaprj_t.object,
    soaprj_t.a_diag_utama,
    soaprj_t.a_diag_penyerta,
    obatalkespasien_t.tglpelayanan AS tgl_tindakan,
    obatalkes_m.obatalkes_nama AS instruksi,
    soaprj_t.soaprj_id,
    soaprj_t.tgl_soaprj,
    soaprj_t.planning
   FROM pendaftaran_t
     JOIN soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
     JOIN ruangan_m ON soaprj_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON soaprj_t.pegawai_id = pegawai_m.pegawai_id
     JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id;");

            $this->execute('ALTER TABLE public.cpptrjdetail_v
            OWNER TO postgres;');

             $this->execute("
CREATE OR REPLACE VIEW public.cpptrj_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    soaprj_t.ruangan_id,
    ruangan_m.ruangan_nama,
    soaprj_t.pegawai_id,
    pegawai_m.nama_pegawai,
    kelompokpegawai_m.kelompokpegawai_nama,
    soaprj_t.subject,
    soaprj_t.object,
    soaprj_t.a_diag_utama,
    soaprj_t.a_diag_penyerta,
    soaprj_t.planning,
    soaprj_t.tgl_soaprj,
    soaprj_t.td_diastolic,
    soaprj_t.td_systolic,
    soaprj_t.pernapasan,
    soaprj_t.beratbadan_kg,
    soaprj_t.tinggibadan_cm,
    soaprj_t.imt,
    soaprj_t.detaknadi,
    soaprj_t.suhutubuh,
    soaprj_t.is_nyeri,
    soaprj_t.skala_nyeri,
    soaprj_t.is_resikojatuh,
    soaprj_t.soaprj_id,
    soaprj_t.pasien_id,
    soaprj_t.catatan_dokter
   FROM pendaftaran_t
     JOIN soaprj_t ON pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id
     JOIN ruangan_m ON soaprj_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON soaprj_t.pegawai_id = pegawai_m.pegawai_id
     JOIN kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id;");

              $this->execute('ALTER TABLE public.cpptrj_v
                OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190912_034831_soaprj_t_typedata cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190912_034831_soaprj_t_typedata cannot be reverted.\n";

        return false;
    }
    */
}
