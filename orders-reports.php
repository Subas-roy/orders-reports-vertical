<?php
/*
Plugin Name: Orders Reports
Plugin URI: https://github.com/subas-roy/orders-reports
Description: Custom Plugin to display weekly, monthly and yearly orders reports.
Version: 1.0.0
Author: Subas Roy
Author URI: https://github.com/subas-roy
License: GPLv2 or later
Text Domain: ordersreports
*/

defined( 'ABSPATH' ) or die( 'Hey, what are you doing? You silly human!.' );

// admin menu
function reports_admin_menu() {
  add_menu_page( 'Orders Reports', 'Orders Reports', 'manage_options', 'orders_reports_admin', 'orders_reports_admin_page', 'dashicons-editor-ol' );
  add_submenu_page('orders_reports_admin', 'Daily', 'Daily', 'manage_options', 'daily_reports', 'daily_reports_page' );
  add_submenu_page('orders_reports_admin', 'Weekly', 'Weekly', 'manage_options', 'weekly_reports', 'weekly_reports_page' );
  add_submenu_page('orders_reports_admin', 'Monthly', 'Monthly', 'manage_options', 'monthly_reports', 'monthly_reports_page' );
}
add_action('admin_menu', 'reports_admin_menu');

function orders_reports_admin_page() { ?>
  <div class="container">
    <div class="row gx-3">
      <div class="col">
        <div class="border my-5 p-5" style="background-color:#E7EBFD;">
          <h1 class="text" style="font-size:48px; font-weight:600;">Orders Reports</h1>
        </div>
      </div>
    </div>
    <div class="row gx-3">
      <div class="col">
        <div class="card text-center">
          <div class="card-header">
            Report
          </div>
          <div class="card-body">
            <h5 class="card-title">Daily Total</h5>
            <?php
              global $wpdb;
              // get table prefix
              $table_prefix = $wpdb->prefix;
              $table_name = 'wc_order_product_lookup';
              // echo $table_prefix;
              $table = $table_prefix.$table_name;

              $rows = $wpdb->get_results("SELECT * 
                FROM $table ARRAY_A
                WHERE DATE(date_created) = CURDATE()
              ");
              
              $all_orders = array();

              foreach($rows as $row){
                $all_orders[] = $row->order_id;
              }
              $unique_orders = array_unique( $all_orders );
              $total = count($unique_orders);
            ?>
            <p class="card-text"><?php echo $total ?> Order(s)</p>
            <a href="<?php echo site_url();?>/wp-admin/admin.php?page=daily_reports" class="btn btn-primary">Details</a>
          </div>
          <div class="card-footer text-muted">
            Today
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card text-center">
          <div class="card-header">
            Report
          </div>
          <div class="card-body">
            <h5 class="card-title">Weekly Total</h5>
            <?php
              global $wpdb;
              // get table prefix
              $table_prefix = $wpdb->prefix;
              $table_name = 'wc_order_product_lookup';
              // echo $table_prefix;
              $table = $table_prefix.$table_name;

              $rows = $wpdb->get_results("SELECT * 
                FROM $table ARRAY_A
                WHERE date_created >= (NOW() - INTERVAL 7 DAY)
              ");
              
              $all_orders = array();

              foreach($rows as $row){
                $all_orders[] = $row->order_id;
              }
              $unique_orders = array_unique( $all_orders );
              $total = count($unique_orders);
            ?>
            <p class="card-text"><?php echo $total ?> Order(s)</p>
            <a href="<?php echo site_url();?>/wp-admin/admin.php?page=weekly_reports" class="btn btn-primary">Details</a>
          </div>
          <div class="card-footer text-muted">
            Past week
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card text-center">
          <div class="card-header">
            Report
          </div>
          <div class="card-body">
            <h5 class="card-title">Monthly Total</h5>
            <?php
              global $wpdb;
              // get table prefix
              $table_prefix = $wpdb->prefix;
              $table_name = 'wc_order_product_lookup';
              // echo $table_prefix;
              $table = $table_prefix.$table_name;

              $rows = $wpdb->get_results("SELECT * 
                FROM $table ARRAY_A
                WHERE date_created >= (NOW() - INTERVAL 1 MONTH)
              ");
              
              $all_orders = array();

              foreach($rows as $row){
                $all_orders[] = $row->order_id;
              }
              $unique_orders = array_unique( $all_orders );
              $total = count($unique_orders);
            ?>
            <p class="card-text"><?php echo $total ?> Order(s)</p>
            <a href="<?php echo site_url();?>/wp-admin/admin.php?page=monthly_reports" class="btn btn-primary">Details</a>
          </div>
          <div class="card-footer text-muted">
            Past month
          </div>
        </div>
      </div>
    </div>
  </div> 
<?php }

function daily_reports_page() { ?>
  <div class="container">
    <div class="row gx-3">
      <div class="col">
        <div class="p-2 border my-5">
          <div class="mb-3" id="orTable">
            <div id="quick_links" class="">
              <ul class="nav">
                <li class="nav-item"><a class="nav-link active_link" href="<?php echo site_url();?>/wp-admin/admin.php?page=daily_reports">Daily</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo site_url();?>/wp-admin/admin.php?page=weekly_reports">Weekly</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo site_url();?>/wp-admin/admin.php?page=monthly_reports">Monthly</a></li>
              </ul>
            </div>
            <h3 class="text">Daily Orders Report<span id="weekly_orders"></span></h3>
            <table class="table table-responsive">
              <thead>
                <tr>
                  <?php 
                  global $wpdb;
                  // get table prefix
                  $table_prefix = $wpdb->prefix;
                  $table_name = 'wc_order_product_lookup';
                  // echo $table_prefix;
                  $table = $table_prefix.$table_name;

                  $rows = $wpdb->get_results("SELECT * 
                    FROM $table ARRAY_A
                    WHERE DATE(date_created) = CURDATE()
                  ");

                  $all_orders = array();
                  
                  foreach($rows as $row){
                    $all_orders[] = $row->order_id;
                  }
                  $unique_orders = array_unique( $all_orders );
                  $total = count($unique_orders);

                  foreach( $unique_orders as $order_id ){
                    $order = wc_get_order( $order_id );

                    $customer_first_name = $order->get_billing_first_name();
                    $customer_last_name  = $order->get_billing_last_name();
                    ?>
                      <td><?php echo $customer_first_name . ' ' . $customer_last_name; ?></td>  
                    <?php 
                  }
                  ?>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <?php
                    foreach( $unique_orders as $order_id ) {
                      $order = wc_get_order( $order_id );
                      ?>
                      <td>
                        <?php 
                          $qty = 0;
                          foreach ( $order->get_items() as $count_id => $count ) {
                            $qty = $count->get_quantity(); 
                          }
                          foreach ( $order->get_items() as $item_id => $item ) {
                            $product_name = $item->get_name();
                              echo $product_name.'('.$qty.')'.'<br>'; 
                          }
                        ?>
                      </td>
                        <?php 
                    }
                      ?>                  
                </tr>
                <caption id="total">Total <?php echo $total;?> Order(s)</caption>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>   
<?php }

function weekly_reports_page() { ?>
  <div class="container">
    <div class="row gx-3">
      <div class="col">
        <div class="p-2 border my-5">
          <div class="mb-3" id="orTable">
            <div id="quick_links" class="">
              <ul class="nav">
                <li class="nav-item"><a class="nav-link" href="<?php echo site_url();?>/wp-admin/admin.php?page=daily_reports">Daily</a></li>
                <li class="nav-item"><a class="nav-link active_link" href="<?php echo site_url();?>/wp-admin/admin.php?page=weekly_reports">Weekly</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo site_url();?>/wp-admin/admin.php?page=monthly_reports">Monthly</a></li>
              </ul>
            </div>
            <h3 class="text">Weekly Orders Report<span id="weekly_orders"></span></h3>
            <table class="table table-responsive">
              <thead>
                <tr>
                  <?php 
                  global $wpdb;
                  // get table prefix
                  $table_prefix = $wpdb->prefix;
                  $table_name = 'wc_order_product_lookup';
                  // echo $table_prefix;
                  $table = $table_prefix.$table_name;

                  $rows = $wpdb->get_results("SELECT * 
                    FROM $table ARRAY_A
                    WHERE date_created >= (NOW() - INTERVAL 7 DAY)
                  ");

                  $all_orders = array();

                  foreach($rows as $row){
                    $all_orders[] = $row->order_id;
                  }
                  $unique_orders = array_unique( $all_orders );
                  $total = count($unique_orders);

                  foreach( $unique_orders as $order_id ){
                    $order = wc_get_order( $order_id );

                    $customer_first_name = $order->get_billing_first_name();
                    $customer_last_name  = $order->get_billing_last_name();
                    ?>
                      <td><?php echo $customer_first_name . ' ' . $customer_last_name; ?></td>  
                    <?php 
                  }
                  ?>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <?php
                    foreach( $unique_orders as $order_id ) {
                      $order = wc_get_order( $order_id );
                      ?>
                      <td>
                        <?php 
                          $qty = 0;
                          foreach ( $order->get_items() as $count_id => $count ) {
                            $qty = $count->get_quantity(); 
                          }
                          foreach ( $order->get_items() as $item_id => $item ) {
                            $product_name = $item->get_name();
                              echo $product_name.'('.$qty.')'.'<br>'; 
                          }
                        ?>
                      </td>
                        <?php 
                    }
                      ?>                  
                </tr>
                <caption id="total">Total <?php echo $total;?> Order(s)</caption>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>   
<?php }

function monthly_reports_page() { ?>
  <div class="container">
    <div class="row gx-3">
      <div class="col">
        <div class="p-2 border my-5">
          <div class="mb-3" id="orTable">
            <div id="quick_links" class="">
              <ul class="nav">
                <li class="nav-item"><a class="nav-link" href="<?php echo site_url();?>/wp-admin/admin.php?page=daily_reports">Daily</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo site_url();?>/wp-admin/admin.php?page=weekly_reports">Weekly</a></li>
                <li class="nav-item"><a class="nav-link active_link" href="<?php echo site_url();?>/wp-admin/admin.php?page=monthly_reports">Monthly</a></li>
              </ul>
            </div>
            <h3 class="text">Monthly Orders Report<span id="daily_orders"></span></h3>
            <table class="table table-responsive">
              <thead>
                <tr>
                  <?php 
                  global $wpdb;
                  // get table prefix
                  $table_prefix = $wpdb->prefix;
                  $table_name = 'wc_order_product_lookup';
                  // echo $table_prefix;
                  $table = $table_prefix.$table_name;

                  $rows = $wpdb->get_results("SELECT * 
                    FROM $table ARRAY_A
                    WHERE date_created >= (NOW() - INTERVAL 1 MONTH)
                  ");

                  $all_orders = array();

                  foreach($rows as $row){
                    $all_orders[] = $row->order_id;
                  }
                  $unique_orders = array_unique( $all_orders );
                  $total = count($unique_orders);

                  foreach( $unique_orders as $order_id ){
                    $order = wc_get_order( $order_id );

                    $customer_first_name = $order->get_billing_first_name();
                    $customer_last_name  = $order->get_billing_last_name();
                    ?>
                      <td><?php echo $customer_first_name . ' ' . $customer_last_name; ?></td>  
                    <?php 
                  }
                  ?>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <?php
                    foreach( $unique_orders as $order_id ) {
                      $order = wc_get_order( $order_id );
                      ?>
                      <td>
                        <?php 
                          $qty = 0;
                          foreach ( $order->get_items() as $count_id => $count ) {
                            $qty = $count->get_quantity(); 
                          }
                          foreach ( $order->get_items() as $item_id => $item ) {
                            $product_name = $item->get_name();
                              echo $product_name.'('.$qty.')'.'<br>'; 
                          }
                        ?>
                      </td>
                        <?php 
                    }
                      ?>                  
                </tr>
                <caption id="total">Total <?php echo $total;?> Order(s)</caption>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div> 
<?php }

// orders reports scripts
function orders_reports_scripts() {
  wp_enqueue_style( 'reports-bootstrap-min', plugins_url('/assets/css/bootstrap.min.css', __FILE__ ));
  wp_enqueue_style( 'reports-style', plugins_url('/assets/css/style.css', __FILE__ ));
  wp_enqueue_script( 'reports-bundle-min-js', plugins_url('/assets/js/bootstrap.bundle.min.js', __FILE__ ), array('jquery'), '', true );
  wp_enqueue_script( 'reports-main-js', plugins_url('/assets/js/script.js', __FILE__ ), array('reports-bundle-min-js'), '', true );
}
add_action( 'admin_enqueue_scripts', 'orders_reports_scripts' );

?>
