<?php

namespace NW\WebService\References\Operations\Notification\Contractors;

interface ContractorsInterface {

    public static function getById(int $id): self;

    public function getFullName(): string;

    public function getId(): int;
}