<?php
// src/Service/DevisCreator.php
namespace App\Services;

use App\Entity\{Company, Devis, LigneDevis};
use App\Enum\DevisStatut;
use App\Repository\ClientRepository;
use App\Services\NumberGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DevisCreator
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private  readonly ClientRepository $clientRepo,
        private  readonly ValidatorInterface $validator,
        private  readonly NumberGenerator $numberGenerator
    ) {}

    public function createDraft(array $input, Company $company): Devis
    {
        $client = $this->clientRepo->findOneBy([
            'id' => $input['client_id'] ?? 0,
            'company' => $company, // un client d'une autre entreprise est refusé
        ]) ?? throw new \InvalidArgumentException('Client introuvable.');

        $lignes = $input['lignes'] ?? [];
        if ($lignes === [] || count($lignes) > 50) {
            throw new \InvalidArgumentException('Nombre de lignes invalide.');
        }

        $aujourdhui = new \DateTimeImmutable('today');

        $devis = (new Devis())
            ->setClient($client)
            ->setNumero($this->numberGenerator->genererProchainNumeroDevis())
            ->setDateEmission($aujourdhui)
            ->setDateValidite($aujourdhui->modify('+30 days'))
            ->setStatut(DevisStatut::Brouillon);

        foreach ($lignes as $l) {
            $quantite = (float) ($l['quantite'] ?? 0);
            $prix     = (float) ($l['prix_unitaire_ht'] ?? -1);
            $taux     = (float) ($l['taux_tva'] ?? 20);

            if ($quantite <= 0 || $prix < 0 || $taux < 0 || $taux > 100) {
                throw new \InvalidArgumentException('Quantité, prix ou taux de TVA invalide.');
            }

            $devis->addLigneDevi(
                (new LigneDevis())
                    ->setDesignation(mb_substr(trim((string) $l['description']), 0, 255))
                    ->setQuantite(number_format($quantite, 2, '.', ''))
                    ->setPrixUnitaireHT(number_format($prix, 2, '.', ''))
                    ->setTauxTVA(number_format($taux, 2, '.', ''))
            );
        }

        $devis->recalculerTotaux();

        $erreurs = $this->validator->validate($devis);
        if (count($erreurs) > 0) {
            throw new \InvalidArgumentException((string) $erreurs);
        }

        $this->em->persist($devis);
        $this->em->flush();

        return $devis;
    }
}