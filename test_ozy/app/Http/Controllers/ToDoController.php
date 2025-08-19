<?php

namespace App\Http\Controllers;

use App\Models\ToDo;
use Illuminate\Http\Request;
use App\Http\Requests\StoreToDoRequest;
use App\Http\Requests\UpdateToDoRequest;

class ToDoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', null);
        $toDos = ToDo::when($status, function ($query, $status) {
            return $query->where('status', $status);
        })
            ->orderBy('status', 'asc')

            ->get();
        return response()->json([
            'message' => 'ToDo list retrieved successfully',
            'data' => $toDos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,completed',
        ]);
        try {
            // default status to pending
            $validatedData['status'] = $validatedData['status'] ?? 'pending';
            $toDo = ToDo::create($validatedData);

            return response()->json([
                'message' => 'ToDo created successfully',
                'data' => $toDo
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid status value'], 422);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $toDo = ToDo::findOrFail($id);
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,completed',
        ]);
        try {
            $toDo->update($validatedData);
            return response()->json([
                'message' => 'ToDo updated successfully',
                'data' => $toDo
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid status value'], 422);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $toDo = ToDo::findOrFail($id);
            $toDo->delete();
            return response()->json([
                'message' => 'ToDo deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'ToDo not found'], 404);
        }
    }

    // complete a single ToDo
    public function complete($id)
    {
        $toDo = ToDo::findOrFail($id);
        // dd($toDo);
        $toDo->update(['status' => 'completed']);
        return response()->json($toDo);
    }
    // array completed
    public function completeAll(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:to_dos,id',
            ]);
            $toDos = ToDo::whereIn('id', $validatedData['ids'])->get();
            // dd($toDos);
            foreach ($toDos as $toDo) {
                $toDo->update(['status' => 'completed']);
            }
            return response()->json($toDos);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }
        // dd($request->all());

    }
}
