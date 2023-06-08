<?php

function register_cpt_product(){
    register_post_type("product", [
        "label" => "Produtos",
        "description" => "Produtos",
        "public" => true,
        "show_ui" => true,
        "capability_type" => "post",
        "rewrite" => ['slug' => 'produto', 'with_front' => true],
        "query_var" => true,
        "supports" => ["custom-fields", "author", "title"],
        "publicly_queryable" => true
    ]);
}
add_action("init", "register_cpt_product");