<?php

namespace App\Entity;

use App\Repository\GitHubProjectsRequestManagerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GitHubProjectsRequestManagerRepository::class)]
class GitHubProjectsRequestManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $is_running = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $start_time = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end_time = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $error_msg = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsRunning(): ?bool
    {
        return $this->is_running;
    }

    public function setIsRunning(bool $is_running): self
    {
        $this->is_running = $is_running;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(?\DateTimeInterface $start_time): self
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(?\DateTimeInterface $end_time): self
    {
        $this->end_time = $end_time;

        return $this;
    }

    public function getErrorMsg(): ?string
    {
        return $this->error_msg;
    }

    public function setErrorMsg(?string $error_msg): self
    {
        $this->error_msg = $error_msg;

        return $this;
    }
}
