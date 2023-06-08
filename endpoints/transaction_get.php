<?php

function api_transaction_get($request){
    $user = wp_get_current_user();
    $user_id = $user->ID;

    $type = sanitize_text_field($request["type"]) ?: "buyer_id";

    if($user_id){
        $login = get_userdata($user_id)->user_login;

        $meta_query = null;
        if($type){
            $meta_query = [
                "key" => $type,
                "value" => $login,
                "compare" => "="
            ];
        }

        $query = [
            "post_type" => "transaction",
            "orderby" => "date",
            "posts_per_page" => -1,
            "meta_query" => [
                $meta_query,
            ],
        ];

        $loop = new WP_Query($query);
        $posts = $loop->posts;
        $response = [];

        foreach($posts as $key => $value){
            $post_id = $value->ID;
            $post_meta = get_post_meta($post_id);

            $response[] = [
                "buyer_id" => $post_meta["buyer_id"][0],
                "seller_id" => $post_meta["seller_id"][0],
                "address" => json_decode($post_meta["address"][0]),
                "product" => json_decode($post_meta["product"][0]),
                "date" => $value->post_date
            ];
        }
    } else{
        $response = new WP_Error("permissao", "Usuário não possui permissão", ["status" => 401]);
    }

    return rest_ensure_response($response);
}

function register_api_transaction_get(){
    register_rest_route("api", "/transacao", [
        [
            "methods" => WP_REST_Server::READABLE,
            "callback" => "api_transaction_get"
        ]
    ]);
}
add_action("rest_api_init", "register_api_transaction_get");