<?php

function api_user_put($request){
    $user = wp_get_current_user();
    $user_id = $user->ID;

    if($user_id > 0){
        $name = sanitize_text_field($request['name']);
        $email = sanitize_email($request['email']);
        $password = $request['password'];
        $cep = sanitize_text_field($request['cep']);

        $email_exists = email_exists($email);

        if(!$email_exists || $email_exists === $user_id){
            $response = [
                "ID" => $user_id,
                "user_pass" => $password,
                "user_email" => $email,
                "display_name" => $name,
                "first_name" => $name
            ];

            wp_update_user($response);

            update_user_meta($user_id, "cep", $cep);
        } else{
            $response = new WP_Error("email", "Email já cadastrado", ["status" => 403]);
        }
    } else{
        $response = new WP_Error("permissao", "Usuário não possui permissão", ["status" => 401]);
    }

    return rest_ensure_response($response);
}

function register_api_user_put(){
    register_rest_route("api", "/usuario", [
        [
            "methods" => WP_REST_Server::EDITABLE,
            "callback" => "api_user_put"
        ]
    ]);
}
add_action("rest_api_init", "register_api_user_put");