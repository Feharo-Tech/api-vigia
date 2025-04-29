<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiStoreRequest;
use App\Http\Requests\ApiUpdateRequest;
use App\Models\Api;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ApiController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    
    public function index()
    {
        return view('apis.index');
    }

    public function create()
    {
        return view('apis.create', [
            'methods' => Api::HTTP_METHODS,
            'intervals' => Api::CHECK_INTERVALS,
            'tags' => Tag::all(),
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
        $this->authorize('view', $api);

        $statusChecks = $api->statusChecks()->latest()->paginate(10);
        $uptimeStats = $api->uptimeStats();

        return view('apis.show', compact('api', 'statusChecks', 'uptimeStats'));
    }

    public function edit(Api $api)
    {
        $this->authorize('update', $api);

        $api->formatJsonFields();

        return view('apis.edit', [
            'api' => $api,
            'methods' => Api::HTTP_METHODS,
            'intervals' => Api::CHECK_INTERVALS,
            'tags' => Tag::all(),
            'selectedTags' => $api->tags->pluck('id')->toArray()
        ]);
    }

    public function update(ApiUpdateRequest $request, Api $api)
    {
        $this->authorize('update', $api);

        $api->updateFromRequest($request->validated());

        $api->tags()->sync($request->input('tags', []));

        return redirect()->route('apis.show', $api)
            ->with('toast', ['type' => 'success', 'message' => 'API atualizada com sucesso!', 'duration' => 5000]);
    }

    public function destroy(Api $api)
    {
        $this->authorize('delete', $api);

        $api->delete();

        return redirect()->route('apis.index')
            ->with('toast', ['type' => 'success', 'message' => 'API removida com sucesso!', 'duration' => 5000]);
    }

    public function checkNow(Api $api)
    {
        $this->authorize('view', $api);

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
        $this->authorize('view', $api);
        return response()->json($api->statusHistory());
    }

    public function reset(Api $api)
    {
        $this->authorize('reset', $api);

        $api->statusChecks()->delete();

        return redirect()->route('apis.show', $api)
            ->with('toast', ['type' => 'success', 'message' => 'API resetada com sucesso!', 'duration' => 5000]);
    }
}
