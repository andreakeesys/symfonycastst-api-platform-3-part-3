<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\UserApi;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EntityToDtoStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $provider
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $entities = $this->provider->provide($operation, $uriVariables, $context);

        $dtos = [];
        foreach ($entities as $entity) {
            $dtos[] = $this->mapEntityToDto($entity);
        }

        return new TraversablePaginator(
            new \ArrayIterator($dtos),
            $entities->getCurrentPage(),
            $entities->getItemsPerPage(),
            $entities->getTotalItems(),
        );
    }

    private function mapEntityToDto(object $entity): object
    {
        /** @var User $entity */
        $dto = new UserApi();
        $dto->id = $entity->getId();
        $dto->email = $entity->getEmail();
        $dto->username = $entity->getUsername();
        $dto->dragonTreasures = $entity->getDragonTreasures()->toArray();
        $dto->flameThrowingDistance = rand(1, 10);

        return $dto;
    }

}
