<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['category', 'user', 'reviews'])->get();
        return response()->json([
            'status' => 'success',
            'data'   => $posts,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title'              => 'required|string|max:255',
                'description'        => 'required|string',
                'price'              => 'required|numeric',
                'number_of_employee' => 'required|integer',
                'posted_by'          => 'required|exists:users,id',
                'category_id'        => 'required|exists:categories,id',
            ]);

            $post = Post::create($request->all());

            return response()->json([
                'status' => 'success',
                'data'   => $post,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    public function show($id)
    {
        $post = Post::with(['category', 'user', 'reviews'])->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data'   => $post,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title'              => 'required|string|max:255',
                'description'        => 'required|string',
                'price'              => 'required|numeric',
                'number_of_employee' => 'required|integer',
                'posted_by'          => 'required|exists:users,id',
            ]);
            $post = Post::find($id);
            if (! $post) {
                return response()->json([
                    'message' => 'Post not found',
                ], 404);
            }
            $post->update($request->all());
            return response()->json([
                'status' => 'success',
                'data'   => $post,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        if (! $post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }
        $post->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Post deleted successfully',
        ]);
    }
}
