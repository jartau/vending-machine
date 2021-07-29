<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    /**
     * Example 1: Buy Soda with exact change
     *
     * Input: 1, 0.25, 0.25, GET-SODA
     * Output: SODA
     *
     * @return void
     */
    public function test_example1(): void
    {
        $this->artisan('VendingMachine')
            ->expectsQuestion('Insert coin or choose product', '1')
            ->expectsQuestion('Insert coin or choose product', '0.25')
            ->expectsQuestion('Insert coin or choose product', '0.25')
            ->expectsQuestion('Insert coin or choose product', 'GET-SODA')
            ->expectsOutput('SODA')
            ->assertExitCode(0);
    }

    /**
     * Example 2: Start adding money, but user ask for return coin
     *
     * Input: 0.10, 0.10, RETURN-COIN
     * Output: 0.10, 0.10
     *
     * @return void
     */
    public function test_example2(): void
    {
        $this->artisan('VendingMachine')
            ->expectsQuestion('Insert coin or choose product', '0.10')
            ->expectsQuestion('Insert coin or choose product', '0.10')
            ->expectsQuestion('Insert coin or choose product', 'RETURN-COIN')
            ->expectsOutput('0.10')
            ->expectsOutput('0.10')
            ->assertExitCode(0);
    }

    /**
     * Example 3: Buy Water without exact change
     *
     * Input: 1, GET-WATER
     * Output: WATER, 0.25, 0.10
     *
     * @return void
     */
    public function test_example3(): void
    {
        $this->artisan('VendingMachine')
            ->expectsQuestion('Insert coin or choose product', '1')
            ->expectsQuestion('Insert coin or choose product', 'GET-WATER')
            ->expectsOutput('WATER')
            ->expectsOutput('0.25')
            ->expectsOutput('0.10')
            ->assertExitCode(0);
    }
}
