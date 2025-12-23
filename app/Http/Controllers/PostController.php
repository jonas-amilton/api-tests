<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();

        return response()->json([
            'data' => $posts,
            'message' => 'Posts listados com sucesso',
            'errors' => null,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $post = Post::create($data);

        return response()->json([
            'data' => $post,
            'message' => 'Post criado com sucesso',
            'errors' => null,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response()->json([
            'data' => $post,
            'message' => 'Post encontrado com sucesso',
            'errors' => null,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $post->update($data);

        return response()->json([
            'data' => $post,
            'message' => 'Post atualizado com sucesso',
            'errors' => null,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json([
            'data' => null,
            'message' => 'Post deletado com sucesso',
            'errors' => null,
        ], 200);
    }
}