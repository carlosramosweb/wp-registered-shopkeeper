<?php
/*
 * Class WP_Registered_Shopkeeper
*/

if (!defined('ABSPATH')) {
    exit();
}

$esc_settings = [];
$esc_settings = get_option( 'wp_registered_shopkeeper_settings' );

$telephone   = '5561900000000';
if (isset($esc_settings['telephone']) && ! empty($esc_settings['telephone'])) {
    $telephone   = $esc_settings['telephone'];
}
$text_first  = 'Escolha sua cidade e fale no WhatsApp com a lojista mais próximo de você.';
if (isset($esc_settings['text_first']) && ! empty($esc_settings['text_first'])) {
    $text_first  = $esc_settings['text_first'];
}
$text_second = 'Selecione o Estado e Cidade para falar no WhatsApp';
if (isset($esc_settings['text_second']) && ! empty($esc_settings['text_second'])) {
    $text_second = $esc_settings['text_second'];
}
$text_third  = 'Fale com um de nossos atendentes.';
if (isset($esc_settings['text_third']) && ! empty($esc_settings['text_third'])) {
    $text_third  = $esc_settings['text_third'];
}

$wr_shopkeeper = new WP_Registered_Shopkeeper();
$telephone = $wr_shopkeeper->format_phone_number($telephone);
?>

<div class="container">
   <div class="card-deck mx-auto" style="margin:50px;width: 500px;">
      <div class="card">
         <div class="c-wrapper">
            <div class="card-img-overlay">
               <p class="card-text text-white text-center">                  
                  <?php echo $text_first; ?>
               </p>
               <form method="post" target="_self">
                    <input type="hidden" name="_action" value="search-shopkeeper">
                    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('search-shopkeeper'); ?>">
                   <div style="margin:20px;" class="form-group">
                      <div style="padding-bottom:20px">
                         <select class="form-control" name="_shopkeeper_state" id="shopkeeper_state">
                            <option value="">Selecione o Estado</option>
                            <option value="Acre">Acre</option>
                            <option value="Alagoas">Alagoas</option>
                            <option value="Amapá">Amapá</option>
                            <option value="Amazonas">Amazonas</option>
                            <option value="Bahia">Bahia</option>
                            <option value="Ceará">Ceará</option>
                            <option value="Distrito Federal">Distrito Federal</option>
                            <option value="Espírito Santo">Espírito Santo</option>
                            <option value="Goiás">Goiás</option>
                            <option value="Maranhão">Maranhão</option>
                            <option value="Mato Grosso">Mato Grosso</option>
                            <option value="Mato Grosso do Sul">Mato Grosso do Sul</option>
                            <option value="Minas Gerais">Minas Gerais</option>
                            <option value="Pará">Pará</option>
                            <option value="Paraíba">Paraíba</option>
                            <option value="Paraná">Paraná</option>
                            <option value="Pernambuco">Pernambuco</option>
                            <option value="Piauí">Piauí</option>
                            <option value="Rio de Janeiro">Rio de Janeiro</option>
                            <option value="Rio Grande do Norte">Rio Grande do Norte</option>
                            <option value="Rio Grande do Sul">Rio Grande do Sul</option>
                            <option value="Rondônia">Rondônia</option>
                            <option value="Roraima">Roraima</option>
                            <option value="Santa Catarina">Santa Catarina</option>
                            <option value="São Paulo">São Paulo</option>
                            <option value="Sergipe">Sergipe</option>
                            <option value="Tocantins">Tocantins</option>
                         </select>
                      </div>
                      <select style="padding-bottom:10px" class="form-control" name="_shopkeeper_city" id="shopkeeper_city">
                         <option value="">Selecione a Cidade</option>
                      </select>
                   </div>
                </div>
            </form>
         </div>
         <div class="card-body">
            <div class="text-center ptb25" id="list_shopkeeper" style="text-align: center;display: inline-block;width: 100%;">

               <img src="<?php echo plugins_url(). '/wp-registered-shopkeeper/assets/images/image-up.png'; ?>" style="margin: 0 auto;">
               <p><?php echo $text_second; ?></p>

            </div>
         </div>
         <div class="card-footer bg-dark text-white text-center">
            <h6 style="color: aliceblue;"><?php echo __( 'Dúvidas?', 'wp-registered-shopkeeper' ) ; ?></h6>
            <p class="card-text text-white">
                <a class="text-white" href="https://web.whatsapp.com/send?phone=<?php echo $telephone; ?>" target="_blank"><?php echo $text_third; ?></a>
            </p>
         </div>
      </div>
   </div>
</div>

<script src="<?php echo plugins_url(). '/wp-registered-shopkeeper/assets/jquery/jquery-3.6.0.js'; ?>"></script>
<link rel="stylesheet" href="<?php echo plugins_url(). '/wp-registered-shopkeeper/assets/css/bootstrap.css'; ?>">
<link rel="stylesheet" href="<?php echo plugins_url(). '/wp-registered-shopkeeper/assets/css/style.css'; ?>">
<script type="text/javascript">
    jQuery('body').on("change", "#shopkeeper_state", function(e){
        var state = jQuery('#shopkeeper_state').val();
        jQuery.ajax({
            type : "POST",
            url : "<?php echo admin_url('admin-ajax.php'); ?>",
            data : {action: "search_shopkeeper_state", shopkeeper_state: state},
            success: function(response) {
                console.log(response);
                jQuery( "select#shopkeeper_city" ).html( response );
            }
        });
    });

    jQuery('body').on("change", "#shopkeeper_city", function(e){
        var state = jQuery('#shopkeeper_state').val();
        var city = jQuery('#shopkeeper_city').val();
        jQuery.ajax({
            type : "POST",
            url : "<?php echo admin_url('admin-ajax.php'); ?>",
            data : {
                action: "search_all_shopkeeper", 
                shopkeeper_state: state,
                shopkeeper_city: city,
            },
            success: function(response) {
                console.log(response);
                jQuery( "#list_shopkeeper" ).html( response );
            }
        });
    });
</script>

