<?php

namespace Contractors;

class Client extends Contractors {

    protected $email;
    protected $mobile;

    public static function getById($id): self 
    {
        return new self($id);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function getComplaintEmailSubject(): string
    {
        return 'complaintClientEmailSubject';
    }

    public function getComplaintEmailBody(): string
    {
        return 'complaintClientEmailBody';
    }

}