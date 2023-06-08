<?php

$template_directory = get_template_directory();

require_once($template_directory . "/custom-post-type/product.php");
require_once($template_directory . "/custom-post-type/transaction.php");

require_once($template_directory . "/endpoints/user_post.php");
require_once($template_directory . "/endpoints/user_get.php");
require_once($template_directory . "/endpoints/user_put.php");
require_once($template_directory . "/endpoints/product_post.php");
require_once($template_directory . "/endpoints/product_get.php");
require_once($template_directory . "/endpoints/product_delete.php");
require_once($template_directory . "/endpoints/transaction_post.php");
require_once($template_directory . "/endpoints/transaction_get.php");

function get_product_id_by_slug($slug){
    $query = new WP_Query([
        "name" => $slug,
        "post_type" => "product",
        "numberposts" => 1,
        "fields" => "ids"
    ]);

    $posts = $query->posts;

    return array_shift($posts);
}

function expire_token() {
    return time() + (60 * 60 * 24);
}
add_action("jwt_auth_expire", "expire_token");

add_action("rest_pre-serve_reques", function() {
    header("Access-Control-Expose-Headers: X-Total-Count");
});

function my_login_screen() { ?>
    <style>
        #login h1 a{
            display: none;
        }

        #backtoblog {
            display: none;
        }
    </style>
<?php }
    add_action("login_enqueue_scripts", "my_login_screen");
?>