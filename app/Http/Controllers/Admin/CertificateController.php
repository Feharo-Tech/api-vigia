<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CertificateStoreRequest;
use App\Http\Requests\CertificateUpdateRequest;
use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;


class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $certificates = Certificate::all();
        return view('admin.certificates.index', compact('certificates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.certificates.create', [
            'types' => Certificate::TYPES
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CertificateStoreRequest $request)
    {
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = Str::slug($originalName) . '-' . uniqid() . '.' . $extension;

        $path = $file->storeAs('certificates', $filename);

        Certificate::create([
            'name' => $request->name,
            'type' => strtolower($request->type),
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'password' => $request->password ? Crypt::encryptString($request->password) : null,
        ]);

        return redirect()->route('admin.certificates.index')->with('toast', [
            'type' => 'success',
            'message' => 'Certificado adicionado com sucesso!',
            'duration' => 5000,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certificate $certificate)
    {
        return view('admin.certificates.edit', [
            'certificate'=> $certificate,
            'types' => Certificate::TYPES
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CertificateUpdateRequest $request, Certificate $certificate)
    {
        if ($request->hasFile('file')) {
            Storage::delete($certificate->path);

            $certificate->path = $request->file('file')->store('certificates');
            $certificate->original_name = $request->file('file')->getClientOriginalName();
        }

        $updateData = [
            'name' => $request->name,
            'type' => strtolower($request->type),
            'path' => $certificate->path,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Crypt::encryptString($request->password);
        }

        $certificate->update($updateData);

        return redirect()->route('admin.certificates.index')->with('toast', [
            'type' => 'success',
            'message' => 'Certificado atualizado com sucesso.',
            'duration' => 5000,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certificate $certificate)
    {
        Storage::delete($certificate->path);
        $certificate->delete();

        return redirect()->route('admin.certificates.index')->with('toast', [
            'type' => 'success',
            'message' => 'Certificado removido!',
            'duration' => 5000,
        ]);
    }
}
