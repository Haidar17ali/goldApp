<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(){
        $employees = Employee::orderBy('fullname', 'asc')->paginate(15);
        return view('pages.employees.index', compact('employees'));
    }

    public function create(){
        return view('pages.employees.create');
    }

    public function store(Request $request){
        // 
    }
}
