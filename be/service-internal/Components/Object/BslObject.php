<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Components\Object;

class BslObject extends DocoBaseObject
{

    private $RegistrationDate;

    private $RegistrationNumber;

    private $BillDate;

    private $BillNumber;

    private $MRNumber;

    private $PatientName;

    private $Gender;

    private $DOB;

    private $IdCardNumber;

    private $Address;

    private $Email;

    private $PhoneNumber;

    private $LabOrderNumber;

    private $LabOrderDate;

    private $LabOrderDetailId;

    private $PcrServiceCode;

    private $IsDispatched;

    private $IsCanceled;

    private $KodePenjamin;

    private $NamaPenjamin;

    private $Instalasi;

    private $Ruangan;

    private $BillableBedType;

    private $PaymentMethod;

    private $ServiceItemAmountCash;

    private $ServiceItemAmountJaminan;

    private $UserIDKasir;

    private $TotalNetBillAmount;

    private $PrimaryDoctorCode;

    private $PrimaryDoctorName;

    private $PrimaryDoctorSpecialisation;

    private $ReffererDoctorCode;

    private $ReffererDoctorName;

    private $ReffererDoctorSpecialisation;

    /**
     * @return mixed
     */
    public function getRegistrationDate()
    {
        return $this->RegistrationDate;
    }

