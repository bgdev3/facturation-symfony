<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Ce champs ne peut être vide !')]
    private string $name = '';

    #[ORM\Column(length: 255)]
    #[Assert\Length(exactly: 14, exactMessage: 'Le SIRET doit contenir exactement 14 chiffres.')]
    #[Assert\Regex(pattern: '/^\d{14}$/', message: 'Le SIRET ne doit contenir que des chiffres.')]
    #[Assert\Luhn(message: 'Ce numéro SIRET n\'est pas valide.')]
    #[Assert\Length(max:14)]
    private string $siret = '';

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Ce champs ne peut être vide !')]
    private string $address = '';

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: '/^\d{5}$/',
        message: 'Le code postal doit être composé de 5 chiffres.'
    )]
    #[Assert\Length(max:5)]
    private string $postalCode = '';

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Ce champs ne peut être vide !')]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Regex(
    pattern: '/^FR[0-9A-Z]{2}[0-9]{9}$/',
    message: 'Format de numéro de TVA intracommunautaire invalide'
    )]
    private ?string $tvaIntraCom = null;

    #[ORM\Column(length: 255)]
    #[Assert\Iban(message: 'IBAN invalide')]
    private string $iban = '';

    #[ORM\Column(length: 255)]
    #[Assert\Bic(ibanPropertyPath: 'iban', message: 'BIC invalide')]
    private string $bic = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSiret(): string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getTvaIntraCom(): ?string
    {
        return $this->tvaIntraCom;
    }

    public function setTvaIntraCom(string $tvaIntraCom): static
    {
        $this->tvaIntraCom = $tvaIntraCom;

        return $this;
    }

    public function getIban(): string
    {
        return $this->iban;
    }

    public function setIban(string $iban): static
    {
        $this->iban = $iban;

        return $this;
    }

    public function getBic(): string
    {
        return $this->bic;
    }

    public function setBic(string $bic): static
    {
        $this->bic = $bic;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }
}
