<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

use function PHPSTORM_META\type;

class PositionController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::where('type', "Divisi")->orderBy('id', 'desc')->with(['children.children'])->get();
        // dd($positions[0]->children);
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
            'name' => 'required',
            "type" => 'required|in:Divisi,Departemen,Bagian'
        ]);

        $data = [
            "name" => $request->name,
            "type" => $request->type,
            "parent_id" => (int)$request->parent,
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
        $position = Position::with('children.children')->findOrFail($id);
        $types = ["Divisi", "Departemen", "Bagian"];
        return view('pages.positions.edit', compact(['types', 'position']));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $position = Position::with('children.children')->findOrFail($id);
        $request->validate([
            'name' => 'required',
            "type" => 'required|in:Divisi,Departemen,Bagian'
        ]);

        
            $position->name = $request->name;
            $position->type = $request->type;
            $position->parent_id= (int)$request->parent;
            $position->save();
        
        return redirect()->route('bagian.index')->with("status", "edited");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $position = Position::with('children.children')->findOrFail($id);

        if(count($position->children)){
            foreach ($position->children as $department) {
                if(count($department->children)){
                    foreach ($department->children as $subPosition) {
                        $subPosition->delete();
                    }
                }
                $department->delete();
            }
        }
        $position->delete();
        return redirect()->back()->with('status', 'deleted');
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
