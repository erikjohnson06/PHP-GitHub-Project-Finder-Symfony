<?php

namespace App\Entity;

use App\Repository\GitHubRepositoryRecordRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: GitHubRepositoryRecordRepository::class)]
#[UniqueEntity('repository_id')]
class GitHubRepositoryRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private ?int $repository_id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $html_url = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $stargazers_count = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $pushed_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRepositoryId(): ?int
    {
        return $this->repository_id;
    }

    public function setRepositoryId(int $repository_id): self
    {
        $this->repository_id = $repository_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getHtmlUrl(): ?string
    {
        return $this->html_url;
    }

    public function setHtmlUrl(string $html_url): self
    {
        $this->html_url = $html_url;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStargazersCount(): ?int
    {
        return $this->stargazers_count;
    }

    public function setStargazersCount(int $stargazers_count): self
    {
        $this->stargazers_count = $stargazers_count;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPushedAt(): ?\DateTimeInterface
    {
        return $this->pushed_at;
    }

    public function setPushedAt(\DateTimeInterface $pushed_at): self
    {
        $this->pushed_at = $pushed_at;

        return $this;
    }
}
