<?php

use App\MissedCall;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HomePageTest extends TestCase
{
    use DatabaseTransactions;

    public function testBasicExample()
    {
        $response = $this->get('/');
        $response->assertSee('Missed Calls');
    }

    public function testEmptyPage()
    {
        $response = $this->call('GET', '/', ['missed_calls' => []]);
        $response->assertViewHas('missed_calls', new Collection());
        $response->assertSee('There are no missed calls at the moment.');
    }

    public function testViewMissedCalls()
    {
        $c = function($selected_product, $phone_number) {
            return compact('selected_product', 'phone_number');
        };

        MissedCall::create($c($s1 = 'Programmable SMS', $p1 = '+11112323'));
        MissedCall::create($c($s2 = 'Programmable Voice', $p2 = '+567567567'));

        $response = $this->get('/');
        $response->assertSee($s1);
        $response->assertSee($s2);
        $response->assertSee($p1);
        $response->assertSee($p2);
    }

}
