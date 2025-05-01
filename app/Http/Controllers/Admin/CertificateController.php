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
        $type = strtolower($request->type);
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slugName = Str::slug($originalName);
        $filename = $slugName . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $directory = 'certificates';

        $relativePath = $file->storeAs($directory, $filename);

        if ($type === 'pfx') {
            $fullPath = Storage::disk('local')->path($relativePath);
            $mergedPemRelative = $this->extractPemFromPfx($fullPath, $request->password, $slugName . '-' . uniqid(), $directory);

            if (!$mergedPemRelative) {
                return back()->withInput()->with('toast', [
                    'type' => 'error',
                    'message' => 'Erro ao extrair o certificado PEM do PFX.',
                    'duration' => 5000,
                ]);
            }

            $relativePath = $mergedPemRelative;
        }

        Certificate::create([
            'name' => $request->name,
            'type' => strtolower($request->type),
            'path' => $relativePath,
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
        $type = strtolower($request->type);

        if ($request->hasFile('file')) {
            Storage::delete($certificate->path);

            $file = $request->file('file');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $slugName = Str::slug($originalName);
            $filename = $slugName . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $directory = 'certificates';

            $relativePath = $file->storeAs($directory, $filename);

            if ($type === 'pfx') {
                $fullPath = Storage::disk('local')->path($relativePath);
                $mergedPemRelative = $this->extractPemFromPfx($fullPath, $request->password, $slugName . '-' . uniqid(), $directory);

                if (!$mergedPemRelative) {
                    return back()->withInput()->with('toast', [
                        'type' => 'error',
                        'message' => 'Erro ao extrair o certificado PEM do PFX.',
                        'duration' => 5000,
                    ]);
                }

                $relativePath = $mergedPemRelative;
            }

            $certificate->path = $relativePath;
            $certificate->original_name = $file->getClientOriginalName();
        }

        $updateData = [
            'name' => $request->name,
            'type' => strtolower($request->type),
            'path' => $certificate->path,
            'original_name' => $certificate->original_name,
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

    private function extractPemFromPfx(string $pfxPath, string $password, string $baseFilename, string $directory): ?string
    {
        $storagePath = Storage::disk('local')->path($directory);

        // Define os caminhos completos
        $privateKeyPath = "$storagePath/{$baseFilename}_private.pem";
        $publicCertPath = "$storagePath/{$baseFilename}_public.pem";
        $mergedPemPath = "$storagePath/{$baseFilename}_full.pem";

        // Extrai chave privada
        $extractKeyCommand = "openssl pkcs12 -in $pfxPath -nocerts -nodes -passin pass:$password -out $privateKeyPath 2>&1";
        shell_exec($extractKeyCommand);
        if (!file_exists($privateKeyPath)) return null;

        // Extrai certificado público
        $extractCertCommand = "openssl pkcs12 -in $pfxPath -clcerts -nokeys -passin pass:$password -out $publicCertPath 2>&1";
        shell_exec($extractCertCommand);
        if (!file_exists($publicCertPath)) return null;

        // Junta os dois arquivos em um .pem completo
        file_put_contents($mergedPemPath, file_get_contents($privateKeyPath) . "\n" . file_get_contents($publicCertPath));

        return Str::of($mergedPemPath)->after(Storage::disk('local')->path(''))->toString(); // caminho relativo
    }
}
