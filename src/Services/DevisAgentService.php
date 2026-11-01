<?php
// src/Service/DevisAgentService.php
namespace App\Services;

use App\Entity\Company;
use App\Repository\ClientRepository;
use App\Services\DevisCreator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DevisAgentService
{
    private const MAX_TOURS = 6;

    public function __construct(
        private HttpClientInterface $client,
        private ClientRepository $clientRepo,
        private DevisCreator $devisCreator,
        private string $apiKey,
    ) {}

    /** @return array{reponse: string, devisId: ?int} */
    public function ask(string $demande, Company $company): array
    {
        $messages = [['role' => 'user', 'content' => $demande]];
        $devisId = null;

        for ($i = 0; $i < self::MAX_TOURS; $i++) {
            $data = $this->client->request('POST', 'https://api.anthropic.com/v1/messages', [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'anthropic-version' => '2023-06-01',
                ],
                'json' => [
                    'model' => 'claude-sonnet-4-6',
                    'max_tokens' => 1024,
                    'system' => "Tu aides à créer des devis. Cherche d'abord le client avec "
                        . "rechercher_client. Si le client est introuvable ou ambigu, ou s'il manque "
                        . "une info (prix, quantité), pose une question au lieu de deviner. "
                        . "Les montants sont en euros HT. Réponds en français, brièvement.",
                    'tools' => $this->tools(),
                    'messages' => $messages,
                ],
            ])->toArray();

            $messages[] = ['role' => 'assistant', 'content' => $data['content']];

            if ($data['stop_reason'] !== 'tool_use') {
                $texte = '';
                foreach ($data['content'] as $bloc) {
                    if ($bloc['type'] === 'text') {
                        $texte .= $bloc['text'];
                    }
                }
                return ['reponse' => $texte, 'devisId' => $devisId];
            }

            $resultats = [];
            foreach ($data['content'] as $bloc) {
                if ($bloc['type'] !== 'tool_use') {
                    continue;
                }
                try {
                    $sortie = $this->execute($bloc['name'], $bloc['input'], $company);
                    if ($bloc['name'] === 'creer_brouillon_devis') {
                        $devisId = $sortie['devis_id'];
                    }
                    $resultats[] = [
                        'type' => 'tool_result',
                        'tool_use_id' => $bloc['id'],
                        'content' => json_encode($sortie, JSON_UNESCAPED_UNICODE),
                    ];
                } catch (\Throwable $e) {
                    $resultats[] = [
                        'type' => 'tool_result',
                        'tool_use_id' => $bloc['id'],
                        'content' => $e->getMessage(),
                        'is_error' => true,
                    ];
                }
            }
            $messages[] = ['role' => 'user', 'content' => $resultats];
        }

        return ['reponse' => "Je n'ai pas réussi à finaliser la demande.", 'devisId' => $devisId];
    }

    private function tools(): array
    {
        return [
            [
                'name' => 'rechercher_client',
                'description' => 'Recherche un client de l’entreprise par nom (recherche partielle).',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => ['nom' => ['type' => 'string']],
                    'required' => ['nom'],
                ],
            ],
            [
                'name' => 'creer_brouillon_devis',
                'description' => 'Crée un devis au statut Brouillon pour un client existant.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'client_id' => ['type' => 'integer'],
                        'lignes' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'description' => ['type' => 'string'],
                                    'quantite' => ['type' => 'number'],
                                    'prix_unitaire_ht' => ['type' => 'number'],
                                ],
                                'required' => ['description', 'quantite', 'prix_unitaire_ht'],
                            ],
                        ],
                    ],
                    'required' => ['client_id', 'lignes'],
                ],
            ],
        ];
    }

    private function execute(string $nom, array $input, Company $company): array
    {
        return match ($nom) {
            'rechercher_client' => array_map(
                fn($c) => ['id' => $c->getId(), 'nom' => $c->getName()],
                $this->clientRepo->searchByName($input['nom'], $company),
            ),
            'creer_brouillon_devis' => [
                'devis_id' => $this->devisCreator->createDraft($input, $company)->getId(),
            ],
            default => throw new \InvalidArgumentException('Outil inconnu'),
        };
    }
}