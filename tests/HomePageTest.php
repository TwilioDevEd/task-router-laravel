<?php

use App\MissedCall;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use \Illuminate\Database\Eloquent\Collection;

class HomePageTest extends TestCase
{
    use DatabaseTransactions;

    public function testBasicExample()
    {
        $this->visit('/')
             ->see('Missed Calls');
    }

    public function testEmptyPage()
    {
        $response = $this
            ->call(
                'GET',
                '/',
                ['missed_calls' => []]
            );
        $this->assertViewHas('missed_calls', []);
        $this->see("There are no missed calls at the moment.");
    }

    public function testViewMissedCalls()
    {
        $newEntry = new MissedCall(["selectedProduct" => "Programmable SMS","phoneNumber" => "+11112323"]);
        $newEntry2 = new MissedCall(["selectedProduct" => "Programmable Voice", "phoneNumber" => "+567567567"]);
        $newEntry->save();
        $newEntry2->save();
        $this->visit("/")
            ->see("Programmable Voice")
            ->see("Programmable SMS")
            ->see("tel:+11112323")
            ->see("+567567567");
    }

}
