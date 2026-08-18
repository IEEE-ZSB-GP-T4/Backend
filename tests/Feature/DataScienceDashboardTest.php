<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\DataScienceDashboardService;
use Laravel\Sanctum\Sanctum;
use Mockery\MockInterface;
use Tests\TestCase;

class DataScienceDashboardTest extends TestCase
{
    public function test_authenticated_user_can_get_data_science_dashboard(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->mock(DataScienceDashboardService::class, function (MockInterface $mock) use ($user) {
            $mock->shouldReceive('generateForUser')
                ->once()
                ->with($user->id)
                ->andReturn([
                    'status' => 'success',
                    'message' => 'Data retrieved successfully',
                    'data' => [
                        'user_id' => $user->id,
                        'dashboard' => [
                            'kpis' => [
                                'task_completion_rate' => 75,
                            ],
                            'visuals' => [],
                        ],
                    ],
                ]);
        });

        $response = $this->getJson('/api/dashboard/data-science');

        $response->assertOk()
            ->assertJsonPath('message', 'Data science dashboard retrieved successfully')
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.dashboard.kpis.task_completion_rate', 75);
    }
}
