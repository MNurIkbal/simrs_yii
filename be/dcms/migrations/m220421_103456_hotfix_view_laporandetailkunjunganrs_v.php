<?php

use yii\db\Migration;

/**
 * Class m220421_103456_hotfix_view_laporandetailkunjunganrs_v
 */
class m220421_103456_hotfix_view_laporandetailkunjunganrs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporandetailkunjunganrs_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporandetailkunjunganrs_v" AS  SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.tgl_pendaftaran AS tgl_masuk,
                COALESCE(pulang_ri.tglpasienpulang, pendaftaran_t.tgl_stopakomodasi, pulang_rj.tglpasienpulang, pendaftaran_t.tgl_pendaftaran) AS tgl_keluar,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pembayaran.tgl_pembayaran,
                COALESCE(pembayaran.no_pembayaran, pembayaranpelayanan.no_pembayaran) AS no_pembayaran,
                COALESCE(ruangan_ri.ruangan_nama, ruangan_rjrd.ruangan_nama) AS ruangan,
                COALESCE(instalasi_ri.instalasi_nama, instalasi_rjrd.instalasi_nama) AS instalasi,
                COALESCE(penjamin_ri.penjamin_nama, penjamin_rjrd.penjamin_nama) AS penjamin,
                COALESCE(carabayar_ri.carabayar_nama, carabayar_rjrd.carabayar_nama) AS cara_bayar,
                pembayaran.total_tagihan,
                pembayaran.total_dijamin, 
                pembayaran.total_dibayar
               FROM ((((((((((((((pendaftaran_t
                 JOIN ( SELECT a.pasien_id,
                        a.no_rekam_medik,
                        a.nama_pasien
                       FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN ( SELECT a.pendaftaran_id,
                        a.created_date AS tgl_pembayaran,
                        a.no_pembayaran,
                        ((a.total_tagihan + COALESCE(a.total_administrasi, (0)::double precision)) - a.total_discountpembayaran) AS total_tagihan,
                        a.total_dijamin,
                        a.total_dibayar,
                        a.pembayaran_id
                       FROM pembayaran_t a
                      WHERE (a.is_deleted = false)) pembayaran ON ((pendaftaran_t.pendaftaran_id = pembayaran.pendaftaran_id)))
                 JOIN ( SELECT pembayaranpelayanan_t.pembayaran_id,
                        pembayaranpelayanan_t.no_pembayaran
                       FROM pembayaranpelayanan_t) pembayaranpelayanan ON ((pembayaran.pembayaran_id = pembayaranpelayanan.pembayaran_id)))
                 LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.penjamin_id,
                        a.ruangan_id,
                        a.pasienpulang_id
                       FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 LEFT JOIN pasienpulang_t pulang_rj ON ((pendaftaran_t.pasienpulang_id = pulang_rj.pasienpulang_id)))
                 LEFT JOIN pasienpulang_t pulang_ri ON ((pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id)))
                 LEFT JOIN ruangan_m ruangan_rjrd ON ((pendaftaran_t.ruangan_id = ruangan_rjrd.ruangan_id)))
                 LEFT JOIN ruangan_m ruangan_ri ON ((pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id)))
                 LEFT JOIN instalasi_m instalasi_rjrd ON ((ruangan_rjrd.instalasi_id = instalasi_rjrd.instalasi_id)))
                 LEFT JOIN instalasi_m instalasi_ri ON ((ruangan_ri.instalasi_id = instalasi_ri.instalasi_id)))
                 LEFT JOIN penjamin_m penjamin_rjrd ON ((pendaftaran_t.penjamin_id = penjamin_rjrd.penjamin_id)))
                 LEFT JOIN penjamin_m penjamin_ri ON ((pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id)))
                 LEFT JOIN carabayar_m carabayar_rjrd ON ((penjamin_rjrd.carabayar_id = carabayar_rjrd.carabayar_id)))
                 LEFT JOIN carabayar_m carabayar_ri ON ((penjamin_ri.carabayar_id = carabayar_ri.carabayar_id)));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220421_103456_hotfix_view_laporandetailkunjunganrs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220421_103456_hotfix_view_laporandetailkunjunganrs_v cannot be reverted.\n";

        return false;
    }
    */
}
