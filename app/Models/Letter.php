<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Letter extends Model
{
    use HasFactory;

    public const TYPE_IN = 'masuk';
    public const TYPE_OUT = 'keluar';

    public const CATEGORIES = [
        'Umum',
        'Keuangan',
        'Hukum',
        'Internal',
        'Eksternal',
        'Rahasia',
    ];

    protected $fillable = [
        'letter_number',
        'letter_type',
        'letter_date',
        'sender_name',
        'sender_email',
        'recipient_name',
        'category',
        'subject',
        'description',
        'file_path',
        'original_file_name',
        'file_mime',
        'file_size',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'letter_date' => 'date',
            'file_size' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->letter_type === self::TYPE_IN ? 'Surat Masuk' : 'Surat Keluar';
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (! $this->file_size) {
            return '-';
        }

        return number_format($this->file_size / 1024, 1) . ' KB';
    }
}
