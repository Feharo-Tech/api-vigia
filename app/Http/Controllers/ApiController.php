<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiStoreRequest;
use App\Http\Requests\ApiUpdateRequest;
use App\Models\Api;
use App\Models\Certificate;
use App\Models\Tag;

class ApiController extends Controller
{    
    public function index()
    {
        return view('apis.index');
    }

    public function create()
    {
        return view('apis.create', [
            'methods' => Api::HTTP_METHODS,
            'intervals' => Api::CHECK_INTERVALS,
            'contentTypes' => Api::CONTENT_TYPE,
            'tags' => Tag::all(),
            'certificates' => Certificate::all(),
        ]);
    }

    public function store(ApiStoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        
        $api = Api::create($data);

        if (isset($data['tags'])) {
            $api->tags()->sync($data['tags']);
        }

        return redirect()->route('apis.show', $api)->with('toast', [
            'type' => 'success',
            'message' => 'API adicionada com sucesso!',
            'duration' => 5000,
        ]);
    }

    public function show(Api $api)
    {
        $uptimeStats = $api->uptimeStats();
        return view('apis.show', compact('api', 'uptimeStats'));
    }

    public function edit(Api $api)
    {
        $api->formatJsonFields();

        return view('apis.edit', [
            'api' => $api,
            'methods' => Api::HTTP_METHODS,
            'intervals' => Api::CHECK_INTERVALS,
            'contentTypes' => Api::CONTENT_TYPE,
            'tags' => Tag::all(),
            'selectedTags' => $api->tags->pluck('id')->toArray(),
            'certificates' => Certificate::all(),
        ]);
    }

    public function update(ApiUpdateRequest $request, Api $api)
    {
        $api->updateFromRequest($request->validated());

        $api->tags()->sync($request->input('tags', []));

        return redirect()->route('apis.show', $api)
            ->with('toast', ['type' => 'success', 'message' => 'API atualizada com sucesso!', 'duration' => 5000]);
    }

    public function destroy(Api $api)
    {
        $api->delete();

        return redirect()->route('apis.index')
            ->with('toast', ['type' => 'success', 'message' => 'API removida com sucesso!', 'duration' => 5000]);
    }

    public function checkNow(Api $api)
    {
        try {
            $status = $api->performStatusCheck();
            return back()->with('toast', [
                'type' => $status['success'] ? 'success' : 'error',
                'message' => $status['message'],
                'duration' => 4000
            ]);
        } catch (\Exception $e) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Erro na verificação: ' . $e->getMessage(),
                'duration' => 5000
            ]);
        }
    }

    public function statusHistory(Api $api)
    {
        return response()->json($api->statusHistory());
    }

    public function reset(Api $api)
    {
        $api->statusChecks()->delete();

        return redirect()->route('apis.show', $api)
            ->with('toast', ['type' => 'success', 'message' => 'API resetada com sucesso!', 'duration' => 5000]);
    }
}
