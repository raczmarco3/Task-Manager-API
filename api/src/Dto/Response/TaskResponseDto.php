<?php

namespace App\Dto\Response;

class TaskResponseDto
{
    private int $id;
    private string $name;
    private string $description;
    private \DateTimeImmutable $deadline;
    private bool $closeDeadline;
    private bool $expired;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
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

    /**
     * @return string
     */
    public function getDeadline(): string
    {
        return $this->deadline->format('Y-m-d H:i');
    }

    /**
     * @param \DateTimeImmutable $deadline
     */
    public function setDeadline(\DateTimeImmutable $deadline): void
    {
        $this->deadline = $deadline;
    }

    /**
     * @return bool
     */
    public function isCloseDeadline(): bool
    {
        return $this->closeDeadline;
    }

    /**
     * @param bool $closeDeadline
     */
    public function setCloseDeadline(bool $closeDeadline): void
    {
        $this->closeDeadline = $closeDeadline;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expired;
    }

    /**
     * @param bool $expired
     */
    public function setExpired(bool $expired): void
    {
        $this->expired = $expired;
    }
}