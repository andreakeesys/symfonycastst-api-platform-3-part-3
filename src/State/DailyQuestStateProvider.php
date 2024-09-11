<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\DailyQuest;
use App\Enum\DailyQuestStatusEnum;
use App\Repository\DragonTreasureRepository;

class DailyQuestStateProvider implements ProviderInterface
{
    public function __construct(
        private DragonTreasureRepository $treasureRepository
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $dailyQuests = $this->createQuests();

        if ($operation instanceof CollectionOperationInterface) {
            return $dailyQuests;
        }

        return $dailyQuests[$uriVariables['dayString']] ?? null;
    }

    /**
     * @return DailyQuest[]
     * @throws \DateMalformedStringException
     */
    private function createQuests(): array
    {
        $treasures = $this->treasureRepository->findBy([], [], 10);

        $quests = [];
        for ($i = 0; $i < 50; $i++) {
            $quest = new DailyQuest(new \DateTimeImmutable(sprintf('- %d days', $i)));
            $quest->questName = sprintf('Quest %d', $i);
            $quest->description = sprintf('Description %d', $i);
            $quest->difficultyLevel = $i % 10;
            $quest->status = $i % 2 === 0 ? DailyQuestStatusEnum::ACTIVE : DailyQuestStatusEnum::COMPLETED;
            $quest->lastUpdated = new \DateTimeImmutable(sprintf('- %d days', rand(1, 100)));
            $randomTreasuresKeys = array_rand($treasures, rand(1, 3));
            $randomTreasures = array_map(fn($key) => $treasures[$key], (array) $randomTreasuresKeys);
            $quest->treasures = $randomTreasures;
            $quests[$quest->getDayString()] = $quest;
        }
        return $quests;
    }
}
