<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\Kriteria;
use App\Services\HistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    protected $historyService;

    /**
     * Konstruktor
     */
    public function __construct(HistoryService $historyService)
    {
        $this->middleware('auth');
        $this->historyService = $historyService;
    }

    /**
     * Menampilkan daftar template
     */
    public function index()
    {
        $user = Auth::user();

        // Admin bisa melihat semua template
        if ($user->role === 'administrator') {
            $templates = Template::with(['kriteria', 'creator'])->get();
        } else {
            // Dosen hanya bisa melihat template untuk kriteria yang bisa mereka akses
            $allowedKriteriaIds = [];

            if ($user->role === 'dosen') {
                $allowedKriteriaIds = $user->kriteria_access ?? [];
            }

            $templates = Template::with(['kriteria', 'creator'])
                ->whereIn('kriteria_id', $allowedKriteriaIds)
                ->get();
        }

        return view('pages.templates.index', compact('templates'));
    }

    /**
     * Menampilkan form untuk membuat template baru
     */
    public function create()
    {
        $user = Auth::user();
        $allowedRoles = ['administrator', 'dosen'];

        // Cek apakah user memiliki izin untuk membuat template
        if (!in_array($user->role, $allowedRoles)) {
            return redirect()->route('templates.index')
                ->with('error', 'Anda tidak memiliki izin untuk membuat template.');
        }

        // Tentukan kriteria yang bisa diakses oleh user
        if ($user->role === 'administrator') {
            $kriteria = Kriteria::all();
        } else {
            $allowedKriteriaIds = [];
            if ($user->role === 'dosen') {
                $allowedKriteriaIds = $user->kriteria_access ?? [];
            }

            $kriteria = Kriteria::whereIn('id', $allowedKriteriaIds)->get();
        }

        $ppepp_types = [
            'penetapan' => 'Penetapan',
            'pelaksanaan' => 'Pelaksanaan',
            'evaluasi' => 'Evaluasi',
            'pengendalian' => 'Pengendalian',
            'peningkatan' => 'Peningkatan'
        ];

        return view('pages.templates.create', compact('kriteria', 'ppepp_types'));
    }

    /**
     * Menyimpan template baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $allowedRoles = ['administrator', 'dosen'];

        // Cek apakah user memiliki izin untuk membuat template
        if (!in_array($user->role, $allowedRoles)) {
            return redirect()->route('templates.index')
                ->with('error', 'Anda tidak memiliki izin untuk membuat template.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'kriteria_id' => 'required|exists:kriteria,id',
            'ppepp_type' => 'required|in:penetapan,pelaksanaan,evaluasi,pengendalian,peningkatan',
        ]);

        // Validasi tambahan untuk memastikan dosen hanya membuat template untuk kriteria yang mereka akses
        if ($user->role !== 'administrator') {
            $allowedKriteriaIds = [];
            if ($user->role === 'dosen') {
                $allowedKriteriaIds = $user->kriteria_access ?? [];
            }

            if (!in_array($validated['kriteria_id'], $allowedKriteriaIds)) {
                return redirect()->route('templates.create')
                    ->with('error', 'Anda hanya dapat membuat template untuk kriteria yang ditugaskan kepada Anda.')
                    ->withInput();
            }
        }

        $template = new Template($validated);
        $template->created_by = Auth::id();
        $template->save();
        
        // Catat aktivitas pembuatan template
        $this->historyService->recordTemplateActivity('create', $template->name);

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil dibuat.');
    }

    /**
     * Menampilkan detail template
     */
    public function show(Template $template)
    {
        $user = Auth::user();

        // Cek apakah user memiliki akses ke template ini
        if ($user->role !== 'administrator') {
            $allowedKriteriaIds = [];

            if ($user->role === 'dosen') {
                $allowedKriteriaIds = $user->kriteria_access ?? [];
            }

            if (!in_array($template->kriteria_id, $allowedKriteriaIds)) {
                return redirect()->route('templates.index')
                    ->with('error', 'Anda tidak memiliki akses ke template ini.');
            }
        }

        return view('pages.templates.show', compact('template'));
    }

    /**
     * Menampilkan form untuk mengedit template
     */
    public function edit(Template $template)
    {
        $user = Auth::user();
        $allowedRoles = ['administrator', 'dosen'];

        // Cek apakah user memiliki izin untuk mengedit template
        if (!in_array($user->role, $allowedRoles)) {
            return redirect()->route('templates.index')
                ->with('error', 'Anda tidak memiliki izin untuk mengedit template.');
        }

        // Jika bukan admin, cek apakah template ini berada dalam kriteria yang bisa diakses
        if ($user->role !== 'administrator') {
            $allowedKriteriaIds = [];
            if ($user->role === 'dosen') {
                $allowedKriteriaIds = $user->kriteria_access ?? [];
            }

            if (!in_array($template->kriteria_id, $allowedKriteriaIds)) {
                return redirect()->route('templates.index')
                    ->with('error', 'Anda hanya dapat mengedit template untuk kriteria yang ditugaskan kepada Anda.');
            }
        }

        // Tentukan kriteria yang bisa diakses oleh user
        if ($user->role === 'administrator') {
            $kriteria = Kriteria::all();
        } else {
            $allowedKriteriaIds = [];
            if ($user->role === 'dosen') {
                $allowedKriteriaIds = $user->kriteria_access ?? [];
            }

            $kriteria = Kriteria::whereIn('id', $allowedKriteriaIds)->get();
        }

        $ppepp_types = [
            'penetapan' => 'Penetapan',
            'pelaksanaan' => 'Pelaksanaan',
            'evaluasi' => 'Evaluasi',
            'pengendalian' => 'Pengendalian',
            'peningkatan' => 'Peningkatan'
        ];

        return view('pages.templates.edit', compact('template', 'kriteria', 'ppepp_types'));
    }

    /**
     * Memperbarui template
     */
    public function update(Request $request, Template $template)
    {
        $user = Auth::user();
        $allowedRoles = ['administrator', 'dosen'];

        // Cek apakah user memiliki izin untuk memperbarui template
        if (!in_array($user->role, $allowedRoles)) {
            return redirect()->route('templates.index')
                ->with('error', 'Anda tidak memiliki izin untuk memperbarui template.');
        }

        // Cek apakah dosen hanya dapat mengupdate template yang mereka buat
        if ($user->role === 'dosen' && $template->created_by !== $user->id) {
            return redirect()->route('templates.index')
                ->with('error', 'Anda hanya dapat memperbarui template yang Anda buat.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'kriteria_id' => 'required|exists:kriteria,id',
            'ppepp_type' => 'required|in:penetapan,pelaksanaan,evaluasi,pengendalian,peningkatan',
        ]);

        // Validasi tambahan untuk memastikan dosen hanya memperbarui template ke kriteria yang mereka akses
        if ($user->role !== 'administrator') {
            $allowedKriteriaIds = [];
            if ($user->role === 'dosen') {
                $allowedKriteriaIds = $user->kriteria_access ?? [];
            }

            if (!in_array($validated['kriteria_id'], $allowedKriteriaIds)) {
                return redirect()->route('templates.edit', $template)
                    ->with('error', 'Anda hanya dapat memperbarui template untuk kriteria yang ditugaskan kepada Anda.')
                    ->withInput();
            }
        }

        $template->update($validated);
        
        // Catat aktivitas update template
        $this->historyService->recordTemplateActivity('update', $template->name);

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil diperbarui.');
    }

    /**
     * Menghapus template
     */
    public function destroy(Template $template)
    {
        $user = Auth::user();
        $allowedRoles = ['administrator', 'dosen'];

        // Cek apakah user memiliki izin untuk menghapus template
        if (!in_array($user->role, $allowedRoles)) {
            return redirect()->route('templates.index')
                ->with('error', 'Anda tidak memiliki izin untuk menghapus template.');
        }

        // Cek apakah dosen hanya dapat menghapus template yang mereka buat
        if ($user->role === 'dosen' && $template->created_by !== $user->id) {
            return redirect()->route('templates.index')
                ->with('error', 'Anda hanya dapat menghapus template yang Anda buat.');
        }

        // Simpan nama template sebelum dihapus untuk log
        $templateName = $template->name;
        
        // Hapus template
        $template->delete();
        
        // Catat aktivitas penghapusan template
        $this->historyService->recordTemplateActivity('delete', $templateName);

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil dihapus.');
    }

    /**
     * Download template sebagai HTML
     */
    public function download(Template $template)
    {
        // Pastikan pengguna memiliki akses ke template ini
        if (auth()->user()->role !== 'administrator') {
            // Jika bukan admin, cek apakah user memiliki akses ke kriteria ini
            $allowedKriteriaIds = [];

            if (auth()->user()->role === 'dosen') {
                $allowedKriteriaIds = auth()->user()->kriteria_access ?? [];
            }

            if (!in_array($template->kriteria_id, $allowedKriteriaIds)) {
                return redirect()->route('templates.index')
                    ->with('error', 'Anda tidak memiliki akses untuk mengunduh template ini.');
            }
        }

        try {
            // Buat nama file yang aman
            $filename = Str::slug($template->name) . '-' . date('YmdHis') . '.docx';
            
            // Buat temporary file untuk menyimpan dokumen
            $tempFile = tempnam(sys_get_temp_dir(), 'word_');
            
            // Konversi HTML ke Word menggunakan PhpWord
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            
            // Import HTML
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $template->content);
            
            // Simpan ke file temporary
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempFile);
            
            // Return file sebagai download
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Template download error: ' . $e->getMessage());
            return redirect()->route('templates.index')
                ->with('error', 'Terjadi kesalahan saat mengunduh template: ' . $e->getMessage());
        }
    }
}
