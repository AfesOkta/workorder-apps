<?php

use App\Models\WorkOrders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class WorkOrderTest extends TestCase
{
    use RefreshDatabase;

    public function testWorkOrderCreation()
    {
        $workOrder = new WorkOrders();
        $this->assertNotNull($workOrder);
    }

    public function testWorkOrderPagination()
    {
        // Seed the database with some work orders
        WorkOrders::factory()->count(15)->create();

        $workOrders = WorkOrders::paginate(10);
        $this->assertNotEmpty($workOrders);
        $this->assertCount(10, $workOrders->items());
    }

    // Add your test methods here
}
