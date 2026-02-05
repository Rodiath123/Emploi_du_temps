<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicClass;
use App\Models\Subject;
use App\Models\Room;
use Illuminate\Http\Request;

class ReferentialController extends Controller
{
    public function index() {
        $classes = AcademicClass::orderBy('created_at', 'desc')->get();
        $subjects = Subject::orderBy('created_at', 'desc')->get();
        $rooms = Room::orderBy('created_at', 'desc')->get();
        return view('admin.referential.index', compact('classes', 'subjects', 'rooms'));
    }

    public function storeRoom(Request $request) {
        $val = $request->validate(['name' => 'required', 'capacity' => 'required|integer']);
        Room::create($val);
        return back()->with('success', 'La salle a été ajoutée au référentiel.');
    }

    public function storeSubject(Request $request) {
        $val = $request->validate(['name' => 'required', 'code' => 'required|unique:subjects', 'credits' => 'required']);
        Subject::create($val);
        return back()->with('success', 'La matière est désormais disponible.');
    }

    public function storeClass(Request $request) {
        $val = $request->validate(['name' => 'required', 'code' => 'required|unique:academic_classes', 'level' => 'required']);
        AcademicClass::create($val);
        return back()->with('success', 'La classe a été configurée.');
    }

    public function destroyRoom($id) { Room::findOrFail($id)->delete(); return back()->with('success', 'Supprimé.'); }
    public function destroySubject($id) { Subject::findOrFail($id)->delete(); return back()->with('success', 'Supprimé.'); }
    public function destroyClass($id) { AcademicClass::findOrFail($id)->delete(); return back()->with('success', 'Supprimé.'); }
}