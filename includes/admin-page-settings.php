<?php
if (!defined('ABSPATH'))
{
    exit();
}


$message = "";
if( isset( $_POST['_update'] ) && isset( $_POST['_wpnonce'] ) ) {
  $_update    = sanitize_text_field( $_POST['_update'] );
  $_wpnonce   = sanitize_text_field( $_POST['_wpnonce'] );
}

if( isset( $_wpnonce ) && isset( $_update ) ) {
  if ( ! wp_verify_nonce( $_wpnonce, "wp-registered-shopkeeper-settings" ) ) {
      $message = 'error';
      
  } else if ( empty( $_update ) ) {
      $message = 'error';         
  }
  
  if( isset( $_POST['settings'] ) ) {

      $esc_settings = [];
      $esc_settings = get_option( 'wp_registered_shopkeeper_settings' );

      $post_settings = [];
      $post_settings = (array)$_POST['settings'];

      $new_settings['telephone']    = sanitize_text_field( $post_settings['telephone'] );
      $new_settings['text_first']   = sanitize_text_field( $post_settings['text_first'] );
      $new_settings['text_second']  = sanitize_text_field( $post_settings['text_second'] );
      $new_settings['text_third']  = sanitize_text_field( $post_settings['text_third'] );

      update_option( "wp_registered_shopkeeper_settings", $new_settings );

      $message = "updated";
  }
}

$esc_settings = [];
$esc_settings = get_option( 'wp_registered_shopkeeper_settings' );

$telephone   = $esc_settings['telephone'];
$text_first  = $esc_settings['text_first'];
$text_second = $esc_settings['text_second'];
$text_third  = $esc_settings['text_third'];
?>
<div id="wpwrap">
   <h1><?php echo __( 'Configuração', 'wp-registered-shopkeeper' ); ?></h1>
   <?php if( isset( $message ) ) { ?>
   <div class="wrap">
      <?php if( $message == "updated" ) { ?>
      <div id="message" class="updated notice is-dismissible" style="margin-left: 0px;">
         <p><?php echo __( 'Atualizações feitas com sucesso!', 'wp-registered-shopkeeper' ) ; ?></p>
         <button type="button" class="notice-dismiss">
         <span class="screen-reader-text">
         <?php echo __( 'Fechar', 'wp-registered-shopkeeper' ) ; ?>
         </span>
         </button>
      </div>
      <?php } ?>
      <?php if( $message == "error" ) { ?>
      <div id="message" class="updated error is-dismissible" style="margin-left: 0px;">
         <p><?php echo __( 'Ops! Não foi possível atualizar!', 'wp-registered-shopkeeper' ) ; ?></p>
         <button type="button" class="notice-dismiss">
         <span class="screen-reader-text">
         <?php echo __( 'Fechar', 'wp-registered-shopkeeper' ) ; ?>
         </span>
         </button>
      </div>
      <?php } ?>
   </div>
   <?php } ?>
   <div class="wrap woocommerce">
      <nav class="nav-tab-wrapper wc-nav-tab-wrapper">
         <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=shopkeeper&page=shopkeeper-settings' ) ); ?>" class="nav-tab <?php if( $tab == "settings") { echo "nav-tab-active"; }; ?>"><?php echo __( 'Configuração', 'wp-registered-shopkeeper' ); ?></a>
      </nav>
      <?php if( ! isset( $tab ) ) { ?>
      <!---->
      <div class="wrap">
         <h2><?php echo __( 'Configuração', 'wp-registered-shopkeeper' ); ?></h2>
         <!---->
         <form action="<?php echo esc_url( admin_url( 'edit.php?post_type=shopkeeper&page=shopkeeper-settings' ) ); ?>" method="post" enctype="application/x-www-form-urlencoded">
            <!---->
            <table class="form-table">
               <tbody>
                  <!---->
                  <tr valign="top">
                     <th scope="row">
                        <label>
                        <?php echo __( 'Telefone:', 'wp-registered-shopkeeper' ); ?>
                        </label>
                     </th>
                     <td>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" name="settings[telephone]" placeholder="<?php echo __( '(00) 00000-0000', 'wp-registered-shopkeeper' ); ?>" style="min-width:300px" class="form-control input-text" value="<?php echo $telephone; ?>">
                        <span>               
                     </td>
                  </tr>
                  <!---->
                  <tr valign="top">
                     <th scope="row">
                        <label>
                        <?php echo __( 'Primeiro Texto:', 'wp-registered-shopkeeper' ); ?>
                        </label>
                     </th>
                     <td>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" name="settings[text_first]" placeholder="<?php echo __( 'Digite o primeiro texto.', 'wp-registered-shopkeeper' ); ?>" style="min-width:300px" class="form-control input-text" value="<?php echo $text_first; ?>">
                        <span>               
                     </td>
                  </tr>
                  <!---->
                  <tr valign="top">
                     <th scope="row">
                        <label>
                        <?php echo __( 'Texto para selecionar Estado e Cidade:', 'wp-registered-shopkeeper' ); ?>
                        </label>
                     </th>
                     <td>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" name="settings[text_second]" placeholder="<?php echo __( 'Digite o segundo texto.', 'wp-registered-shopkeeper' ); ?>" style="min-width:300px" class="form-control input-text" value="<?php echo $text_second; ?>">
                        <span>               
                     </td>
                  </tr>
                  <!---->
                  <tr valign="top">
                     <th scope="row">
                        <label>
                        <?php echo __( 'Texto de Dúvidas:', 'wp-registered-shopkeeper' ); ?>
                        </label>
                     </th>
                     <td>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" name="settings[text_third]" placeholder="<?php echo __( 'Digite o terceiro texto.', 'wp-registered-shopkeeper' ); ?>" style="min-width:300px" class="form-control input-text" value="<?php echo $text_third; ?>">
                        <span>               
                     </td>
                  </tr>
                  <!---->
                  <tr valign="top">
                     <td>
                        <hr/>
                     </td>
                  </tr>
                  <!---->
               </tbody>
            </table>
            <!---->
            <hr/>
            <div class="submit">
               <button class="button button-primary" type="submit"><?php echo __( 'Salvar Alterações', 'wp-registered-shopkeeper' ) ; ?></button>
               <input type="hidden" name="_update" value="1">
               <input type="hidden" name="_wpnonce" value="<?php echo sanitize_text_field( wp_create_nonce( 'wp-registered-shopkeeper-settings' ) ); ?>">
               <!---->
               <span>
               <span aria-hidden="true" class="dashicons dashicons-warning" style="vertical-align: sub;"></span>
               <?php echo __( 'Não esqueça de <strong>salvar suas alterações</strong>.', 'wp-registered-shopkeeper' ) ; ?>
               </span>
            </div>
            <!---->
         </form>
         <!---->
      </div>
      <!---->
      <?php } ?>
   </div>
</div>