<?php

namespace App\Http\Controllers;

use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TodoApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(TodoResource::collection(Todo::all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        DB::beginTransaction();
        try {
            if (Todo::query()->where('title', $request->title)->exists()) {
                return response('Todo title already exist',422);
            }

           $todo = Todo::query()->create($request->all());
            DB::commit();
            return \response(new TodoResource($todo));
        }catch (\Exception $exception){
            DB::rollBack();
            return \response('Something went wrong', 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $todo = Todo::find($id);

        return  \response(new TodoResource($todo));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try
        {
            Todo::query()->find($id)->update($request->all());
            DB::commit();

            $shop = Todo::query()->find($id);
            return \response(new TodoResource($shop));

        }catch (\Exception $exception){
            DB::rollBack();
            return response('Something went wrong', 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        DB::beginTransaction();
        try {
            Todo::query()->find($id)->delete();
            DB::commit();
            return \response(new TodoResource(Todo::withTrashed()->find($id)));
        }catch (\Exception $exception){
            DB::rollBack();
            return response('Something went wrong',422);
        }
    }
}
