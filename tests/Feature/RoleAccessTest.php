<?php

namespace Tests\Feature;

use App\Livewire\LetterIndex;
use App\Models\Letter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_pimpinan_can_view_letter_index(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        $this->actingAs($pimpinan)
            ->get(route('letters.index'))
            ->assertOk()
            ->assertSee('Mode pimpinan: lihat dan download saja');
    }

    public function test_pimpinan_cannot_open_create_form(): void
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan']);

        Livewire::actingAs($pimpinan)
            ->test(LetterIndex::class)
            ->call('create')
            ->assertForbidden();
    }

    public function test_staff_can_delete_letter(): void
    {
        Storage::fake('local');
        $staff = User::factory()->create(['role' => 'staff']);
        Storage::disk('local')->put('letters/dummy.pdf', 'dummy');

        $letter = Letter::create([
            'letter_number' => '006/ADM/VII/2026',
            'letter_type' => Letter::TYPE_IN,
            'letter_date' => '2026-07-06',
            'sender_name' => 'Kantor Pusat',
            'category' => 'Umum',
            'subject' => 'Undangan rapat',
            'file_path' => 'letters/dummy.pdf',
            'original_file_name' => 'dummy.pdf',
            'created_by' => $staff->id,
        ]);

        Livewire::actingAs($staff)
            ->test(LetterIndex::class)
            ->call('delete', $letter->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('letters', ['id' => $letter->id]);
    }
}
