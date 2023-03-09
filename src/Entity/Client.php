<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("post:read")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:"nom is required")]
    #[Groups("post:read")]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:"prenom is required")]
    #[Groups("post:read")]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:"email is required")]
    #[Assert\Email(message:"The email '{{value}}' is not a valid email")]
    #[Groups("post:read")]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:"tel is required")]
    /* #[Assert\length(
        'min'=>8,
        'max'=>13,
        'minMessage'=>'phone number requires at least {{limit}} numbers',
        'maxMessage'=> 'phone number cant be longer than {{limit}} numbers'
)
]*/
    #[Groups("post:read")]
    private ?string $tel = null;
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Credit::class, orphanRemoval: true)]
    private Collection $Credit;

    public function __construct()
    {
        $this->Credit = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }
    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }
    /**
     * @return Collection<int, Credit>
     */
    public function getCredit(): Collection
    {
        return $this->Credit;
    }

    public function addCredit(Credit $credit): self
    {
        if (!$this->Credit->contains($credit)) {
            $this->Credit->add($credit);
            $credit->setClient($this);
        }

        return $this;
    }

    public function removeCredit(Credit $credit): self
    {
        if ($this->Credit->removeElement($credit)) {
            // set the owning side to null (unless already changed)
            if ($credit->getClient() === $this) {
                $credit->setClient(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getEmail();
    }
}
