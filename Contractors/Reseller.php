<?php

namespace Contractors;

class Reseller extends Contractors {

    public static function getById(int $id): self {
        return new self($id);
    }

    public function getResellerEmailsByEvent(int $id, string $event): array 
    {
        return ['someemeil@example.com', 'someemeil2@example.com'];
    }

    public static function getResellerEmailById(int $id): string
    {
        return 'contractor@example.com';
    }

    public function getComplaintEmailSubject(): string
    {
        return 'complaintEmployeeEmailSubject';
    }

    public function getComplaintEmailBody(): string
    {
        return 'complaintEmployeeEmailBody';
    }

}