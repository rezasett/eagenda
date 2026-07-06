<?php

namespace App\Livewire;

use App\Models\Letter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class LetterIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    public bool $showForm = false;
    public ?int $editingId = null;

    public string $letter_number = '';
    public string $letter_type = Letter::TYPE_IN;
    public string $letter_date = '';
    public string $sender_name = '';
    public string $sender_email = '';
    public string $recipient_name = '';
    public string $category = '';
    public string $subject = '';
    public string $description = '';

    public ?TemporaryUploadedFile $file = null;
    public ?string $existingFileName = null;

    public string $search = '';
    public string $typeFilter = '';
    public string $categoryFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $monthFilter = '';

    protected string $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        Gate::authorize('view-letters');
    }

    public function updatedSearch(): void
    {
        $this->validateOnly('search', $this->searchRules());
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function updatedMonthFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        Gate::authorize('manage-letters');
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('manage-letters');

        $letter = Letter::findOrFail($id);

        $this->editingId = $letter->id;
        $this->letter_number = $letter->letter_number;
        $this->letter_type = $letter->letter_type;
        $this->letter_date = $letter->letter_date->format('d-m-Y');
        $this->sender_name = $letter->sender_name;
        $this->sender_email = (string) $letter->sender_email;
        $this->recipient_name = (string) $letter->recipient_name;
        $this->category = $letter->category;
        $this->subject = $letter->subject;
        $this->description = (string) $letter->description;
        $this->file = null;
        $this->existingFileName = $letter->original_file_name;
        $this->showForm = true;
        $this->resetValidation();
    }

    public function save()
    {
        Gate::authorize('manage-letters');

        $validated = $this->validate($this->rules(), $this->messages());

        $payload = [
            'letter_number' => trim($validated['letter_number']),
            'letter_type' => $validated['letter_type'],
            'letter_date' => Carbon::createFromFormat('d-m-Y', $validated['letter_date'])->format('Y-m-d'),
            'sender_name' => trim($validated['sender_name']),
            'sender_email' => $validated['sender_email'] ? trim($validated['sender_email']) : null,
            'recipient_name' => $validated['recipient_name'] ? trim($validated['recipient_name']) : null,
            'category' => $validated['category'],
            'subject' => trim($validated['subject']),
            'description' => $validated['description'] ? trim($validated['description']) : null,
        ];

        if ($this->file) {
            $payload['file_path'] = $this->file->store('letters', 'local');
            $payload['original_file_name'] = $this->safeOriginalName($this->file->getClientOriginalName());
            $payload['file_mime'] = $this->file->getMimeType();
            $payload['file_size'] = $this->file->getSize();
        }

        if ($this->editingId) {
            $letter = Letter::findOrFail($this->editingId);

            if ($this->file && $letter->file_path && Storage::disk('local')->exists($letter->file_path)) {
                Storage::disk('local')->delete($letter->file_path);
            }

            $letter->update($payload);
            session()->flash('success', 'Data surat berhasil diperbarui.');
        } else {
            $payload['created_by'] = auth()->id();
            Letter::create($payload);
            session()->flash('success', 'Data surat berhasil disimpan.');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->resetPage();

        return null;
    }

    public function delete(int $id): void
    {
        Gate::authorize('manage-letters');

        $letter = Letter::findOrFail($id);

        if ($letter->file_path && Storage::disk('local')->exists($letter->file_path)) {
            Storage::disk('local')->delete($letter->file_path);
        }

        $letter->delete();
        session()->flash('success', 'Data surat berhasil dihapus.');
        $this->resetPage();
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->categoryFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->monthFilter = '';
        $this->resetValidation('search');
        $this->resetPage();
    }

    public function rules(): array
    {
        return [
            'letter_number' => [
                'required',
                'string',
                'min:1',
                'max:50',
                Rule::unique('letters', 'letter_number')->ignore($this->editingId),
            ],
            'letter_type' => ['required', Rule::in([Letter::TYPE_IN, Letter::TYPE_OUT])],
            'letter_date' => ['required', 'date_format:d-m-Y'],
            'sender_name' => ['required', 'string', 'max:100'],
            'sender_email' => ['nullable', 'email', 'max:100'],
            'recipient_name' => ['nullable', 'string', 'max:100'],
            'category' => ['required', Rule::in(Letter::CATEGORIES)],
            'subject' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => [
                $this->editingId ? 'nullable' : 'required',
                'file',
                'max:2048',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
            ],
        ];
    }

    public function searchRules(): array
    {
        return [
            'search' => [
                'nullable',
                'string',
                'max:100',
                'not_regex:/<[^>]*>|(script|select\s+|insert\s+|update\s+|delete\s+|drop\s+|--|;)/i',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'letter_number.required' => 'Nomor surat wajib diisi.',
            'letter_number.min' => 'Nomor surat minimal 1 karakter.',
            'letter_number.max' => 'Nomor surat maksimal 50 karakter.',
            'letter_number.unique' => 'Nomor surat sudah digunakan.',
            'letter_type.required' => 'Jenis surat wajib dipilih.',
            'letter_type.in' => 'Jenis surat tidak valid.',
            'letter_date.required' => 'Tanggal surat wajib diisi.',
            'letter_date.date_format' => 'Tanggal surat harus berformat DD-MM-YYYY, contoh 06-07-2026.',
            'sender_name.required' => 'Asal/pengirim surat wajib diisi.',
            'sender_name.max' => 'Asal/pengirim maksimal 100 karakter.',
            'sender_email.email' => 'Email pengirim tidak valid.',
            'recipient_name.max' => 'Tujuan surat maksimal 100 karakter.',
            'category.required' => 'Kategori surat wajib dipilih.',
            'category.in' => 'Kategori surat tidak tersedia pada dropdown.',
            'subject.required' => 'Perihal wajib diisi.',
            'subject.max' => 'Perihal maksimal 100 karakter.',
            'description.max' => 'Keterangan maksimal 1.000 karakter.',
            'file.required' => 'Berkas surat wajib diunggah.',
            'file.file' => 'Berkas surat tidak valid.',
            'file.max' => 'Ukuran berkas maksimal 2 MB.',
            'file.mimes' => 'Format berkas harus PDF, JPG, JPEG, PNG, DOC, atau DOCX.',
            'search.max' => 'Kata kunci pencarian maksimal 100 karakter.',
            'search.not_regex' => 'Kata kunci pencarian mengandung pola script/injection yang tidak diperbolehkan.',
        ];
    }

    public function render()
    {
        Gate::authorize('view-letters');

        $query = Letter::query()->with('creator')->latest();

        if ($this->hasSearchAttackPattern($this->search)) {
            $query->whereRaw('1 = 0');
        } else {
            $keyword = trim($this->search);

            if ($keyword !== '') {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('letter_number', 'like', '%' . $keyword . '%')
                        ->orWhere('subject', 'like', '%' . $keyword . '%')
                        ->orWhere('sender_name', 'like', '%' . $keyword . '%')
                        ->orWhere('recipient_name', 'like', '%' . $keyword . '%');
                });
            }
        }

        if ($this->typeFilter !== '') {
            $query->where('letter_type', $this->typeFilter);
        }

        if ($this->categoryFilter !== '') {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->dateFrom !== '') {
            $query->whereDate('letter_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo !== '') {
            $query->whereDate('letter_date', '<=', $this->dateTo);
        }

        if ($this->monthFilter === 'this_month') {
            $query->whereBetween('letter_date', [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateString(),
            ]);
        }

        return view('livewire.letter-index', [
            'letters' => $query->paginate(10),
            'categories' => Letter::CATEGORIES,
            'types' => [
                Letter::TYPE_IN => 'Surat Masuk',
                Letter::TYPE_OUT => 'Surat Keluar',
            ],
            'canManage' => auth()->user()->can('manage-letters'),
        ])->title('Data Surat');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->letter_number = '';
        $this->letter_type = Letter::TYPE_IN;
        $this->letter_date = '';
        $this->sender_name = '';
        $this->sender_email = '';
        $this->recipient_name = '';
        $this->category = '';
        $this->subject = '';
        $this->description = '';
        $this->file = null;
        $this->existingFileName = null;
        $this->resetValidation();
    }

    private function safeOriginalName(string $name): string
    {
        $name = preg_replace('/[^A-Za-z0-9._\- ]/', '_', $name) ?: 'surat';

        return mb_substr($name, 0, 150);
    }

    private function hasSearchAttackPattern(string $value): bool
    {
        return (bool) preg_match('/<[^>]*>|(script|select\s+|insert\s+|update\s+|delete\s+|drop\s+|--|;)/i', $value);
    }
}
