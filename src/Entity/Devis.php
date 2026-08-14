<?php

namespace App\Entity;

use App\Enum\DevisStatut;
use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Le numéro ne peut être vide.')]
    #[Assert\Regex(
    pattern: '/^(DEV|FAC)-\d{4}-\d{3,}$/',
    message: 'Le format du numéro doit être DEV-AAAA-000 ou FAC-AAAA-000.'
    )]
    private string $numero = '';

    #[ORM\Column]
    private ?\DateTimeImmutable $dateEmission = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateValidite = null;

    #[ORM\Column(length: 255, enumType: DevisStatut::class)]
    #[Assert\NotBlank(message: 'Le statut ne peut être vide.')]
    private ?DevisStatut $statut = null;

    #[ORM\Column(length: 255, type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le montant HT ne peut être vide.')]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: 'Le montant doit être un nombre valide (ex: 150.00).'
    )]
    private string $montantHT = '';

    #[ORM\Column(length: 255, type: 'decimal', precision: 10, scale: 2)]
     #[Assert\NotBlank(message: 'Le montant TVA ne peut être vide.')]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: 'Le montant doit être un nombre valide (ex: 150.00).'
    )]
    private string $montantTVA = '';

    #[ORM\Column(length: 255, type: 'decimal', precision: 10, scale: 2)]
        #[Assert\NotBlank(message: 'Le montant TTC ne peut être vide.')]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: 'Le montant doit être un nombre valide (ex: 150.00).'
    )]
    private string $montantTTC = '';

    #[ORM\ManyToOne(inversedBy: 'devis')]
    private ?Client $client = null;

    /**
     * @var Collection<int, LigneDevis>
     */
    #[ORM\OneToMany(targetEntity: LigneDevis::class, mappedBy: 'devis')]
    private Collection $ligneDevis;

    public function __construct()
    {
        $this->ligneDevis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDateEmission(): ?\DateTimeImmutable
    {
        return $this->dateEmission;
    }

    public function setDateEmission(\DateTimeImmutable $dateEmission): static
    {
        $this->dateEmission = $dateEmission;

        return $this;
    }

    public function getDateValidite(): ?\DateTimeImmutable
    {
        return $this->dateValidite;
    }

    public function setDateValidite(\DateTimeImmutable $dateValidite): static
    {
        $this->dateValidite = $dateValidite;

        return $this;
    }

    public function getStatut(): ?DevisStatut
    {
        return $this->statut;
    }

    public function setStatut(DevisStatut $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getMontantHT(): string
    {
        return $this->montantHT;
    }

    public function setMontantHT(string $montantHT): static
    {
        $this->montantHT = $montantHT;

        return $this;
    }

    public function getMontantTVA(): string
    {
        return $this->montantTVA;
    }

    public function setMontantTVA(string $montantTVA): static
    {
        $this->montantTVA = $montantTVA;

        return $this;
    }

    public function getMontantTTC(): string
    {
        return $this->montantTTC;
    }

    public function setMontantTTC(string $montantTTC): static
    {
        $this->montantTTC = $montantTTC;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, LigneDevis>
     */
    public function getLigneDevis(): Collection
    {
        return $this->ligneDevis;
    }

    public function addLigneDevi(LigneDevis $ligneDevi): static
    {
        if (!$this->ligneDevis->contains($ligneDevi)) {
            $this->ligneDevis->add($ligneDevi);
            $ligneDevi->setDevis($this);
        }

        return $this;
    }

    public function removeLigneDevi(LigneDevis $ligneDevi): static
    {
        if ($this->ligneDevis->removeElement($ligneDevi)) {
            // set the owning side to null (unless already changed)
            if ($ligneDevi->getDevis() === $this) {
                $ligneDevi->setDevis(null);
            }
        }

        return $this;
    }
}
