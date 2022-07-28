<?php
// Add custom Theme Functions here
// 
add_filter( 'add_to_cart_text', 'woo_custom_cart_button_text' );
// Thay chữ trong trang danh mục sản phẩm
add_filter( 'woocommerce_product_add_to_cart_text', 'woo_custom_cart_button_text' );
//Thay chữ trong trang chi tiết của bài viết
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );
function woo_custom_cart_button_text() {
return __( 'Xem chi tiết', 'woocommerce' );
}

function insert_query_vars($vars) {
 array_push($vars, 'pro_id'); //lưu id sản phẩm
 array_push($vars, 'action'); //lưu thao tác (thêm, xóa)
 return $vars;
}
add_filter('query_vars', 'insert_query_vars');
 
function rewrite_rules($rules) {
 $new_rules = array();
 $new_rules['(gio-hang)/(them|xoa)/([0-9]+)/?'] = 'index.php?pagename=$matches[1]&action=$matches[2]&pro_id=$matches[3]';
 return $new_rules + $rules;
 echo $matches[2];
 
}
add_action('rewrite_rules_array', 'rewrite_rules'); 
add_action('init', 'mySessionStart', 1);
add_action('wp_logout', 'mySessionEnd');
add_action('wp_login', 'mySessionEnd');
 
function mySessionStart() {
ob_start();
if(!session_id()) {
session_start();
}
}
function mySessionEnd() {
session_destroy ();
}