<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->middleware('auth');
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

            if ($user->role === 'dosen1') {
                $allowedKriteriaIds = [1, 2, 3];
            } elseif ($user->role === 'dosen2') {
                $allowedKriteriaIds = [4, 5, 6];
            } elseif ($user->role === 'dosen3') {
                $allowedKriteriaIds = [7, 8, 9];
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
        // Hanya admin yang bisa membuat template
        if (Auth::user()->role !== 'administrator') {
            return redirect()->route('templates.index')
                ->with('error', 'Anda tidak memiliki izin untuk membuat template.');
        }

        $kriteria = Kriteria::all();
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
        // Hanya admin yang bisa membuat template
        if (Auth::user()->role !== 'administrator') {
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

        $template = new Template($validated);
        $template->created_by = Auth::id();
        $template->save();

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

            if ($user->role === 'dosen1') {
                $allowedKriteriaIds = [1, 2, 3];
            } elseif ($user->role === 'dosen2') {
                $allowedKriteriaIds = [4, 5, 6];
            } elseif ($user->role === 'dosen3') {
                $allowedKriteriaIds = [7, 8, 9];
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
        // Hanya admin yang bisa mengedit template
        if (Auth::user()->role !== 'administrator') {
            return redirect()->route('templates.index')
                ->with('error', 'Anda tidak memiliki izin untuk mengedit template.');
        }

        $kriteria = Kriteria::all();
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
        // Hanya admin yang bisa memperbarui template
        if (Auth::user()->role !== 'administrator') {
            return redirect()->route('templates.index')
                ->with('error', 'Anda tidak memiliki izin untuk memperbarui template.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'kriteria_id' => 'required|exists:kriteria,id',
            'ppepp_type' => 'required|in:penetapan,pelaksanaan,evaluasi,pengendalian,peningkatan',
        ]);

        $template->update($validated);

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil diperbarui.');
    }

    /**
     * Menghapus template
     */
    public function destroy(Template $template)
    {
        // Hanya admin yang bisa menghapus template
        if (Auth::user()->role !== 'administrator') {
            return redirect()->route('templates.index')
                ->with('error', 'Anda tidak memiliki izin untuk menghapus template.');
        }

        $template->delete();

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

            if (auth()->user()->role === 'dosen1') {
                $allowedKriteriaIds = [1, 2, 3];
            } elseif (auth()->user()->role === 'dosen2') {
                $allowedKriteriaIds = [4, 5, 6];
            } elseif (auth()->user()->role === 'dosen3') {
                $allowedKriteriaIds = [7, 8, 9];
            }

            if (!in_array($template->kriteria_id, $allowedKriteriaIds)) {
                return redirect()->route('templates.index')
                    ->with('error', 'Anda tidak memiliki akses untuk mengunduh template ini.');
            }
        }

        // Buat nama file yang aman
        $filename = Str::slug($template->name) . '-' . date('YmdHis') . '.docx';

        // Header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Konversi HTML ke Word menggunakan PhpWord
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        // Import HTML
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $template->content);

        // Simpan ke output
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('php://output');
        exit;
    }
}
