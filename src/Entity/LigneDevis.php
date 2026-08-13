<?php

namespace App\Entity;

use App\Repository\LigneDevisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LigneDevisRepository::class)]
#[ORM\HasLifecycleCallbacks] 
class LigneDevis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le statut ne peut être vide.')]
    private ?string $designation = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $quantite = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le montant HT ne peut être vide.')]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: 'Le montant doit être un nombre valide (ex: 150.00).'
    )]
    private ?string $prixUnitaireHT = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
     #[Assert\NotBlank(message: 'Le taux TVA ne peut être vide.')]
    #[Assert\Regex(
    pattern: '/^\d+(\.\d{1,2})?$/',
    message: 'Le montant doit être un nombre valide (ex: 150.00).'
    )]
    private ?string $tauxTVA = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $montantHT = null;

    #[ORM\ManyToOne(inversedBy: 'ligneDevis')]
    private ?Devis $devis = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getQuantite(): ?string
    {
        return $this->quantite;
    }

    public function setQuantite(string $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixUnitaireHT(): ?string
    {
        return $this->prixUnitaireHT;
    }

    public function setPrixUnitaireHT(string $prixUnitaireHT): static
    {
        $this->prixUnitaireHT = $prixUnitaireHT;

        return $this;
    }

    public function getTauxTVA(): ?string
    {
        return $this->tauxTVA;
    }

    public function setTauxTVA(string $tauxTVA): static
    {
        $this->tauxTVA = $tauxTVA;

        return $this;
    }

    public function getMontantHT(): ?string
    {
        return $this->montantHT;
    }

    public function setMontantHT(string $montantHT): static
    {
        $this->montantHT = $montantHT;

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

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculerMontantHT(): void
    {
        $quantite = (string) $this->quantite;
        $prixUnitaire = $this->prixUnitaireHT ?? '0';

        $this->montantHT = bcmul($quantite, $prixUnitaire, 2);
    }
}
