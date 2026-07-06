<?php

namespace App\Livewire;

use App\Models\Letter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DashboardPage extends Component
{
    public function render()
    {
        Gate::authorize('view-letters');

        $start = Carbon::now()->startOfMonth()->toDateString();
        $end = Carbon::now()->endOfMonth()->toDateString();

        return view('livewire.dashboard-page', [
            'totalLetters' => Letter::count(),
            'incomingLetters' => Letter::where('letter_type', Letter::TYPE_IN)->count(),
            'outgoingLetters' => Letter::where('letter_type', Letter::TYPE_OUT)->count(),
            'thisMonthLetters' => Letter::whereBetween('letter_date', [$start, $end])->count(),
            'latestLetters' => Letter::latest()->limit(5)->get(),
        ])->title('Dashboard E-Agenda');
    }
}
