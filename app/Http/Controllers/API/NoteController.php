<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Repositories\NoteRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    /**
     * @var NoteRepository
     */
    private $notes;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(NoteRepository $notes)
    {
        $this->middleware('auth');
        $this->notes = $notes;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {

        $data = $this->notes->forUser($request->user());
        return response()->json([NoteResource::collection($data), 'Notes fetched.']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'note' => 'required|string|max:1000',
            'title' => 'required|string|max:50',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $note = Note::create([
            'note' => $request->note,
            'title' => $request->title,
            'user_id' => auth()->id()
        ]);


        return response()->json(['Note created successfully.', new NoteResource($note)]);
    }


    /**
     * @param $id
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Note $note)
    {
        $this->authorize('view', $note);
        if (is_null($note)) {
            return response()->json('Data not found', 404);
        }
        return response()->json([new NoteResource($note)]);
    }

    /**
     * @param Request $request
     * @param Note $note
     * @return JsonResponse
     */
    public function update(Request $request, Note $note)
    {
        $this->authorize('update', $note);
        $validator = Validator::make($request->all(),[
            'note' => 'required|string|max:1000',
            'title' => 'required|string|max:50',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }



        $note->title = $request->title;
        $note->note = $request->note;
        $note->save();

        return response()->json(['Note updated successfully.', new NoteResource($note)]);
    }

    /**
     * @param Note $note
     * @return JsonResponse
     */
    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);
        $note->delete();

        return response()->json('Note deleted successfully');
    }
}
