<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \App\Post;

class PostController extends Controller
{
    public function show() {
        $posts = Post::orderBy('created_at', 'asc')->get();

        return view('posts', [
            'posts' => $posts
        ]);
    }

    public function save(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'body' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('/')
                ->withInput()
                ->withErrors($validator);
        }

        $post = new Post();
        $post->name = $request->name;
        $post->content = $request->body;
        $post->save();

        $data = $post->toArray();
        $data['event_type'] = 'create';
        $this->_sendPushMessage($data);

        return redirect('/');
    }

    public function delete($id) {
        Post::findOrFail($id)->delete();
        $data['id'] = $id;
        $data['event_type'] = 'delete';
        $this->_sendPushMessage($data);
        return redirect('/');
    }

    protected function _sendPushMessage(array $data = array()) {
        //send push message
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'pusher');
        $socket->connect("tcp://localhost:5555");
        $socket->send(json_encode($data));
    }


}
