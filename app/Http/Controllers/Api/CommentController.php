<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Notifications\CommentNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Events\NotificationEvent;

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
        broadcast(new NotificationEvent(auth()->user(), $post, 'comment'))->toOthers();
        return response()->json(['success' => 'Comment posted successfully']);
    }
    public function deleteComm(Request $request){
        $comment = Comment::find($request->id);
        $comment->delete();
        return response()->json(['success' => 'Comment deleted successfully']);
    }
}
