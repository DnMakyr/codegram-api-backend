<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Notifications\CommentNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function comment(Request $request)
    {
        $comment = new Comment();
        $comment->user_id = $request->commenter;
        $comment->post_id = $request->post;
        $comment->content = $request->comment;
        $comment->save();
        $post = $comment->post;
        $user = $comment->post->user;
        $user->notify(new CommentNotification(auth()->user(), $post, $comment));
        return response()->json(['success' => 'Comment posted successfully']);
    }
}
