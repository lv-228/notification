<?php

namespace NW\WebService\References\Operations\Notification\Contractors;

class ContractorsFactory {

    public function getResellerById($id): Reseller 
    {
        return Reseller::getById($id);
    }

    public function getClientById($id): Client 
    {
        return Client::getById($id);
    }

    public function getCreatorById($id): Creator 
    {
        return Creator::getById($id);
    }

    public function getExpertById($id): Expert 
    {
        return Expert::getById($id);
    }
    
}