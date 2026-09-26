<?php
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Components\Object;

use Integrasi\Components\DocoConstants;

class RekapRisObject extends DocoBaseObject
{

    private $pendaftaran_id;

    private $pasienmasukpenunjang_id;

    private $tindakanpelayanan_id;

    private $daftartindakan_id;

    private $no_pembayaran;

    private $payload;

    private $id_sync_sercon;

    private $is_sending;

    private $sync_respon;

    /**
     * @return mixed
     */
    public function getPendaftaranId()
    {
        return $this->pendaftaran_id;
    }

    /**
     * @param mixed $pendaftaran_id
     *
     * @return self
     */
    public function setPendaftaranId($pendaftaran_id)
    {
        $this->pendaftaran_id = $pendaftaran_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPasienmasukpenunjangId()
    {
        return $this->pasienmasukpenunjang_id;
    }

    /**
     * @param mixed $pasienmasukpenunjang_id
     *
     * @return self
     */
    public function setPasienmasukpenunjangId($pasienmasukpenunjang_id)
    {
        $this->pasienmasukpenunjang_id = $pasienmasukpenunjang_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTindakanpelayananId()
    {
        return $this->tindakanpelayanan_id;
    }

    /**
     * @param mixed $tindakanpelayanan_id
     *
     * @return self
     */
    public function setTindakanpelayananId($tindakanpelayanan_id)
    {
        $this->tindakanpelayanan_id = $tindakanpelayanan_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDaftartindakanId()
    {
        return $this->daftartindakan_id;
    }

    /**
     * @param mixed $daftartindakan_id
     *
     * @return self
     */
    public function setDaftartindakanId($daftartindakan_id)
    {
        $this->daftartindakan_id = $daftartindakan_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNoPembayaran()
    {
        return $this->no_pembayaran;
    }

    /**
     * @param mixed $no_pembayaran
     *
     * @return self
     */
    public function setNoPembayaran($no_pembayaran)
    {
        $this->no_pembayaran = $no_pembayaran;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     *
     * @return self
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdSyncSercon()
    {
        return $this->id_sync_sercon;
    }

    /**
     * @param mixed $id_sync_sercon
     *
     * @return self
     */
    public function setIdSyncSercon($id_sync_sercon)
    {
        $this->id_sync_sercon = $id_sync_sercon;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsSending()
    {
        return $this->is_sending;
    }

    /**
     * @param mixed $is_sending
     *
     * @return self
     */
    public function setIsSending($is_sending)
    {
        $this->is_sending = $is_sending;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSyncRespon()
    {
        return $this->sync_respon;
    }

    /**
     * @param mixed $sync_respon
     *
     * @return self
     */
    public function setSyncRespon($sync_respon)
    {
        $this->sync_respon = $sync_respon;

        return $this;
    }

    /**
     * @return array
     */
    public function buildArray()
    {
        return [
            'pendaftaran_id' => $this->pendaftaran_id,
            'pasienmasukpenunjang_id' => $this->pasienmasukpenunjang_id,
            'tindakanpelayanan_id' => $this->tindakanpelayanan_id,
            'daftartindakan_id' => $this->daftartindakan_id,
            'no_pembayaran' => $this->no_pembayaran,
            'payload' => $this->payload,
            'id_sync_sercon' => $this->id_sync_sercon,
            'is_sending' => $this->is_sending,
            'sync_respon' => $this->sync_respon,
        ];
    }
}