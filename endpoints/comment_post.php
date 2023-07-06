<?php
    function api_comment_post($request) {
        $user = wp_get_current_user();
        $user_id = $user->ID;
        
        if ($user_id == 0) {
            $response = new WP_Error('error', 'Usuário sem permissão.', ['status' => 401]);
            
            return rest_ensure_response($response);
        }
        
        $post_id = $request['id'];
        $comment_content = sanitize_text_field($request['comment']);

        if (empty($comment_content)) {
            $response = new WP_Error('error', 'Dados incompletos.', ['status' => 422]);
            
            return rest_ensure_response($response);
        }

        $response = [
            'comment_author' => $user->user_login,
            'comment_content' => $comment_content,
            'comment_post_ID' => $post_id,
            'user_id' => $user_id
        ];

        $comment_id = wp_insert_comment($response);
        $comment = get_comment($comment_id);
       
        return rest_ensure_response($comment);
    }

    function register_api_comment_post() {
        register_rest_route('api', '/comment/(?P<id>[\d]+)', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => 'api_comment_post'
        ]);
    }

    add_action('rest_api_init', 'register_api_comment_post');