<?php

namespace Contractors;

abstract class Contractors {

    protected $id;
    protected $name;
    protected $surName;

    protected function __construct() {}

    public function getId(): int {
        return $this->id;
    }

    public function getFullName(): string {
        return $this->name . ' ' . $this->surName;
    }

    abstract public static function getById(int $id): self;
    
}