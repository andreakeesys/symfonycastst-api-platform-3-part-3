<?php

namespace App\Tests\Functional;

use App\Enum\DailyQuestStatusEnum;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DailyQuestResourceTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    public function testPatchCanUpdateStatus()
    {
        $day = new \DateTime('-2 day');

        $this->browser()
            ->patch('/api/quests/' . $day->format('Y-m-d'), [
                'json' => [
                    'status' => DailyQuestStatusEnum::COMPLETED
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMatches('status', 'completed')
        ;
    }
}
