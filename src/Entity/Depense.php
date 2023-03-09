<?php

namespace App\Entity;

use App\Repository\DepenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeInterface;
use Dompdf\Dompdf;

#[ORM\Entity(repositoryClass: DepenseRepository::class)]
class Depense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @Assert\GreaterThanOrEqual(0)
     */
    #[ORM\Column(nullable: true)]
    private ?float $prix = null;

    /**
     * @Assert\NotBlank
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    /**
     * @Assert\NotBlank
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etat = null;

    /**
     * @Assert\NotBlank
     *
     */

    #[ORM\Column(length: 255, nullable: true)]

    private ?string $description = null;

    /**
     * @Assert\NotBlank
     */

    /**
     * @Assert\GreaterThanOrEqual(0)
     */
    #[ORM\Column(nullable: true)]
    private ?float $totalParMois = null;

    #[ORM\OneToMany(mappedBy: 'depense', targetEntity: Categorie::class)]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

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

    public function getTotalParMois(): ?float
    {
        return $this->totalParMois;
    }

    public function setTotalParMois(?float $totalParMois): self
    {
        $this->totalParMois = $totalParMois;

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setDepense($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getDepense() === $this) {
                $category->setDepense(null);
            }
        }

        return $this;
    }
}