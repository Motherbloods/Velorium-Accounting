<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AuditLog::with('user')
            ->when($request->model_type, fn($q) => $q->where('model_type', 'like', '%' . $request->model_type . '%'))
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->orderByDesc('created_at')
            ->paginate(30);

        $modelTypes = AuditLog::select('model_type')->distinct()->pluck('model_type');

        return view('audit-logs.index', compact('logs', 'modelTypes'));
    }
}