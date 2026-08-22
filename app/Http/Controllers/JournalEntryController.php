<?php

namespace App\Http\Controllers;

use App\Exceptions\FiscalPeriodClosedException;
use App\Exceptions\JournalNotBalancedException;
use App\Models\CoaAccount;
use App\Models\JournalEntry;
use App\Services\JournalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JournalEntryController extends Controller
{
    public function __construct(protected JournalService $journalService)
    {
    }

    public function index(Request $request): View
    {
        $entries = JournalEntry::with('fiscalPeriod')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(20);

        return view('journal.index', compact('entries'));
    }

    public function create(): View
    {
        $accounts = CoaAccount::postable()->active()->orderBy('kode_akun')->get();

        return view('journal.create', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.coa_account_id' => ['required', 'exists:coa_accounts,id'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.kredit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $entry = $this->journalService->create([
                'tanggal' => $data['tanggal'],
                'keterangan' => $data['keterangan'] ?? null,
                'created_by' => $request->user()->id,
            ], $data['lines']);
        } catch (JournalNotBalancedException | FiscalPeriodClosedException $e) {
            return back()->withInput()->withErrors(['lines' => $e->getMessage()]);
        }

        return redirect()->route('journal.show', $entry)->with('status', 'Jurnal berhasil dibuat sebagai draft.');
    }

    public function show(JournalEntry $journal): View
    {
        $journal->load('details.coaAccount', 'fiscalPeriod', 'creator');

        return view('journal.show', ['entry' => $journal]);
    }

    public function submit(Request $request, JournalEntry $journal): RedirectResponse
    {
        try {
            $this->journalService->submit($journal, $request->user());
        } catch (JournalNotBalancedException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('status', 'Jurnal diajukan untuk persetujuan.');
    }

    public function approve(Request $request, JournalEntry $journal): RedirectResponse
    {
        $this->journalService->approve($journal, $request->user());

        return back()->with('status', 'Jurnal disetujui.');
    }

    public function reject(Request $request, JournalEntry $journal): RedirectResponse
    {
        $data = $request->validate([
            'catatan_penolakan' => ['required', 'string', 'max:255'],
        ]);

        $this->journalService->reject($journal, $request->user(), $data['catatan_penolakan']);

        return back()->with('status', 'Jurnal ditolak.');
    }

    public function backToDraft(JournalEntry $journal): RedirectResponse
    {
        $this->journalService->backToDraft($journal);

        return back()->with('status', 'Jurnal dikembalikan ke draft.');
    }

    public function post(JournalEntry $journal): RedirectResponse
    {
        try {
            $this->journalService->post($journal);
        } catch (JournalNotBalancedException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('status', 'Jurnal berhasil diposting.');
    }
}