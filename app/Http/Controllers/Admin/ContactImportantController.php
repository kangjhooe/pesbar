<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactImportant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactImportantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = ContactImportant::ordered()->paginate(10);
        return view('admin.contact-importants.index', compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = [
            'polisi' => 'Polisi',
            'rumah_sakit' => 'Rumah Sakit',
            'pemadam_kebakaran' => 'Pemadam Kebakaran',
            'ambulans' => 'Ambulans',
            'posko_bencana' => 'Posko Bencana',
            'kantor_camat' => 'Kantor Camat',
            'puskesmas' => 'Puskesmas',
            'lainnya' => 'Lainnya'
        ];
        
        return view('admin.contact-importants.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ContactImportant::create($request->all());

        return redirect()->route('admin.contact-importants.index')
            ->with('success', 'Kontak penting berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactImportant $contactImportant)
    {
        return view('admin.contact-importants.show', compact('contactImportant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactImportant $contactImportant)
    {
        $types = [
            'polisi' => 'Polisi',
            'rumah_sakit' => 'Rumah Sakit',
            'pemadam_kebakaran' => 'Pemadam Kebakaran',
            'ambulans' => 'Ambulans',
            'posko_bencana' => 'Posko Bencana',
            'kantor_camat' => 'Kantor Camat',
            'puskesmas' => 'Puskesmas',
            'lainnya' => 'Lainnya'
        ];
        
        return view('admin.contact-importants.edit', compact('contactImportant', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactImportant $contactImportant)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contactImportant->update($request->all());

        return redirect()->route('admin.contact-importants.index')
            ->with('success', 'Kontak penting berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactImportant $contactImportant)
    {
        $contactImportant->delete();

        return redirect()->route('admin.contact-importants.index')
            ->with('success', 'Kontak penting berhasil dihapus.');
    }

    /**
     * Toggle status aktif/tidak aktif
     */
    public function toggleStatus(ContactImportant $contactImportant)
    {
        $contactImportant->update(['is_active' => !$contactImportant->is_active]);
        
        $status = $contactImportant->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', "Kontak penting berhasil {$status}.");
    }

    /**
     * Bulk activate contacts
     */
    public function bulkActivate(Request $request)
    {
        $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contact_importants,id'
        ]);

        $count = ContactImportant::whereIn('id', $request->contact_ids)
            ->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengaktifkan {$count} kontak penting.",
            'count' => $count
        ]);
    }

    /**
     * Bulk deactivate contacts
     */
    public function bulkDeactivate(Request $request)
    {
        $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contact_importants,id'
        ]);

        $count = ContactImportant::whereIn('id', $request->contact_ids)
            ->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil menonaktifkan {$count} kontak penting.",
            'count' => $count
        ]);
    }

    /**
     * Bulk delete contacts
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contact_importants,id'
        ]);

        $count = ContactImportant::whereIn('id', $request->contact_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "Berhasil menghapus {$count} kontak penting.",
            'count' => $count
        ]);
    }

    /**
     * Export contacts to CSV
     */
    public function export()
    {
        $contacts = ContactImportant::ordered()->get();
        
        $filename = 'kontak_penting_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($contacts) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'No',
                'Nama',
                'Jenis',
                'Telepon',
                'Alamat',
                'Deskripsi',
                'Status',
                'Urutan',
                'Tanggal Dibuat',
                'Tanggal Diperbarui'
            ]);

            // CSV Data
            foreach ($contacts as $index => $contact) {
                fputcsv($file, [
                    $index + 1,
                    $contact->name,
                    ucwords(str_replace('_', ' ', $contact->type)),
                    $contact->phone,
                    $contact->address,
                    $contact->description,
                    $contact->is_active ? 'Aktif' : 'Tidak Aktif',
                    $contact->sort_order,
                    $contact->created_at->format('d-m-Y H:i:s'),
                    $contact->updated_at->format('d-m-Y H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
