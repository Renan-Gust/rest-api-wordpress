<?php

function api_user_post($request){
    $name = sanitize_text_field($request['name']);
    $email = sanitize_email($request['email']);
    $password = $request['password'];
    $cep = sanitize_text_field($request['cep']);

    $user_exists = username_exists($email);
    $email_exists = email_exists($email);

    if(!$user_exists && !$email_exists && $email && $password){
        $user_id = wp_create_user($email, $password, $email);

        $response = [
            "ID" => $user_id,
            "display_name" => $name,
            "first_name" => $name,
            "role" => "subscriber"
        ];

        wp_update_user($response);

        update_user_meta($user_id, "cep", $cep);
    } else{
        $response = new WP_Error("email", "Email já cadastrado", ["status" => 403]);
    }

    return rest_ensure_response($response);
}

function register_api_user_post(){
    register_rest_route("api", "/usuario", [
        [
            "methods" => WP_REST_Server::CREATABLE,
            "callback" => "api_user_post"
        ]
    ]);
}
add_action("rest_api_init", "register_api_user_post");