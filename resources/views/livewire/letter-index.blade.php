<div class="livewire-page-root">
<div class="page-header">
    <div>
        <p class="eyebrow">Data Surat</p>
        <h1>Administrasi Surat Masuk/Keluar</h1>
        <p class="muted">Kelola pencatatan, arsip digital, pencarian, dan filter surat.</p>
    </div>

    @if($canManage)
        <button type="button" wire:click="create" class="button button-primary">+ Input Surat</button>
    @else
        <span class="role-info">Mode pimpinan: lihat dan download saja</span>
    @endif
</div>

@if (session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

@if($showForm && $canManage)
    <div class="card form-card">
        <div class="card-header">
            <div>
                <h2>{{ $editingId ? 'Edit Data Surat' : 'Input Data Surat' }}</h2>
                <p class="muted">Validasi dibuat sesuai kebutuhan EP dan BVA pada test plan.</p>
            </div>
            <button type="button" wire:click="cancel" class="button button-light">Tutup</button>
        </div>

        <form wire:submit="save" class="grid-form">
            <div>
                <label>Nomor Surat <span>*</span></label>
                <input type="text" wire:model="letter_number" maxlength="50" placeholder="001/ADM/VII/2026">
                @error('letter_number') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label>Jenis Surat <span>*</span></label>
                <select wire:model="letter_type">
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('letter_type') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label>Tanggal Surat <span>*</span></label>
                <input type="text" wire:model="letter_date" placeholder="DD-MM-YYYY, contoh 06-07-2026">
                @error('letter_date') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label>Kategori <span>*</span></label>
                <select wire:model="category">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $item)
                        <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                </select>
                @error('category') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label>Asal/Pengirim <span>*</span></label>
                <input type="text" wire:model="sender_name" placeholder="Nama instansi/pengirim">
                @error('sender_name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label>Email Pengirim</label>
                <input type="text" wire:model="sender_email" placeholder="admin@kantor.com">
                @error('sender_email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label>Tujuan/Penerima</label>
                <input type="text" wire:model="recipient_name" placeholder="Bagian/Unit tujuan">
                @error('recipient_name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label>Upload Berkas <span>*</span></label>
                <input type="file" wire:model="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                <small>Format: PDF/JPG/JPEG/PNG/DOC/DOCX. Maksimal 2 MB.</small>
                @if($existingFileName)
                    <small>File saat ini: {{ $existingFileName }}</small>
                @endif
                <div wire:loading wire:target="file" class="uploading">Mengunggah file...</div>
                @error('file') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="full-span">
                <label>Perihal <span>*</span></label>
                <input type="text" wire:model="subject" maxlength="101" placeholder="Maksimal 100 karakter">
                <small>{{ strlen($subject) }}/100 karakter</small>
                @error('subject') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="full-span">
                <label>Keterangan</label>
                <textarea rows="3" wire:model="description" placeholder="Catatan tambahan bila diperlukan"></textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions full-span">
                <button type="button" wire:click="cancel" class="button button-light">Batal</button>
                <button type="submit" class="button button-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Simpan</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
@endif

<div class="card">
    <div class="card-header compact">
        <div>
            <h2>Filter & Pencarian</h2>
            <p class="muted">Pencarian dapat dilakukan berdasarkan nomor, pengirim, penerima, atau perihal surat.</p>
        </div>
        <button type="button" wire:click="clearFilters" class="button button-light">Reset Filter</button>
    </div>

    <div class="filter-grid">
        <div>
            <label>Kata Kunci</label>
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari perihal/nomor/pengirim">
            @error('search') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label>Jenis</label>
            <select wire:model.live="typeFilter">
                <option value="">Semua</option>
                @foreach($types as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Kategori</label>
            <select wire:model.live="categoryFilter">
                <option value="">Semua</option>
                @foreach($categories as $item)
                    <option value="{{ $item }}">{{ $item }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Filter Cepat</label>
            <select wire:model.live="monthFilter">
                <option value="">Semua Periode</option>
                <option value="this_month">Surat Bulan Ini</option>
            </select>
        </div>

        <div>
            <label>Dari Tanggal</label>
            <input type="date" wire:model.live="dateFrom">
        </div>

        <div>
            <label>Sampai Tanggal</label>
            <input type="date" wire:model.live="dateTo">
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header compact">
        <div>
            <h2>Daftar Surat</h2>
            <p class="muted">Total data pada halaman ini mengikuti filter yang dipilih.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nomor Surat</th>
                    <th>Jenis</th>
                    <th>Tanggal</th>
                    <th>Pengirim</th>
                    <th>Kategori</th>
                    <th>Perihal</th>
                    <th>File</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($letters as $letter)
                    <tr wire:key="letter-{{ $letter->id }}">
                        <td>{{ $letters->firstItem() + $loop->index }}</td>
                        <td><strong>{{ $letter->letter_number }}</strong></td>
                        <td><span class="badge {{ $letter->letter_type === 'masuk' ? 'blue' : 'green' }}">{{ $letter->type_label }}</span></td>
                        <td>{{ $letter->letter_date->format('d-m-Y') }}</td>
                        <td>
                            {{ $letter->sender_name }}
                            @if($letter->sender_email)
                                <br><small>{{ $letter->sender_email }}</small>
                            @endif
                        </td>
                        <td>{{ $letter->category }}</td>
                        <td>{{ $letter->subject }}</td>
                        <td>
                            <a href="{{ route('letters.download', $letter) }}" class="link-button">Download</a>
                            <br><small>{{ $letter->formatted_file_size }}</small>
                        </td>
                        <td class="actions-cell">
                            @if($canManage)
                                <button type="button" wire:click="edit({{ $letter->id }})" class="button button-small button-light">Edit</button>
                                <button type="button" wire:click="delete({{ $letter->id }})" onclick="return confirm('Yakin ingin menghapus data surat ini?')" class="button button-small button-danger">Hapus</button>
                            @else
                                <span class="muted">View only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-state">Data surat tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $letters->links() }}
    </div>
</div>
</div>
