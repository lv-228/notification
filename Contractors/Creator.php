<?php

namespace Contractors;

class Creator extends Contractors {

    public static function getById(int $id): self {
        return new self($id);
    }

}