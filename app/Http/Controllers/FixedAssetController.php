<?php

namespace App\Http\Controllers;

use App\Models\CoaAccount;
use App\Models\FixedAsset;
use App\Services\DepreciationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FixedAssetController extends Controller
{
    public function __construct(protected DepreciationService $depreciationService)
    {
    }

    public function index(): View
    {
        $assets = FixedAsset::orderByDesc('tanggal_perolehan')->paginate(20);

        return view('fixed-assets.index', compact('assets'));
    }

    public function create(): View
    {
        $coaAsetOptions = CoaAccount::postable()->active()->whereIn('kode_akun', ['121', '123', '125'])->get();
        $coaAkumulasiOptions = CoaAccount::postable()->active()->whereIn('kode_akun', ['122', '124', '126'])->get();
        $coaPembayaranOptions = CoaAccount::postable()->active()->whereIn('kode_akun', ['111', '112', '211'])->get();

        return view('fixed-assets.create', compact('coaAsetOptions', 'coaAkumulasiOptions', 'coaPembayaranOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode_aset' => ['required', 'string', 'max:50', 'unique:fixed_assets,kode_aset'],
            'nama_aset' => ['required', 'string', 'max:150'],
            'coa_aset_id' => ['required', 'exists:coa_accounts,id'],
            'coa_akumulasi_penyusutan_id' => ['required', 'exists:coa_accounts,id'],
            'coa_pembayaran_id' => ['required', 'exists:coa_accounts,id'],
            'tanggal_perolehan' => ['required', 'date'],
            'harga_perolehan' => ['required', 'numeric', 'min:0.01'],
            'nilai_residu' => ['nullable', 'numeric', 'min:0'],
            'umur_manfaat_tahun' => ['required', 'integer', 'min:1'],
            'umur_manfaat_fiskal_tahun' => ['nullable', 'integer', 'min:1'],
            'metode_penyusutan' => ['required', 'in:garis_lurus,saldo_menurun_ganda'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $asset = FixedAsset::create([
                'kode_aset' => $data['kode_aset'],
                'nama_aset' => $data['nama_aset'],
                'coa_aset_id' => $data['coa_aset_id'],
                'coa_akumulasi_penyusutan_id' => $data['coa_akumulasi_penyusutan_id'],
                'tanggal_perolehan' => $data['tanggal_perolehan'],
                'harga_perolehan' => $data['harga_perolehan'],
                'nilai_residu' => $data['nilai_residu'] ?? 0,
                'umur_manfaat_tahun' => $data['umur_manfaat_tahun'],
                'umur_manfaat_fiskal_tahun' => $data['umur_manfaat_fiskal_tahun'] ?? null,
                'metode_penyusutan' => $data['metode_penyusutan'],
                'akumulasi_penyusutan' => 0,
                'nilai_buku' => $data['harga_perolehan'],
                'status' => 'aktif',
            ]);

            app(\App\Services\JournalService::class)->create([
                'tanggal' => $data['tanggal_perolehan'],
                'keterangan' => "Perolehan aset {$asset->nama_aset}",
                'referensi_type' => FixedAsset::class,
                'referensi_id' => $asset->id,
                'created_by' => $request->user()->id,
            ], [
                ['coa_account_id' => $data['coa_aset_id'], 'debit' => $data['harga_perolehan'], 'kredit' => 0],
                ['coa_account_id' => $data['coa_pembayaran_id'], 'debit' => 0, 'kredit' => $data['harga_perolehan']],
            ], 'AT');
        });

        return redirect()->route('fixed-assets.index')->with('status', 'Aset tetap berhasil ditambahkan.');
    }

    public function show(FixedAsset $fixedAsset): View
    {
        $fixedAsset->load('schedules');

        return view('fixed-assets.show', ['asset' => $fixedAsset]);
    }

    public function runDepreciation(Request $request, FixedAsset $fixedAsset): RedirectResponse
    {
        $data = $request->validate([
            'periode' => ['required', 'date'],
        ]);

        $schedule = $this->depreciationService->runForAsset($fixedAsset, $data['periode'], $request->user());

        if (!$schedule) {
            return back()->withErrors(['periode' => 'Penyusutan untuk periode ini sudah ada atau tidak ada beban tersisa.']);
        }

        return back()->with('status', 'Penyusutan berhasil dijalankan.');
    }

    public function dispose(Request $request, FixedAsset $fixedAsset): RedirectResponse
    {
        $data = $request->validate([
            'tanggal_pelepasan' => ['required', 'date'],
            'harga_jual_pelepasan' => ['required', 'numeric', 'min:0'],
        ]);

        $this->depreciationService->dispose($fixedAsset, $data['tanggal_pelepasan'], (string) $data['harga_jual_pelepasan'], $request->user());

        return redirect()->route('fixed-assets.index')->with('status', 'Aset berhasil dilepas.');
    }
}