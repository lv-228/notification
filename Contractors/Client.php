<?php

namespace NW\WebService\References\Operations\Notification\Contractors;

class Client implements ContractorsInterface {

    protected $id;
    protected $name;
    protected $surName;
    protected $email;
    protected $mobile;

    private function __construct(){};

    public static function getById($id): self 
    {
        return new self($id);
    }

    public function getFullName(): string 
    {
        return $this->name . ' ' . $this->surName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getId(): int
    {
        return $this->id;
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