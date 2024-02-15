<?php

namespace NW\WebService\References\Operations\Notification\Contractors;

class Reseller implements ContractorsInterface {

    protected $id;
    protected $name;
    protected $surName;
    protected $email;

    private function __construct(){};

    public static function getById($id): self 
    {
        return new self($id);
    }

    public function getFullName(): string 
    {
        return $this->name . ' ' . $this->surName;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmailsByStatus(string $status): array 
    {
        return ['someemeil@example.com', 'someemeil2@example.com'];
    }

    public static function getEmail(): string
    {
        return $this->email;
    }

    public function getComplaintEmailSubject(): string
    {
        return 'complaintResellerEmailSubject';
    }

    public function getComplaintEmailBody(): string
    {
        return 'complaintResellerEmailBody';
    }

}