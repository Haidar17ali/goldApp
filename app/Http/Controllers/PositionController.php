<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

use function PHPSTORM_META\type;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::orderBy('id', 'desc')->paginate(10);
        return view('pages.positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = ["Divisi", "Departemen", "Bagian"];
        return view('pages.positions.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:positions,name',
            "type" => 'required|in:Divisi,Departemen,Bagian'
        ]);

        $data = [
            "name" => $request->name,
            "type" => $request->type,
            "parent" => (int)$request->parent,
        ];

        Position::create($data);
        return redirect()->route('bagian.index')->with("status", "saved");
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function selectType(Request $request) {
        $typeValue = $request->type;
        $response = "";

        if($typeValue == "Departemen"){
            $response = Position::where("type", "Divisi")->with(['parent'])->get();
        }else if($typeValue == "Bagian"){
            $response = Position::where("type", "Departemen")->with('parent')->get();
        }
        // dd($response);
        return response()->json($response);
    }
}
