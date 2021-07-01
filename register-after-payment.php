<?php
/*
 * Plugin Name: WooCommerce Auto Registration Guest Users
 * Description: Auto register not existing users after successfully payment on specific product or products
 * Version: 1.0
 * Author: dawidwojcik476@gmail.com
 * WC tested up to: 3.3.5
 * License: GPL3
 */

function wc_register_guests( $order_id ) {

    $order = new WC_Order($order_id);




    $order_email = $order->get_billing_email();
    $username = $order->get_billing_first_name().$order->get_billing_last_name();
    
    
 
    $email = email_exists( $order_email );

    foreach ( $order->get_items() as $item_id => $item ) {
        $product_id = $item->get_product_id();
    }


    if( $email == false && $product_id == 214){
    
    // random password with 12 chars
    $random_password = wp_generate_password();
   

    
    
    $userdata = array(
    'user_pass' => $random_password, 
    'user_login' => $username, 
    'user_nicename' => $order->get_formatted_billing_full_name(), 
    'user_email' => $order_email, 
    'display_name' => $order->get_formatted_billing_full_name(),
    'nickname' => $username, 
    'first_name' => $order->get_billing_first_name(), 
    'last_name' => $order->get_billing_last_name(), 
    'description' => 'Generado automaticamente desde el formulario de reservas',
    );
    
    $user_id = wp_insert_user( $userdata ) ;
    
    if ( ! is_wp_error( $user_id ) ) {
    echo "User created : ". $user_id;
    }
    

    update_user_meta( $user_id, 'guest', 'yes' );
    
    
    
    //user's billing data
    update_user_meta( $user_id, 'billing_address_1', $order->get_billing_address_1() );
    update_user_meta( $user_id, 'billing_address_2', $order->get_billing_address_2() );
    update_user_meta( $user_id, 'billing_city', $order->get_billing_city() );
    update_user_meta( $user_id, 'billing_company', $order->get_billing_company() );
    update_user_meta( $user_id, 'billing_country', $order->get_billing_country() );
    update_user_meta( $user_id, 'billing_email', $order->get_billing_email() );
    update_user_meta( $user_id, 'billing_first_name', $order->get_billing_first_name() );
    update_user_meta( $user_id, 'billing_last_name', $order->get_billing_last_name() );
    update_user_meta( $user_id, 'billing_phone', $order->get_billing_phone() );
    update_user_meta( $user_id, 'billing_postcode', $order->get_billing_postcode() );
    update_user_meta( $user_id, 'billing_state', $order->get_billing_state() );
      wc_update_new_customer_past_orders( $user_id );

      wp_mail($order_email, 'Register new account', 'Account has been successful registered. Please find credentials below:' . "<br>" . 'Login: ' . $order_email . "<br>" . 'Password: ' . $userdata['user_pass']);
    }

}

add_action( 'woocommerce_order_status_completed', 'wc_register_guests', 10, 1 );





function my_lost_password_page( $lostpassword_url, $redirect ) {
    return home_url( '/reset-password' . $redirect );
}

add_filter( 'lostpassword_url', 'my_lost_password_page', 10, 2 );