    /**
     * @param mixed $RegistrationDate
     *
     * @return self
     */
    public function setRegistrationDate($RegistrationDate)
    {
        $this->RegistrationDate = $RegistrationDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRegistrationNumber()
    {
        return $this->RegistrationNumber;
    }

    /**
     * @param mixed $RegistrationNumber
     *
     * @return self
     */
    public function setRegistrationNumber($RegistrationNumber)
    {
        $this->RegistrationNumber = $RegistrationNumber;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillDate()
    {
        return $this->BillDate;
    }

    /**
     * @param mixed $BillDate
     *
     * @return self
     */
    public function setBillDate($BillDate)
    {
        $this->BillDate = $BillDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillNumber()
    {
        return $this->BillNumber;
    }

    /**
     * @param mixed $BillNumber
     *
     * @return self
     */
    public function setBillNumber($BillNumber)
    {
        $this->BillNumber = $BillNumber;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMRNumber()
    {
        return $this->MRNumber;
    }

    /**
     * @param mixed $MRNumber
     *
     * @return self
     */
    public function setMRNumber($MRNumber)
    {
        $this->MRNumber = $MRNumber;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPatientName()
    {
        return $this->PatientName;
    }

    /**
     * @param mixed $PatientName
     *
     * @return self
     */
    public function setPatientName($PatientName)
    {
        $this->PatientName = $PatientName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->Gender;
    }

    /**
     * @param mixed $Gender
     *
     * @return self
     */
    public function setGender($Gender)
    {
        $this->Gender = $Gender;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDOB()
    {
        return $this->DOB;
    }

    /**
     * @param mixed $DOB
     *
     * @return self
     */
    public function setDOB($DOB)
    {
        $this->DOB = $DOB;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdCardNumber()
    {
        return $this->IdCardNumber;
    }

    /**
     * @param mixed $IdCardNumber
     *
     * @return self
     */
    public function setIdCardNumber($IdCardNumber)
    {
        $this->IdCardNumber = $IdCardNumber;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->Address;
    }

    /**
     * @param mixed $Address
     *
     * @return self
     */
    public function setAddress($Address)
    {
        $this->Address = $Address;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->Email;
    }

    /**
     * @param mixed $Email
     *
     * @return self
     */
    public function setEmail($Email)
    {
        $this->Email = $Email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->PhoneNumber;
    }

    /**
     * @param mixed $PhoneNumber
     *
     * @return self
     */
    public function setPhoneNumber($PhoneNumber)
    {
        $this->PhoneNumber = $PhoneNumber;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabOrderNumber()
    {
        return $this->LabOrderNumber;
    }

    /**
     * @param mixed $LabOrderNumber
     *
     * @return self
     */
    public function setLabOrderNumber($LabOrderNumber)
    {
        $this->LabOrderNumber = $LabOrderNumber;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabOrderDate()
    {
        return $this->LabOrderDate;
    }

    /**
     * @param mixed $LabOrderDate
     *
     * @return self
     */
    public function setLabOrderDate($LabOrderDate)
    {
        $this->LabOrderDate = $LabOrderDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabOrderDetailId()
    {
        return $this->LabOrderDetailId;
    }

    /**
     * @param mixed $LabOrderDetailId
     *
     * @return self
     */
    public function setLabOrderDetailId($LabOrderDetailId)
    {
        $this->LabOrderDetailId = $LabOrderDetailId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPcrServiceCode()
    {
        return $this->PcrServiceCode;
    }

    /**
     * @param mixed $PcrServiceCode
     *
     * @return self
     */
    public function setPcrServiceCode($PcrServiceCode)
    {
        $this->PcrServiceCode = $PcrServiceCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsDispatched()
    {
        return $this->IsDispatched;
    }

    /**
     * @param mixed $IsDispatched
     *
     * @return self
     */
    public function setIsDispatched($IsDispatched)
    {
        $this->IsDispatched = $IsDispatched;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsCanceled()
    {
        return $this->IsCanceled;
    }

    /**
     * @param mixed $IsCanceled
     *
     * @return self
     */
    public function setIsCanceled($IsCanceled)
    {
        $this->IsCanceled = $IsCanceled;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getKodePenjamin()
    {
        return $this->KodePenjamin;
    }

    /**
     * @param mixed $KodePenjamin
     *
     * @return self
     */
    public function setKodePenjamin($KodePenjamin)
    {
        $this->KodePenjamin = $KodePenjamin;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNamaPenjamin()
    {
        return $this->NamaPenjamin;
    }

    /**
     * @param mixed $NamaPenjamin
     *
     * @return self
     */
    public function setNamaPenjamin($NamaPenjamin)
    {
        $this->NamaPenjamin = $NamaPenjamin;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInstalasi()
    {
        return $this->Instalasi;
    }

    /**
     * @param mixed $Instalasi
     *
     * @return self
     */
    public function setInstalasi($Instalasi)
    {
        $this->Instalasi = $Instalasi;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRuangan()
    {
        return $this->Ruangan;
    }

    /**
     * @param mixed $Ruangan
     *
     * @return self
     */
    public function setRuangan($Ruangan)
    {
        $this->Ruangan = $Ruangan;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillableBedType()
    {
        return $this->BillableBedType;
    }

    /**
     * @param mixed $BillableBedType
     *
     * @return self
     */
    public function setBillableBedType($BillableBedType)
    {
        $this->BillableBedType = $BillableBedType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->PaymentMethod;
    }

    /**
     * @param mixed $PaymentMethod
     *
     * @return self
     */
    public function setPaymentMethod($PaymentMethod)
    {
        $this->PaymentMethod = $PaymentMethod;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getServiceItemAmountCash()
    {
        return $this->ServiceItemAmountCash;
    }

    /**
     * @param mixed $ServiceItemAmountCash
     *
     * @return self
     */
    public function setServiceItemAmountCash($ServiceItemAmountCash)
    {
        $this->ServiceItemAmountCash = $ServiceItemAmountCash;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getServiceItemAmountJaminan()
    {
        return $this->ServiceItemAmountJaminan;
    }

    /**
     * @param mixed $ServiceItemAmountJaminan
     *
     * @return self
     */
    public function setServiceItemAmountJaminan($ServiceItemAmountJaminan)
    {
        $this->ServiceItemAmountJaminan = $ServiceItemAmountJaminan;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserIDKasir()
    {
        return $this->UserIDKasir;
    }

    /**
     * @param mixed $UserIDKasir
     *
     * @return self
     */
    public function setUserIDKasir($UserIDKasir)
    {
        $this->UserIDKasir = $UserIDKasir;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalNetBillAmount()
    {
        return $this->TotalNetBillAmount;
    }

    /**
     * @param mixed $TotalNetBillAmount
     *
     * @return self
     */
    public function setTotalNetBillAmount($TotalNetBillAmount)
    {
        $this->TotalNetBillAmount = $TotalNetBillAmount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrimaryDoctorCode()
    {
        return $this->PrimaryDoctorCode;
    }

    /**
     * @param mixed $PrimaryDoctorCode
     *
     * @return self
     */
    public function setPrimaryDoctorCode($PrimaryDoctorCode)
    {
        $this->PrimaryDoctorCode = $PrimaryDoctorCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrimaryDoctorName()
    {
        return $this->PrimaryDoctorName;
    }

    /**
     * @param mixed $PrimaryDoctorName
     *
     * @return self
     */
    public function setPrimaryDoctorName($PrimaryDoctorName)
    {
        $this->PrimaryDoctorName = $PrimaryDoctorName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrimaryDoctorSpecialisation()
    {
        return $this->PrimaryDoctorSpecialisation;
    }

    /**
     * @param mixed $PrimaryDoctorSpecialisation
     *
     * @return self
     */
    public function setPrimaryDoctorSpecialisation($PrimaryDoctorSpecialisation)
    {
        $this->PrimaryDoctorSpecialisation = $PrimaryDoctorSpecialisation;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReffererDoctorCode()
    {
        return $this->ReffererDoctorCode;
    }

    /**
     * @param mixed $ReffererDoctorCode
     *
     * @return self
     */
    public function setReffererDoctorCode($ReffererDoctorCode)
    {
        $this->ReffererDoctorCode = $ReffererDoctorCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReffererDoctorName()
    {
        return $this->ReffererDoctorName;
    }

    /**
     * @param mixed $ReffererDoctorName
     *
     * @return self
     */
    public function setReffererDoctorName($ReffererDoctorName)
    {
        $this->ReffererDoctorName = $ReffererDoctorName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReffererDoctorSpecialisation()
    {
        return $this->ReffererDoctorSpecialisation;
    }

    /**
     * @param mixed $ReffererDoctorSpecialisation
     *
     * @return self
     */
    public function setReffererDoctorSpecialisation($ReffererDoctorSpecialisation)
    {
        $this->ReffererDoctorSpecialisation = $ReffererDoctorSpecialisation;

        return $this;
    }

    public function buildArray()
    {
        return [
            'RegistrationDate' => $this->getRegistrationDate(),
            'RegistrationNumber' => $this->getRegistrationNumber(),
            'BillDate' => $this->getBillDate(),
            'BillNumber' => $this->getBillNumber(),
            'MRNumber' => $this->getMRNumber(),
            'PatientName' => $this->getPatientName(),
            'Gender' => $this->getGender(),
            'DOB' => $this->getDOB(),
            'IdCardNumber' => $this->getIdCardNumber(),
            'Address' => $this->getAddress(),
            'Email' => $this->getEmail(),
            'PhoneNumber' => $this->getPhoneNumber(),
            'LabOrderNumber' => $this->getLabOrderNumber(),
            'LabOrderDate' => $this->getLabOrderDate(),
            'LabOrderDetailId' => $this->getLabOrderDetailId(),
            'PcrServiceCode' => $this->getPcrServiceCode(),
            'IsDispatched' => $this->getIsDispatched(),
            'IsCanceled' => $this->getIsCanceled(),
            'KodePenjamin' => $this->getKodePenjamin(),
            'NamaPenjamin' => $this->getNamaPenjamin(),
            'Instalasi' => $this->getInstalasi(),
            'Ruangan' => $this->getRuangan(),
            'BillableBedType' => $this->getBillableBedType(),
            'PaymentMethod' => $this->getPaymentMethod(),
            'ServiceItemAmountCash' => $this->getServiceItemAmountCash(),
            'ServiceItemAmountJaminan' => $this->getServiceItemAmountJaminan(),
            'UserIDKasir' => $this->getUserIDKasir(),
            'TotalNetBillAmount' => $this->getTotalNetBillAmount(),
            'PrimaryDoctorCode' => $this->getPrimaryDoctorCode(),
            'PrimaryDoctorName' => $this->getPrimaryDoctorName(),
            'PrimaryDoctorSpecialisation' => $this->getPrimaryDoctorSpecialisation(),
            'ReffererDoctorCode' => $this->getReffererDoctorCode(),
            'ReffererDoctorName' => $this->getReffererDoctorName(),
            'ReffererDoctorSpecialisation' => $this->getReffererDoctorSpecialisation(),
        ];
    }
}