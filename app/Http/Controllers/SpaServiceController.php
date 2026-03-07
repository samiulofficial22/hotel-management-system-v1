<?php

namespace App\Http\Controllers;

use App\Models\SpaService;
use App\Repositories\SpaServiceRepository;
use Illuminate\Http\Request;

class SpaServiceController extends Controller
{
    public function __construct(protected SpaServiceRepository $repository)
    {
    }

    public function index()
    {
        $services = $this->repository->all();
        return view('spa.services.index', compact('services'));
    }

    public function create()
    {
        return view('spa.services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $this->repository->create($data);
        return redirect()->route('spa.services.index')->with('success', 'Service created');
    }

    public function edit(SpaService $service)
    {
        return view('spa.services.edit', compact('service'));
    }

    public function update(Request $request, SpaService $service)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $this->repository->update($service, $data);
        return redirect()->route('spa.services.index')->with('success', 'Service updated');
    }

    public function destroy(SpaService $service)
    {
        $this->repository->delete($service);
        return redirect()->route('spa.services.index')->with('success', 'Service deleted');
    }
}
