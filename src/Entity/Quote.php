<?php

namespace App\Entity;

use App\Repository\QuoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: QuoteRepository::class)]
class Quote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['quote:read'])]

    private ?int $id = null;
    #[Groups(['quote:read'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;
    #[Groups(['quote:read'])]
    #[ORM\Column(length: 255)]
    private ?string $author = null;
    #[Groups(['quote:read'])]
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'Quote')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addQuote($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeQuote($this);
        }

        return $this;
    }

    /**
     * @param Collection $users
     * @return Quote
     */
    public function setUsers(Collection $users): Quote
    {
        $this->users = $users;
        return $this;
    }
}
