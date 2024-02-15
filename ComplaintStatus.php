<?php

namespace NW\WebService\References\Operations\Notification;

class ComplaintStatus {

    const NEW    = 'newReturnStatus';
    const CHANGE = 'changeReturnStatus';
    const byType = [
        1 => self::NEW,
        2 => self::CHANGE;
    ];

}