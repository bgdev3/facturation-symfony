<?php

namespace App\Entity;

use App\Enum\FactureStatut;
use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
#[UniqueEntity(fields: ['numero'], message: 'Ce numéro de facture existe déjà.')]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Le numéro ne peut être vide.')]
    #[Assert\Regex(
    pattern: '/^FAC-\d{4}-\d{3,}$/',
    message: 'Le format du numéro doit être DEV-AAAA-000 ou FAC-AAAA-000.'
    )]
    private string $numero = '';

    #[ORM\Column]
    private ?\DateTimeImmutable $dateEmission = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateEcheance = null;

    #[ORM\Column(length: 255, enumType: FactureStatut::class)]
    #[Assert\NotBlank(message: 'Le statut ne peut être vide.')]
    private ?FactureStatut $statut = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: 'Le montant doit être un nombre valide (ex: 150.00).'
    )]
    private string $montantHT = '';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: 'Le montant doit être un nombre valide (ex: 150.00).'
    )]
    private string $montantTVA = '';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: 'Le montant doit être un nombre valide (ex: 150.00).'
    )]
    private string $montantTTC = '';

    #[ORM\Column(length: 255, nullable:true)]
    private string $conditionsPaiement = '';

    #[ORM\ManyToOne(inversedBy: 'factures')]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    private ?Devis $devis = null;

    /**
     * @var Collection<int, LigneFacture>
     */
    #[ORM\OneToMany(mappedBy: 'facture', targetEntity: LigneFacture::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ligneFactures;

    /**
     * @var Collection<int, Paiement>
     */
    #[ORM\OneToMany(targetEntity: Paiement::class, mappedBy: 'facture')]
    private Collection $paiements;

    public function __construct()
    {
        $this->ligneFactures = new ArrayCollection();
        $this->paiements = new ArrayCollection();
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

    public function getDateEcheance(): ?\DateTimeImmutable
    {
        return $this->dateEcheance;
    }

    public function setDateEcheance(\DateTimeImmutable $dateEchenace): static
    {
        $this->dateEcheance = $dateEchenace;

        return $this;
    }

    public function getStatut(): ?FactureStatut
    {
        return $this->statut;
    }

    public function setStatut(FactureStatut $statut): static
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

    public function getConditionsPaiement(): string
    {
        return $this->conditionsPaiement;
    }

    public function setConditionsPaiement(string $conditionsPaiement): static
    {
        $this->conditionsPaiement = $conditionsPaiement;

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

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): static
    {
        $this->devis = $devis;

        return $this;
    }

    /**
     * @return Collection<int, LigneFacture>
     */
    public function getLigneFactures(): Collection
    {
        return $this->ligneFactures;
    }

    public function addLigneFacture(LigneFacture $ligneFactures): static
    {
        if (!$this->ligneFactures->contains($ligneFactures)) {
            $this->ligneFactures->add($ligneFactures);
            $ligneFactures->setFacture($this);
        }

        return $this;
    }

    public function removeLigneFacture(LigneFacture $ligneFactures): static
    {
        if ($this->ligneFactures->removeElement($ligneFactures)) {
            // set the owning side to null (unless already changed)
            if ($ligneFactures->getFacture() === $this) {
                $ligneFactures->setFacture(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): static
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements->add($paiement);
            $paiement->setFacture($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): static
    {
        if ($this->paiements->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getFacture() === $this) {
                $paiement->setFacture(null);
            }
        }

        return $this;
    }

    public function getTotalPaye(): float
    {
        return array_sum(array_map(fn($p) => $p->getMontant(), $this->paiements->toArray()));
    }

    public function getResteAPayer(): float
    {
        return $this->getMontantTTC() - $this->getTotalPaye();
    }

      public function recalculerTotaux(): void
    {
        $ht = '0.00';
        $tva = '0.00';

        foreach ($this->ligneFactures as $ligne) {
            $ht  = bcadd($ht, $ligne->getTotalHT(), 2);
            $tva = bcadd($tva, $ligne->getMontantTVA(), 2);
        }

        $this->montantHT  = $ht;
        $this->montantTVA = $tva;
        $this->montantTTC = bcadd($ht, $tva, 2);
    }

}
