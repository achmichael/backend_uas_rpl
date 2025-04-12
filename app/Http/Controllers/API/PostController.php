<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Posts",
 *     description="Data terkait postingan"
 * )
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Web Developer Needed"),
 *     @OA\Property(property="description", type="string", example="Looking for an experienced web developer..."),
 *     @OA\Property(property="price", type="number", format="float", example=1000),
 *     @OA\Property(property="number_of_employee", type="integer", example=2),
 *     @OA\Property(property="posted_by", type="uuid", example=1),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Get list of posts",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query for post title or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of posts",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Post")
     *             )
     *         )
     *     )
     * )
     */

    public function index(Request $request)
    {
        if ($request->has('q')) {
            return $this->search($request);
        }

        $posts = Post::with(['category', 'user', 'reviews', 'level'])->get();
        return response()->json([
            'status' => 'success',
            'data'   => $posts,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Create a new post",
     *     tags={"Posts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "price", "number_of_employee", "posted_by", "category_id"},
     *             @OA\Property(property="title", type="string", example="Web Developer Needed"),
     *             @OA\Property(property="description", type="string", example="Looking for an experienced web developer..."),
     *             @OA\Property(property="price", type="number", format="float", example=1000),
     *             @OA\Property(property="number_of_employee", type="integer", example=2),
     *             @OA\Property(property="posted_by", type="integer", example=1),
     *             @OA\Property(property="category_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Post")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

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
            $data            = $request->all();
            $data['user_id'] = auth()->id;
            $post            = Post::create($data);

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

    public function search(Request $request)
    {
        $posts = Post::with(['category', 'user', 'company', 'reviews'])
            ->where('title', 'like', '%' . $request->q . '%')
            ->orWhere('description', 'like', '%' . $request->q . '%')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $posts,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Get post details",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Post")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post not found bro")
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        $post = Post::with(['category', 'user', 'reviews'])->find($id);
        if (! $post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found bro',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data'    => $post,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Update a post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "price", "number_of_employee", "posted_by"},
     *             @OA\Property(property="title", type="string", example="Updated Web Developer Job"),
     *             @OA\Property(property="description", type="string", example="Updated job description..."),
     *             @OA\Property(property="price", type="number", format="float", example=1500),
     *             @OA\Property(property="number_of_employee", type="integer", example=3),
     *             @OA\Property(property="posted_by", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Post")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

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

    /**
     * @OA\Get(
     *     path="/api/posts/{id}/recommend-freelancer",
     *     summary="Get recommended freelancers for a post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of recommended freelancers",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     )
     * )
     */

    public function recommendFreelancer($id, PostService $postService)
    {
        $post = Post::find($id);
        if (! $post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $freelancers = $postService->matchingFreelancer($post);
        return response()->json([
            'status' => 'success',
            'data'   => $freelancers,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Delete a post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully",
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Post deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *
     * response=404,
     *        description="Post not found",
     * @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="Post not found")
     *        )
     *    )
     * )
     */

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
