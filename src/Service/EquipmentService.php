<?php

namespace App\Service;

use App\Entity\Equipment;
use App\Repository\EquipmentRepository;
use App\Exception\EquipmentNotFoundException;

class EquipmentService
{
    private EquipmentRepository $equipmentRepository;

    public function __construct(EquipmentRepository $equipmentRepository)
    {
        $this->equipmentRepository = $equipmentRepository;
    }

    public function getEquipmentOrThrowException(Equipment $equipment): Equipment
    {
        $equipmentId = $equipment->getId();

        if (!$equipmentId || !$this->equipmentRepository->find($equipmentId)) {
            throw new EquipmentNotFoundException("Equipment with ID $equipmentId not found.");
        }

        return $equipment;
    }

    public function handleBadRequest(): array
    {
        return ['error' => 'Bad request'];
    }

    public function handleNotFound(): array
    {
        return ['error' => 'Equipment not found'];
    }

    public function handleUpdateError(): array
    {
        return ['error' => 'Unable to update equipment'];
    }
}
