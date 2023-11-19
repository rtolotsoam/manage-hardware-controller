<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EquipmentNotFoundException extends NotFoundHttpException
{
    public function __construct(string $message = 'Equipment not found.')
    {
        parent::__construct($message);
    }
}
