<?php

namespace App\Http\Controllers\Api;

use App\Events\CommentEvent;
use App\Models\User;
use App\Notifications\CommentNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Events\NotificationEvent;
use App\Models\Post;

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
        if ($user->id != auth()->user()->id) {
        $user->notify(new CommentNotification(auth()->user(), $post, $comment));
        broadcast(new NotificationEvent(auth()->user(), $post, 'comment'))->toOthers();
        broadcast(new CommentEvent($post, $comment))->toOthers();
        };
        return response()->json(['success' => 'Comment posted successfully']);
    }
    public function deleteComm(Request $request){
        $comment = Comment::find($request->id);
        $comment->delete();
        return response()->json(['success' => 'Comment deleted successfully']);
    }
    public function getComment(Post $post){
        $comments = $post->comments()->with('user.profile')->get();
        return response()->json(['comments' => $comments]);
    }
}
