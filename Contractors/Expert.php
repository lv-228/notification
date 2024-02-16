<?php

namespace Contractors;

class Expert extends Contractors {

    public static function getById(int $id): self {
        return new self($id);
    }

}