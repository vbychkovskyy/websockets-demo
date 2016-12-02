@extends('layouts.app')

@section('content')
    <!-- Bootstrap Boilerplate... -->
    <div class="panel-body">
        <!-- Display Validation Errors -->
        @include('common.errors')
        <form action="/post" method="POST" class="form-horizontal">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="post-name" class="col-sm-3 control-label">Post</label>

                <div class="col-sm-6">
                    <input type="text" name="name" id="post-name" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="post-body" class="col-sm-3 control-label">Content</label>

                <div class="col-sm-6">
                    <textarea name="body" id="post-body" class="form-control" rows="5"></textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <button type="submit" class="btn btn-default">
                        <i class="fa fa-plus"></i> Add Post
                    </button>
                </div>
            </div>
        </form>

        @if (count($posts) > 0)
            <div class="panel panel-default">
                <div class="panel-heading">Current Posts</div>

                <div class="panel-body">
                    <table class="table table-striped post-table">
                        <thead>
                            <th>Post</th>
                            <th>Content</th>
                            <th>Action</th>
                        </thead>

                        <tbody>
                        @foreach ($posts as $post)
                            <tr data-id="{{ $post->id }}">
                                <td class="table-text">
                                    <div>{{ $post->name }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ $post->content }}</div>
                                </td>
                                <td>
                                    <form action="/post/{{ $post->id }}" method="POST">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button>Delete Post</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <script type="text/javascript">
        /*var conn = new WebSocket('ws://localhost:8082');
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        conn.onmessage = function(e) {
            console.log(e.data);
        };
        conn.onerror = function (e) {
            console.log(e);
        }*/
    </script>

    <script src="//code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="//autobahn.s3.amazonaws.com/js/autobahn.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            var $table = $('.post-table');
            var conn2 = new ab.Session('ws://localhost:8083',
                function() {
                    conn2.subscribe('create', function(event, data) {
                        var csrf = '{{ csrf_field() }}';
                        var methodField = '{{ method_field('DELETE') }}';
                        var $tr = $('<tr data-id="' + data.id + '">' +
                            '<td class="table-text">' +
                                '<div>' + data.name + '</div>' +
                            '</td>' +
                            '<td class="table-text">' +
                                '<div>' + data.content + '</div>' +
                            '</td>' +
                            '<td>' +
                                '<form action="/post/' + data.id + '" method="POST">' + csrf + methodField +
                                    '<button>Delete Post</button>' +
                                '</form>' +
                            '</td>' +
                        '</tr>');
                        $table.find('tbody').append($tr);
                        console.log('New event "' + event);
                        console.log(data);
                    });
                    conn2.subscribe('delete', function(event, data) {
                        $('tr[data-id="' + data.id + '"]').remove();
                        console.log('New event "' + event);
                        console.log(data);
                    });
                },
                function() {
                    console.warn('WebSocket connection closed');
                },
                {'skipSubprotocolCheck': true}
            );
        });
    </script>
@endsection