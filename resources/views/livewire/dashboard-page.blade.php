<div class="livewire-page-root">
<div class="page-header">
    <div>
        <p class="eyebrow">Dashboard</p>
        <h1>Sistem Administrasi Surat Masuk dan Surat Keluar</h1>
        <p class="muted">Ringkasan data surat dan akses cepat ke daftar surat.</p>
    </div>
    <a href="{{ route('letters.index') }}" class="button button-primary">Kelola Data Surat</a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span>Total Surat</span>
        <strong>{{ $totalLetters }}</strong>
    </div>
    <div class="stat-card">
        <span>Surat Masuk</span>
        <strong>{{ $incomingLetters }}</strong>
    </div>
    <div class="stat-card">
        <span>Surat Keluar</span>
        <strong>{{ $outgoingLetters }}</strong>
    </div>
    <div class="stat-card">
        <span>Bulan Ini</span>
        <strong>{{ $thisMonthLetters }}</strong>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h2>Surat Terbaru</h2>
            <p class="muted">Lima data surat terakhir yang tercatat pada sistem.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Jenis</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Perihal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($latestLetters as $letter)
                    <tr>
                        <td>{{ $letter->letter_number }}</td>
                        <td><span class="badge">{{ $letter->type_label }}</span></td>
                        <td>{{ $letter->letter_date->format('d-m-Y') }}</td>
                        <td>{{ $letter->category }}</td>
                        <td>{{ $letter->subject }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">Belum ada data surat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
