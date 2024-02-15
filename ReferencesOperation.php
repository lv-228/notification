<?php

namespace NW\WebService\References\Operations\Notification;

abstract class ReferencesOperation
{
    abstract public function doOperation(): array;

    public function getRequest($pName)
    {
        if($_REQUEST[$pName] === null) {
            return null;
        }
        $result = [];
        if(is_array($_REQUEST[$pName])) {
            foreach ($_REQUEST[$pName] as $key => $data_elem) {
                $result[$key] = is_string($data_elem) ? preg_replace('~[\x00\x0A\x0D\x1A\x22\x27\x5C]~u', '\\\$0', $data_elem) : $data_elem;
            }
        } else if (is_string($_REQUEST[$pName])) {
            $result[] = preg_replace('~[\x00\x0A\x0D\x1A\x22\x27\x5C]~u', '\\\$0', $_REQUEST[$pName]);
        }
        return $result;
    }
}