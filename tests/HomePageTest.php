<?php

use App\MissedCall;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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
        $this->assertViewHas('missed_calls', new Collection());
        $this->see("There are no missed calls at the moment.");
    }

    public function testViewMissedCalls()
    {
        $newEntry = new MissedCall(
            [
            "selected_product" => "Programmable SMS",
            "phone_number" => "+11112323"
            ]
        );
        $newEntry2 = new MissedCall(
            [
            "selected_product" => "Programmable Voice",
            "phone_number" => "+567567567"
            ]
        );
        $newEntry->save();
        $newEntry2->save();
        $this->visit("/")
            ->see("Programmable Voice")
            ->see("Programmable SMS")
            ->see("tel:+11112323")
            ->see("+567567567");
    }

}
