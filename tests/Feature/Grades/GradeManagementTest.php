<?php

namespace Tests\Feature\Grades;

use App\Filament\Staff\Resources\Grades\GradeResource;
use App\Filament\Staff\Resources\Grades\Pages\CreateGrade;
use App\Filament\Staff\Resources\Grades\Pages\ListGrades;
use App\Models\Grade;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GradeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'accountant']);
        Role::create(['name' => 'secretary']);
        Role::create(['name' => 'employee']);
        Role::create(['name' => 'parent_student']);

        Filament::setCurrentPanel(Filament::getPanel('staff'));
    }

    /** @test */
    public function secretary_can_create_a_grade(): void
    {
        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $this->actingAs($secretary);

        Livewire::test(CreateGrade::class)
            ->set('data.name', 'CE2')
            ->set('data.description', 'Cours élémentaire 2ème année')
            ->call('create');

        $this->assertDatabaseHas('grades', [
            'name'        => 'CE2',
            'description' => 'Cours élémentaire 2ème année',
        ]);
    }

    /** @test */
    public function accountant_cannot_create_a_grade(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $this->assertFalse(GradeResource::canCreate());
    }

    /** @test */
    public function any_staff_can_view_grades(): void
    {
        $grade = Grade::factory()->create(['name' => 'CP']);

        foreach (['admin', 'secretary', 'accountant', 'employee'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $this->actingAs($user);

            $this->assertTrue(GradeResource::canViewAny());
        }
    }

    /** @test */
    public function grade_appears_in_staff_list(): void
    {
        $grade = Grade::factory()->create(['name' => 'Terminale']);

        $secretary = User::factory()->create();
        $secretary->assignRole('secretary');

        $this->actingAs($secretary);

        Livewire::test(ListGrades::class)
            ->assertCanSeeTableRecords(Grade::all());
    }
}