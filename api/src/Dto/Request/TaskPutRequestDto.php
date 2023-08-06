<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class TaskPutRequestDto
{
    #[Assert\Range(min: 1)]
    private $id;

    #[Assert\NotBlank]
    #[Assert\Length(
        max: 255,
        maxMessage: "This value cannot be longer than {{ limit }} characters!",
    )]
    private string $name;

    #[Assert\NotBlank]
    private string $description;

    #[Assert\NotBlank]
    #[Assert\DateTime(format: "Y-m-d H:i")]
    private string $deadline;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDeadline(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->deadline);
    }

    /**
     * @param string $deadline
     */
    public function setDeadline(string $deadline): void
    {
        $this->deadline = $deadline;
    }
}