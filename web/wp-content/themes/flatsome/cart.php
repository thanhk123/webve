<?php
   /*
   Template Name: Trang giỏ hàng
   */
   ?>
<?php get_header();?>
<div class="wx-content">
   <div class="container">
      <div class="content_cat giohang">
         <div class="breadcrumbs">
            <div class="inner">
               <ul>
                  <?php if ( function_exists( 'yoast_breadcrumb' ) ) {
                     yoast_breadcrumb();
                     } ?>
               </ul>
            </div>
         </div>
         <div class="main main-cart" style="margin-top: 20px;">
            <?php
               global $product_prefix;
               $action = get_query_var('action'); //thêm|xóa
               $pro_id = get_query_var('pro_id'); //id sản phẩm
               if($action){ //nếu có thao tác (thêm hoặc xóa)
                switch($action){
                 case 'them':
                  if (isset( $_SESSION['cart'][$pro_id])) //nếu đã có thì cập nhật số lượng thêm 1
                   $quantity = $_SESSION['cart'][$pro_id] + 1;
                  else
                   $quantity = 1; //ngược lại tạo 1 item mới với số lượng là 1
                  $_SESSION['cart'][$pro_id] = $quantity; //cập nhật lại
                  wp_redirect(get_bloginfo('url').'/gio-hang'); exit(); //trả về trang hiển thị giỏ hàng
                  break;
                 case 'xoa':
                  if(isset( $_SESSION['cart'] ) && count( $_SESSION['cart'] ) > 0 ){ //kiểm tra và xóa sản phẩm dựa vào id
                   unset($_SESSION['cart'][$pro_id]);
                   wp_redirect(get_bloginfo('url').'/gio-hang'); exit();
                  }
                  else{
                   unset($_SESSION['cart']);
                   echo "<h3>Hiện chưa có sản phẩm nào trong giỏ hàng! <a href='".get_bloginfo('url')."'>Bấm vào đây</a> để xem và mua hàng.</h3>";
                  }
                 break;
               }
               }else{ //không có thao tác thêm hoặc xóa thì sẽ hiển thị sản phẩm trong giỏ hàng
               ?>
            <?php
               if ( isset( $_SESSION['cart'] ) && count( $_SESSION['cart'] ) > 0 ) { //kiểm tra số lượng sản phẩm trước khi hiển thị
                
               ?>
            <h3 class="title-don">Thông tin đơn hàng</h3>
            <form action="" method="post" class="thongtin_sp">
               <table id="cart" class="form_cart">
                  <tr class="title-cart">
                     <td style="width:50px;"></td>
                     <td>Tên sản phẩm</td>
                     <td>Giá</td>
                     <td>Số lượng</td>
                     <td>Thành tiền</td>
                     <td><input type="submit" name="cart_update" value="Cập nhật" style="border: 1px solid olive; cursor: pointer;"
                        title="Cập nhật giỏ hàng"/></td>
                  </tr>
                  <?php
                     $total = 0;
                     foreach ( $_SESSION['cart'] as $pro_id => $quantity ){ //lặp qua mảng cart session lấy ra id và show thông tin sản phẩm theo id đó
                      $product = get_post((int)$pro_id );
                      if(get_field('price', $pro_id)!=0){
                      $price = get_field('price', $pro_id);}else{
                      $price = get_field('price_old', $pro_id);
                      }
                     ?>
                  <tr class="content-cart">
                     <td style="width:50px;"><?php if ( has_post_thumbnail( $pro_id ) ) echo get_the_post_thumbnail( $pro_id, array( 50, 50 ) ); else echo "<img src='".get_bloginfo('template_url')."/images/no_img.png' style='width:50px;height:50px;' />"; ?></td>
                     <td><a href="/<?php echo $product->post_name;?>"><?php echo $product->post_title; ?></a></td>
                     <td><?php echo number_format($price). ' VNĐ'; ?></td>
                     <td><input autocomplete="off" type="text" value="<?php echo $quantity; ?> " name="quantity[<?php echo $pro_id; ?>]" style="width:50px;" /></td>
                     <td><?php echo number_format( $price * $quantity) . ' VNĐ'; ?></td>
                     <?php  $total += $price * $quantity;   $_POST['tien'] = number_format($total).' đ'; ?>
                     <td><a href="<?php echo get_bloginfo( 'url' ) . '/gio-hang/xoa/' . $pro_id; //link xóa sản phẩm trong giỏ ?>" class="remove-product">Xóa</a>
                     </td>
                  </tr>
                  <?php
                     $check .= $product->post_title. " " ."(".$quantity.")". " ,"; 
                     $_POST['name'] = $check;
                     }
                     ?>
                  <tr>
                     <td>
                        <a title="Mua tiếp" class="btn muatiep" onclick="javascript: history.go(-1)">Mua tiếp</a>
                     </td>
                     <td> <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                        Thanh toán
                        </button>
                     </td>
                     <td colspan="2"></td>
                     <td class="sum-money">Tổng tiền:</td>
                     <td colspan="3"><span class="money"><?php echo number_format($total); ?></span> VNĐ</td>
                  </tr>
               </table>
            </form>
            <!-- Modal -->
            <div class="nhap_thong_tin">
               <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                     <div class="modal-content">
                        <div class="modal-header">
                           <h4 class="modal-title text-center" id="myModalLabel">Thông tin đặt hàng</h4>
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                           </button>
                        </div>
                        <div class="modal-body">
                           <?php echo do_shortcode('[contact-form-7 id="302" title="Contact form 1"]') ?> 
                           <script>
                              jQuery(function($) {
                                $('.disabled').attr('disabled', 'disabled');
                              });
                           </script>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <?php
               } else {
               echo "<h2>Hiện chưa có sản phẩm nào trong giỏ hàng! <a href='" . get_bloginfo( 'url' ) . "'>Bấm vào đây</a> để xem và mua hàng.</h2>";
               }
               ?>
            <?php
               if(isset($_POST['cart_update'])){ //xử lý cập nhật giỏ hàng
                if(isset($_POST['quantity'])){
                 if(count($_SESSION['cart']) == 0) unset($_SESSION['cart']); //nếu không còn sản phẩm trong giỏ thì xóa giỏ hàng
                 foreach($_POST['quantity'] as $pro_id => $quantity){ //lặp mảng số lượng mới và cập nhật mới cho giỏ hàng
                   if($quantity == 0) unset($_SESSION['cart'][$pro_id]);
                   else $_SESSION['cart'][$pro_id] = $quantity;
                 }
                 wp_redirect(get_bloginfo( 'url' ).'/gio-hang'); //cập nhật xong trả về trang hiển thị sản phẩm trong giỏ
                }
               }
               }
               ?>
         </div>
      </div>
   </div>
</div>
<?php get_footer()?>