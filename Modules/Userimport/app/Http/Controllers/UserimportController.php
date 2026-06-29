<?php

namespace Modules\Userimport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Userimport\Models\Role;
use Modules\Userimport\Services\UserImporter;
use Throwable;

class UserimportController extends Controller
{
    public function index(): View
    {
        return view('userimport::index', [
            'path' => config('userimport.path'),
            'scheduleTime' => config('userimport.schedule_time'),
            'userCount' => User::count(),
            'roleCount' => Role::count(),
            'summary' => session('userimport.summary'),
            'error' => session('userimport.error'),
        ]);
    }

    public function run(Request $request, UserImporter $importer): RedirectResponse
    {
        $request->validate([
            'file' => ['nullable', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        try {
            $path = $request->hasFile('file')
                ? $request->file('file')->getRealPath()
                : config('userimport.path');

            $summary = $importer->import($path);
        } catch (Throwable $e) {
            return redirect()->route('userimport.index')->with('userimport.error', $e->getMessage());
        }

        return redirect()->route('userimport.index')->with('userimport.summary', $summary);
    }
}
