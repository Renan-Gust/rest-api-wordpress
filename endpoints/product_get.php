<?php

function product_schema($slug){
    $post_id = get_product_id_by_slug($slug);
    if($post_id){
        $post_meta = get_post_meta($post_id);

        $images = get_attached_media("image", $post_id);
        $images_array = null;

        if($images){
            $images_array = [];

            foreach($images as $key => $value){
                $images_array[] = [
                    "title" => $value->post_name,
                    "src" => $value->guid,
                ];
            }
        }

        $response = [
            "id" => $slug,
            "photos" => $images_array,
            "name" => $post_meta["name"][0],
            "description" => $post_meta["description"][0],
            "price" => $post_meta["price"][0],
            "sold" => $post_meta["sold"][0],
            "user_id" => $post_meta["user_id"][0],
        ];
    } else{
        $response = new WP_Error("inexistente", "Produto não encontrado", ["status" => 404]);
    }

    return $response;
}

function api_product_get($request){
    $slug = $request['slug'];

    $response = product_schema($slug);
    
    return rest_ensure_response($response);
}

function register_api_product_get(){
    register_rest_route("api", "/produto/(?P<slug>[-\w]+)", [
        [
            "methods" => WP_REST_Server::READABLE,
            "callback" => "api_product_get"
        ]
    ]);
}
add_action("rest_api_init", "register_api_product_get");

function api_products_get($request){
    $q = sanitize_text_field($request["q"]) ?: '';
    $page = sanitize_text_field($request["page"]) ?: 0;
    $limit = sanitize_text_field($request["limit"]) ?: 9;
    $user_id = sanitize_text_field($request["user_id"]);

    $user_id_query = null;
    if($user_id){
        $user_id_query = [
            "key" => "user_id",
            "value" => $user_id,
            "compare" => "="
        ];
    }

    $sold = [
        "key" => "sold",
        "value" => "false",
        "compare" => "="
    ];

    $query = [
        "post_title" => "products",
        "posts_per_page" => $limit,
        "paged" => $page,
        "s" => $q,
        "meta_query" => [
            $user_id_query,
            $sold
        ],
    ];

    $loop = new WP_Query($query);
    $posts = $loop->posts;
    $total = $loop->found_posts;

    $products = [];
    foreach($posts as $key => $value){
        $products[] = product_schema($value->post_name);
    }

    $response = rest_ensure_response($products);
    $response->header("X-Total-Count", $total);

    return $response;
}

function register_api_products_get(){
    register_rest_route("api", "/produto", [
        [
            "methods" => WP_REST_Server::READABLE,
            "callback" => "api_products_get"
        ]
    ]);
}
add_action("rest_api_init", "register_api_products_get");