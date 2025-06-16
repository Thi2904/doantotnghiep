<?php

namespace App\Http\Controllers;

use App\Models\CommentAndRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentAndRateController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'productID' => 'required|exists:products,productID',
            'contentComment' => 'required|string',
            'rate' => 'required|integer|min:1|max:5',
        ]);

        $user = Auth::user();
        if ($user->role !== 'customer') {
            return response()->json(['message' => 'Chỉ khách hàng mới được bình luận.'], 403);
        }

        $comment = CommentAndRate::create([
            'cusID' => $user->id,
            'productID' => $request->productID,
            'contentComment' => $request->contentComment,
            'rate' => $request->rate,
        ]);

        return back()->with('success', 'Bình luận đã được tạo.');
    }


    public function update(Request $request, $id)
    {
        $comment = CommentAndRate::findOrFail($id);

        if (Auth::id() !== $comment->cusID) {
            return response()->json(['message' => 'Bạn không có quyền cập nhật bình luận này.'], 403);
        }

        $request->validate([
            'contentComment' => 'sometimes|required|string',
            'rate' => 'sometimes|required|integer|min:1|max:5',
        ]);

        $comment->update($request->only(['contentComment', 'rate']));

        return response()->json($comment);
    }

    public function destroy($id)
    {
        $comment = CommentAndRate::findOrFail($id);

        if (Auth::id() !== $comment->cusID) {
            return response()->json(['message' => 'Bạn không có quyền xoá bình luận này.'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Đã xoá bình luận.']);
    }
}
