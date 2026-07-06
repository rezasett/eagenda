<?php

namespace Tests\Feature;

use App\Livewire\LetterIndex;
use App\Models\Letter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LetterValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_save_letter_with_valid_input_and_file_2mb(): void
    {
        Storage::fake('local');
        $staff = User::factory()->create(['role' => 'staff']);

        Livewire::actingAs($staff)
            ->test(LetterIndex::class)
            ->set('letter_number', '001/ADM/VII/2026')
            ->set('letter_type', Letter::TYPE_IN)
            ->set('letter_date', '06-07-2026')
            ->set('sender_name', 'Kantor Pusat')
            ->set('sender_email', 'admin@kantor.com')
            ->set('recipient_name', 'Bagian Administrasi')
            ->set('category', 'Umum')
            ->set('subject', str_repeat('A', 100))
            ->set('file', UploadedFile::fake()->create('surat.pdf', 2048, 'application/pdf'))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('letters', [
            'letter_number' => '001/ADM/VII/2026',
            'subject' => str_repeat('A', 100),
        ]);
    }

    public function test_invalid_date_text_is_rejected(): void
    {
        Storage::fake('local');
        $staff = User::factory()->create(['role' => 'staff']);

        Livewire::actingAs($staff)
            ->test(LetterIndex::class)
            ->set('letter_number', '002/ADM/VII/2026')
            ->set('letter_type', Letter::TYPE_IN)
            ->set('letter_date', 'kemarin')
            ->set('sender_name', 'Kantor Pusat')
            ->set('category', 'Umum')
            ->set('subject', 'Undangan rapat')
            ->set('file', UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'))
            ->call('save')
            ->assertHasErrors(['letter_date']);
    }

    public function test_file_larger_than_2mb_is_rejected(): void
    {
        Storage::fake('local');
        $staff = User::factory()->create(['role' => 'staff']);

        Livewire::actingAs($staff)
            ->test(LetterIndex::class)
            ->set('letter_number', '003/ADM/VII/2026')
            ->set('letter_type', Letter::TYPE_IN)
            ->set('letter_date', '06-07-2026')
            ->set('sender_name', 'Kantor Pusat')
            ->set('category', 'Umum')
            ->set('subject', 'Undangan rapat')
            ->set('file', UploadedFile::fake()->create('surat.pdf', 2100, 'application/pdf'))
            ->call('save')
            ->assertHasErrors(['file']);
    }

    public function test_invalid_file_extension_is_rejected(): void
    {
        Storage::fake('local');
        $staff = User::factory()->create(['role' => 'staff']);

        Livewire::actingAs($staff)
            ->test(LetterIndex::class)
            ->set('letter_number', '004/ADM/VII/2026')
            ->set('letter_type', Letter::TYPE_IN)
            ->set('letter_date', '06-07-2026')
            ->set('sender_name', 'Kantor Pusat')
            ->set('category', 'Umum')
            ->set('subject', 'Undangan rapat')
            ->set('file', UploadedFile::fake()->create('virus.exe', 100, 'application/octet-stream'))
            ->call('save')
            ->assertHasErrors(['file']);
    }

    public function test_subject_more_than_100_characters_is_rejected(): void
    {
        Storage::fake('local');
        $staff = User::factory()->create(['role' => 'staff']);

        Livewire::actingAs($staff)
            ->test(LetterIndex::class)
            ->set('letter_number', '005/ADM/VII/2026')
            ->set('letter_type', Letter::TYPE_IN)
            ->set('letter_date', '06-07-2026')
            ->set('sender_name', 'Kantor Pusat')
            ->set('category', 'Umum')
            ->set('subject', str_repeat('A', 101))
            ->set('file', UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'))
            ->call('save')
            ->assertHasErrors(['subject']);
    }
}
