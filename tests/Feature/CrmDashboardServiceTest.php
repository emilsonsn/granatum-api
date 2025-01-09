<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CrmDashboard\CrmDashboardService;
use App\Models\Lead;
use App\Models\BudgetGenerated;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CrmDashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cards_with_monthly_period()
    {
        Lead::factory()->count(10)->create();

        BudgetGenerated::factory()->count(5)->create([
            'created_at' => Carbon::now(),
            'status' => 'Generated'
        ]);

        $service = new CrmDashboardService();
        $result = $service->cards('Monthly');

        $this->assertTrue($result['status']);
        $this->assertEquals(0, $result['data']['leads']);
    }

    public function test_budget_graphic_grouped_by_month()
    {
        BudgetGenerated::factory()->count(10)->create([
            'created_at' => Carbon::now()->startOfMonth(),
            'status' => 'Generated'
        ]);

        BudgetGenerated::factory()->count(5)->create([
            'created_at' => Carbon::now()->subMonth()->startOfMonth(),
            'status' => 'Generated'
        ]);

        $service = new CrmDashboardService();
        $result = $service->budgetGraphic('Generated');

        $this->assertTrue($result['status']);
        $this->assertArrayHasKey(Carbon::now()->format('Y-m'), $result['data']);
        $this->assertEquals(10, $result['data'][Carbon::now()->format('Y-m')]);
    }
}
