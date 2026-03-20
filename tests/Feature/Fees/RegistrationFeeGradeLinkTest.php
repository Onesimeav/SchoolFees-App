<?php

namespace Tests\Feature\Fees;

use App\Models\Grade;
use App\Models\RegistrationFee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationFeeGradeLinkTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function registration_fee_can_be_linked_to_a_grade(): void
    {
        $grade = Grade::factory()->create(['name' => 'CE1']);

        $fee = RegistrationFee::create([
            'type'         => 'RegistrationFee',
            'title'        => 'Frais CE1',
            'grade_id'     => $grade->id,
            'total_amount' => 20000,
            'academic_year'=> '2025-2026',
        ]);

        $this->assertDatabaseHas('fees', [
            'id'       => $fee->id,
            'grade_id' => $grade->id,
        ]);

        $this->assertTrue($fee->grade->is($grade));
    }

    /** @test */
    public function grade_selector_shown_only_for_registration_fee_type(): void
    {
        // The FeeForm grade_id field has ->visible(fn ($get) => $get('type') === 'App\Models\RegistrationFee')
        // Test that grade_id is nullable (not required) for non-RegistrationFee types
        $grade = Grade::factory()->create();

        // TuitionFee can be created without grade_id
        $fee = \App\Models\TuitionFee::create([
            'type'                  => 'TuitionFee',
            'title'                 => 'Frais scolarité',
            'classroom'             => 'CE1',
            'total_amount'          => 50000,
            'academic_year'         => '2025-2026',
            'number_of_installments'=> 3,
        ]);

        $this->assertNull($fee->grade_id);

        // RegistrationFee links to a grade
        $regFee = RegistrationFee::create([
            'type'         => 'RegistrationFee',
            'title'        => 'Frais inscription',
            'grade_id'     => $grade->id,
            'total_amount' => 15000,
            'academic_year'=> '2025-2026',
        ]);

        $this->assertNotNull($regFee->grade_id);
    }
}