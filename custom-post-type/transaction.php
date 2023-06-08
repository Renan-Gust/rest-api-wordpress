<?php

function register_cpt_transaction(){
    register_post_type("transaction", [
        "label" => "Transação",
        "description" => "Transação",
        "public" => true,
        "show_ui" => true,
        "capability_type" => "post",
        "rewrite" => ['slug' => 'transacao', 'with_front' => true],
        "query_var" => true,
        "supports" => ["custom-fields", "author", "title"],
        "publicly_queryable" => true
    ]);
}
add_action("init", "register_cpt_transaction");