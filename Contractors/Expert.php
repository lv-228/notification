<?php

namespace NW\WebService\References\Operations\Notification\Contractors;

class Expert implements ContractorsInterface {

    protected $id;
    protected $name;
    protected $surName;

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

}