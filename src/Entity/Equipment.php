<?php
namespace App\Entity;

use App\Repository\EquipmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Equipment",
 *     type="object",
 *     required={"name", "category", "number", "description"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Equipment Name"),
 *     @OA\Property(property="category", type="string", example="Equipment Category"),
 *     @OA\Property(property="number", type="string", example="E12345"),
 *     @OA\Property(property="description", type="string", example="Equipment description"),
 *     @OA\Property(property="createdAt", type="string", format="date-time", example="2022-01-01T12:00:00+00:00"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time", example="2022-01-02T12:30:00+00:00"),
 * )
 */
#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getEquipments'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Length(min: 3)]
    #[Groups(['getEquipments', 'setEquipments'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['getEquipments', 'setEquipments'])]
    private ?string $category = null;

    #[ORM\Column(length: 255)]
    #[Length(min: 2)]
    #[Groups(['getEquipments', 'setEquipments'])]
    private ?string $number = null;

    #[ORM\Column(type: Types::TEXT, length: 65535, options: ['default' => ''])]
    #[Length(max: 65535)]
    #[Groups(['getEquipments', 'setEquipments'])]
    private ?string $description = '';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['getEquipments'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['getEquipments'])]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime;
        }
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt() : void
    {
        $this->updatedAt = new \DateTime;
    }
}
