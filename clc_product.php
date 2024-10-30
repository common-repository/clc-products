<?php 
/*
Plugin Name: CLC Products
Plugin URI: https://clcnederland.com
Description: A plugin to show products from clcnederland.com with the affiliate tag
Version: 1.2.4
Author: CLC Nederland
Author URI: https://clcnederland.com
License: GPL2
*/

//function that adds styling to head
function clc_custom_styling(){
	wp_enqueue_style('clc_product_css', plugin_dir_url(__FILE__) . 'clc_product.css');
	
	$options = get_option( 'CLC_Products_options' );
	if(!empty($options['custom_styling'])){
		wp_add_inline_style( 'clc_product_css', $options['custom_styling'] );
	}
}

//function that adds glider on page
function add_glider_setup(){
	wp_enqueue_script('glider_js', plugin_dir_url(__FILE__) . 'glider/glider.js');
	wp_enqueue_style('glider_css', plugin_dir_url(__FILE__) . 'glider/glider.css');
	wp_add_inline_style( 'glider_css', '.glider{overflow-x:hidden;}' );
}


// function that runs when shortcode is called
function clc_showProduct($atts) {
	$options = get_option( 'CLC_Products_options' );
	
	if(empty(esc_attr( $options['affiliate_tag'] ))){
		$tag = 'clc316';
	} else {
		$tag = esc_attr( $options['affiliate_tag'] );
	}
	
	$isbn = NULL;
	$title = NULL;
	$cat = NULL;
	$amount = NULL;
	$output = NULL;
	if(isset($atts['isbn'])){
		$isbn = $atts['isbn'];
	}
	if(isset($atts['title'])){
		$title = $atts['title'];
	}
	if(isset($atts['cat'])){
		$cat = $atts['cat'];
	}
	if(isset($atts['pubid'])){
		$pubid = $atts['pubid'];
	}
	if(isset($atts['amount'])){
		$amount = $atts['amount'];
	}
	
	$urlExt = array();
	if(!empty($isbn)){
		$urlExt[] = 'isbn='.$isbn;
	}
	if(!empty($title)){
		$urlExt[] = 'title='.urlencode($title);
	}
	if(!empty($cat)){
		$urlExt[] = 'cat='.urlencode($cat);
	}
	if(!empty($pubid)){
		$urlExt[] = 'pubid='.urlencode($pubid);
	}
	if(!empty($amount)){
		if(is_numeric($amount)){
			$urlExt[] = 'amount='.$amount;
		}
	}
	
	if(!empty($urlExt)){
		$url = 'https://api.boekenenmuziek.nl/productJson?'.implode('&', $urlExt).'&tag='.$tag;
		$i = 0;
		while(!isset($class->Products)){
			$response = wp_remote_get( $url );
			$result = wp_remote_retrieve_body( $response );
			$class = json_decode($result);
			
			if($i>3){
				$admin_email = get_option('admin_email');
				if(!empty($admin_email)){
					echo '<p>Er is een fout bij het ophalen van producten. Probeer het nogmaals. Blijft deze fout zich herhalen, stuur dan een mail naar: <a href="mailto:'.$admin_email.'">'.$admin_email.'</a></p>';
				} else {
					echo '<p>Er is een fout bij het ophalen van producten. Probeer het nogmaals. Blijft deze fout zich herhalen, neem dan contact op met ons.</p>';
				}
				if(WP_DEBUG){
					echo '<p style="font-size:0.5rem;">Error: '.$result.'<br/>'.$url.'</p>';
				}
				return;
			}
			$i++;
		}
		
		if(!isset($atts['type'])){
			$atts['type'] = NULL;
		}
		
		switch($atts['type']){
			case 'link':
				if(count($class->Products) > 1){
					$i = 1;
					$output .= 'Bestel ';
					foreach($class->Products as $product){
						if ($i == count($class->Products)) { 
							$output .= ' en "<a href="'.$product->Link.'"><em>'.$product->Title.'</em></a>"';
						} elseif($i == 1){
							$output .= '"<a href="'.$product->Link.'"><em>'.$product->Title.'</em></a>"';					
						} else {
							$output .= ', "<a href="'.$product->Link.'"><em>'.$product->Title.'</em></a>"';							
						}
						$i++;
					}
				} else {
					foreach($class->Products as $product){
						$output .= '<a href="'.$product->Link.'">Bestel <em>'.$product->Title.'</em></a>';
					}
				}
				break;

			case 'custom_layout_1':
				if(!empty($options['custom_layout_1'])){
					foreach($class->Products as $product){
						$layout = $options['custom_layout_1'];
						$layout = str_replace('{{link}}', $product->Link, $layout);
						$layout = str_replace('{{Link}}', $product->Link, $layout);
						$layout = str_replace('{{image}}', $product->Image, $layout);
						$layout = str_replace('{{Image}}', $product->Image, $layout);
						$layout = str_replace('{{title}}', $product->Title, $layout);
						$layout = str_replace('{{Title}}', $product->Title, $layout);
						$layout = str_replace('{{subtitle}}', $product->Subtitle, $layout);
						$layout = str_replace('{{Subtitle}}', $product->Subtitle, $layout);
						$layout = str_replace('{{description}}', $product->Description, $layout);
						$layout = str_replace('{{Description}}', $product->Description, $layout);
						$layout = str_replace('{{price}}', number_format($product->Price, 2, ',', ''), $layout);
						$layout = str_replace('{{Price}}', number_format($product->Price, 2, ',', ''), $layout);

						$output .= $layout;
					}
				} else {
					foreach($class->Products as $product){
						$output .= '<div class="product"><div style="display:flex;width:100%;margin-bottom:2em;"><div style="flex:45%;padding-right:5%;"> <a class="p_link" href="'.$product->Link.'"> <img class="p_image" src="'.$product->Image.'" width="100%" height="auto" loading="lazy"> </a></div><div style="flex:65%;"><h2 class="p_title">'.$product->Title.'</h2><h3 class="p_subtitle">'.$product->Subtitle.'</h3><p style="text-align:justify;"><span class="p_description">'.$product->Description.'</span> <a class="link" href="'.$product->Link.'">(Lees meer)</a></p><h3 class="clc_price p_price_box">€ <span class="p_price">'.number_format($product->Price, 2, ',', '').'</span></h3><a class="p_link" id="bestelknop_clc" href="'.$product->Link.'">Bestellen</a></div></div></div>';
					}	
				}
				break;

			case 'custom_layout_2':
				if(!empty($options['custom_layout_2'])){
					foreach($class->Products as $product){
						$layout = $options['custom_layout_2'];
						$layout = str_replace('{{link}}', $product->Link, $layout);
						$layout = str_replace('{{Link}}', $product->Link, $layout);
						$layout = str_replace('{{image}}', $product->Image, $layout);
						$layout = str_replace('{{Image}}', $product->Image, $layout);
						$layout = str_replace('{{title}}', $product->Title, $layout);
						$layout = str_replace('{{Title}}', $product->Title, $layout);
						$layout = str_replace('{{subtitle}}', $product->Subtitle, $layout);
						$layout = str_replace('{{Subtitle}}', $product->Subtitle, $layout);
						$layout = str_replace('{{description}}', $product->Description, $layout);
						$layout = str_replace('{{Description}}', $product->Description, $layout);
						$layout = str_replace('{{price}}', number_format($product->Price, 2, ',', ''), $layout);
						$layout = str_replace('{{Price}}', number_format($product->Price, 2, ',', ''), $layout);

						$output .= $layout;
					}
				} else {
					foreach($class->Products as $product){
						$output .= '<div class="product"><div style="display:flex;width:100%;margin-bottom:2em;"><div style="flex:45%;padding-right:5%;"> <a class="p_link" href="'.$product->Link.'"> <img class="p_image" src="'.$product->Image.'" width="100%" height="auto" loading="lazy"> </a></div><div style="flex:65%;"><h2 class="p_title">'.$product->Title.'</h2><h3 class="p_subtitle">'.$product->Subtitle.'</h3><p style="text-align:justify;"><span class="p_description">'.$product->Description.'</span> <a class="link" href="'.$product->Link.'">(Lees meer)</a></p><h3 class="clc_price p_price_box">€ <span class="p_price">'.number_format($product->Price, 2, ',', '').'</span></h3><a class="p_link" id="bestelknop_clc" href="'.$product->Link.'">Bestellen</a></div></div></div>';
					}	
				}
				break;

			case 'custom_layout_3':
				if(!empty($options['custom_layout_3'])){
					foreach($class->Products as $product){
						$layout = $options['custom_layout_3'];
						$layout = str_replace('{{link}}', $product->Link, $layout);
						$layout = str_replace('{{Link}}', $product->Link, $layout);
						$layout = str_replace('{{image}}', $product->Image, $layout);
						$layout = str_replace('{{Image}}', $product->Image, $layout);
						$layout = str_replace('{{title}}', $product->Title, $layout);
						$layout = str_replace('{{Title}}', $product->Title, $layout);
						$layout = str_replace('{{subtitle}}', $product->Subtitle, $layout);
						$layout = str_replace('{{Subtitle}}', $product->Subtitle, $layout);
						$layout = str_replace('{{description}}', $product->Description, $layout);
						$layout = str_replace('{{Description}}', $product->Description, $layout);
						$layout = str_replace('{{price}}', number_format($product->Price, 2, ',', ''), $layout);
						$layout = str_replace('{{Price}}', number_format($product->Price, 2, ',', ''), $layout);

						$output .= $layout;
					}
				} else {
					foreach($class->Products as $product){
						$output .= '<div class="product"><div style="display:flex;width:100%;margin-bottom:2em;"><div style="flex:45%;padding-right:5%;"> <a class="p_link" href="'.$product->Link.'"> <img class="p_image" src="'.$product->Image.'" width="100%" height="auto" loading="lazy"> </a></div><div style="flex:65%;"><h2 class="p_title">'.$product->Title.'</h2><h3 class="p_subtitle">'.$product->Subtitle.'</h3><p style="text-align:justify;"><span class="p_description">'.$product->Description.'</span> <a class="link" href="'.$product->Link.'">(Lees meer)</a></p><h3 class="clc_price p_price_box">€ <span class="p_price">'.number_format($product->Price, 2, ',', '').'</span></h3><a class="p_link" id="bestelknop_clc" href="'.$product->Link.'">Bestellen</a></div></div></div>';
					}	
				}
				break;
				
			case 'carousel_large':
				if(isset($options['enable_carousel'])){
					$id = rand();
					$output .= '<div class="glider-contain">
					  <div class="glider-'.$id.' glider">';
					$p = 0;
					foreach($class->Products as $product){
						$output .= '<div class="product"><div style="display:flex;width:100%;margin-bottom:2em;"><div style="flex:45%;padding-right:5%;"> <a class="p_link" href="'.$product->Link.'"> <img class="p_image" src="'.$product->Image.'" width="100%" height="auto" loading="lazy"> </a></div><div style="flex:65%;"><h2 class="p_title">'.$product->Title.'</h2><h3 class="p_subtitle">'.$product->Subtitle.'</h3><p style="text-align:justify;"><span class="p_description">'.$product->Description.'</span> <a class="link" href="'.$product->Link.'">(Lees meer)</a></p><h3 class="clc_price p_price_box">€ <span class="p_price">'.number_format($product->Price, 2, ',', '').'</span></h3><a class="p_link" id="bestelknop_clc" href="'.$product->Link.'">Bestellen</a></div></div></div>';
						$p++;
					}
					
					$dots_script = NULL;
					$dots_html = NULL;
					if($p < 5){
						$dots_script = 'dots: ".dots-'.$id.'",';
						$dots_html = '<div role="tablist" class="dots dots-'.$id.'"></div>';
					}
					
					$output .= '</div>

					  <button role="button" aria-label="Previous" class="glider-prev glider-prev-'.$id.'">«</button>
					  <button role="button" aria-label="Next" class="glider-next glider-next-'.$id.'">»</button>
					  '.$dots_html.'
					</div>';

					$productsToShow = 1;
					if(isset($atts['productsToShow'])){
						$productsToShow = $atts['productsToShow'];
					}

					$productsToScroll = 1;
					if(isset($atts['productsToScroll'])){
						$productsToScroll = $atts['productsToScroll'];
					}
					
					$output .= '<script>new Glider(document.querySelector(".glider-'.$id.'"), {
					  slidesToShow: '.$productsToShow.',
					  slidesToScroll: '.$productsToScroll.',
					  draggable: true,
					  '.$dots_script.'
					  arrows: {
						prev: ".glider-prev-'.$id.'",
						next: ".glider-next-'.$id.'"
					  }
					});</script>';

					break;
					
				}
				
			case 'large':
				foreach($class->Products as $product){
					$output .= '<div class="product"><div style="display:flex;width:100%;margin-bottom:2em;"><div style="flex:45%;padding-right:5%;"> <a class="p_link" href="'.$product->Link.'"> <img class="p_image" src="'.$product->Image.'" width="100%" height="auto" loading="lazy"> </a></div><div style="flex:65%;"><h2 class="p_title">'.$product->Title.'</h2><h3 class="p_subtitle">'.$product->Subtitle.'</h3><p style="text-align:justify;"><span class="p_description">'.$product->Description.'</span> <a class="link" href="'.$product->Link.'">(Lees meer)</a></p><h3 class="clc_price p_price_box">€ <span class="p_price">'.number_format($product->Price, 2, ',', '').'</span></h3><a class="p_link" id="bestelknop_clc" href="'.$product->Link.'">Bestellen</a></div></div></div>';
				}
				break;	

			case 'carousel_small':
				if(isset($options['enable_carousel'])){
					$id = rand();
					$output .= '<div class="glider-contain">
					  <div class="glider glider-'.$id.'">';
					
					foreach($class->Products as $product){
						$output .= '<div style="padding: 0 1em;"><div> <a class="p_link p_imagebox_'.$id.'" href="'.$product->Link.'" style="display:block;"> <img class="p_image p_image_'.$id.'" src="'.$product->Image.'" width="100%" height="auto" loading="lazy"> </a><p class="p_title glider_p_title" title="'.$product->Title.'"><strong>'.$product->Title.'</strong></p><p class="clc_price p_price_box">€ <span class="p_price">'.number_format($product->Price, 2, ',', '').'</span></p></div><a class="p_link" id="bestelknop_clc" href="'.$product->Link.'">Bestellen</a></div>';
					}
					
					$output .= '</div>

					  <button role="button" aria-label="Previous" class="glider-prev glider-prev-'.$id.'">«</button>
					  <button role="button" aria-label="Next" class="glider-next glider-next-'.$id.'">»</button>
					  <div role="tablist" class="dots dots-'.$id.'"></div>
					</div>';

					$productsToShow = 4;
					if(isset($atts['visible'])){
						$productsToShow = $atts['visible'];
					}

					$output .= '<script>new Glider(document.querySelector(".glider-'.$id.'"), {
					  slidesToShow: '.$productsToShow.',
					  slidesToScroll: '.$productsToShow.',
					  draggable: true,
					  dots: ".dots-'.$id.'",
					  arrows: {
						prev: ".glider-prev-'.$id.'",
						next: ".glider-next-'.$id.'"
					  }
					});</script>
					<script>
						jQuery(function(){
							var items = document.getElementsByClassName("p_image_'.$id.'");
							var h = 0;
							for(var i=0;i<items.length;i++){
								if(document.querySelectorAll(".p_image_'.$id.'")[i].offsetHeight > h){
									h = document.querySelectorAll(".p_image_'.$id.'")[i].offsetHeight;
								}
							}
							if(h != 0 && h != 1){
								for(var i=0;i<items.length;i++){
									document.querySelectorAll(".p_imagebox_'.$id.'")[i].style.height = h+"px";
									document.querySelectorAll(".p_image_'.$id.'")[i].style.maxHeight = h+"px";
								}
							}
								
							jQuery(window).scroll(function(){
								if(h == 0 || h == 1){
									var items = document.getElementsByClassName("p_image_'.$id.'");
									for(var i=0;i<items.length;i++){
										if(document.querySelectorAll(".p_image_'.$id.'")[i].offsetHeight > h){
											h = document.querySelectorAll(".p_image_'.$id.'")[i].offsetHeight;
										}
									}
									if(h != 0 && h != 1){
										for(var i=0;i<items.length;i++){
											document.querySelectorAll(".p_imagebox_'.$id.'")[i].style.height = h+"px";
											document.querySelectorAll(".p_image_'.$id.'")[i].style.maxHeight = h+"px";
										}
									}
								}
							});
						});
					</script>';

					break;
					
				}
				
			case 'small':
				foreach($class->Products as $product){
					$output .= '<div style="display:block;width:100%;margin-bottom:2em;"> <a class="p_link" href="'.$product->Link.'"> <img class="p_image" src="'.$product->Image.'" width="100%" height="auto" loading="lazy"> </a><h2 class="p_title">'.$product->Title.'</h2><h3 class="p_subtitle">'.$product->Subtitle.'</h3><p style="text-align:justify;"><span class="p_description">'.$product->Description.'</span> <a class="link" href="'.$product->Link.'">(Lees meer)</a></p><h3 class="clc_price p_price_box">€ <span class="p_price">'.number_format($product->Price, 2, ',', '').'</span></h3> <a class="p_link" id="bestelknop_clc" href="'.$product->Link.'">Bestellen</a></div>';
				}
				break;
				
			case 'page_small':
				$className = 'class-'.uniqid();
				$width = 25;
				$smallWidth = 100;
				if(!empty($atts['visible'])){
					$width = 100/$atts['visible'];
				}
				if($width*2 < 100){
					$smallWidth = $width*2;
				}
				
				$output .= '<style>
				.'.$className.' {
					width: '.$width.'%;
				}
				@media only screen and (max-width: 764px) {
					.'.$className.' {
						width: '.$smallWidth.'%;
					}
				}
				</style>';
				$output .= '<div style="display:flex;flex-wrap:wrap;margin:0 -1em;">';
				foreach($class->Products as $product){
					$output .= '<div class="page_product_block '.$className.'" style="display:block;margin-bottom:2em;padding:1em;"> <a class="p_link page_small_image_box" href="'.$product->Link.'" style="display:block;height:275px;"><img class="p_image page_small_image" src="'.$product->Image.'" style="max-height:275px;max-width:100%;display: block;margin:0 auto;" loading="lazy"> </a><h5 title="'.$product->Title.'" class="p_title page_small_title">'.$product->Title.'</h5><h6 title="'.$product->Subtitle.'" class="p_subtitle page_small_title">'.$product->Subtitle.'</h6><p class="clc_price p_price_box">€ <span class="p_price">'.number_format($product->Price, 2, ',', '').'</span></p> <a class="p_link" id="bestelknop_clc" href="'.$product->Link.'">Bestellen</a></div>';
				}
				$output .= '</div>
				
				<script>
					jQuery(function(){
						var items = document.getElementsByClassName("page_small_image");
						var h = 0;
						for(var i=0;i<items.length;i++){
							if(document.querySelectorAll(".page_small_image")[i].offsetHeight > h){
								h = document.querySelectorAll(".page_small_image")[i].offsetHeight;
							}
						}
						if(h != 0 && h != 1){
							for(var i=0;i<items.length;i++){
								document.querySelectorAll(".page_small_image_box")[i].style.height = h+"px";
								document.querySelectorAll(".page_small_image")[i].style.maxHeight = h+"px";
							}
						}
							
						jQuery(window).scroll(function(){
							if(h == 0 || h == 1){
								var items = document.getElementsByClassName("page_small_image");
								for(var i=0;i<items.length;i++){
									if(document.querySelectorAll(".page_small_image")[i].offsetHeight > h){
										h = document.querySelectorAll(".page_small_image")[i].offsetHeight;
									}
								}
								if(h != 0 && h != 1){
									for(var i=0;i<items.length;i++){
										document.querySelectorAll(".page_small_image_box")[i].style.height = h+"px";
										document.querySelectorAll(".page_small_image")[i].style.maxHeight = h+"px";
									}
								}
							}
						});
					});
				</script>';
				break;
				
			default:
				foreach($class->Products as $product){
					$output .= '<div class="product"><div style="display:flex;width:100%;margin-bottom:2em;justify-content:space-between;"><div style="flex:45%;padding-right:5%;"> <a class="p_link" href="'.$product->Link.'"> <img class="p_image" src="'.$product->Image.'" width="100%" height="auto" loading="lazy"> </a></div><div style="flex:65%;"><h2 class="p_title">'.$product->Title.'</h2><h3 class="p_subtitle">'.$product->Subtitle.'</h3><p style="text-align:justify;"><span class="p_description">'.$product->Description.'</span> <a class="link" href="'.$product->Link.'">(Lees meer)</a></p><h3 class="clc_price p_price_box">€ <span class="p_price">'.number_format($product->Price, 2, ',', '').'</span></h3><a class="p_link" id="bestelknop_clc" href="'.$product->Link.'">Bestellen</a></div></div></div>';
				}
				break;
		}
	}
	
	if(!empty($output)){
		return $output;
	} else {
		//return json_encode($atts);'
		return;
	}
}

function CLC_Products_settings_page() {
    add_options_page( 'CLC_Products', 'CLC Products', 'manage_options', 'clc-products', 'CLC_Products_render_settings_page' );
}

function CLC_Products_render_settings_page() {
    ?><h2>CLC Products</h2><h3>Instructies</h3><p>Voeg een blok toe in de 'Gutenberg Block Editor' of plaats een shortcode met de juiste titel of isbn (voorbeelden hieronder) op een pagina om producten weer te geven. <table class="form-table"><tbody><tr><th scope="row">Voorbeeld van standaard shortcode</th><td><p><code>[clc-product isbn="9789491935060"]</code></p><p><code>[clc-product title="Nederig leven"]</code></p></td></tr><tr><th scope="row">Voorbeeld van standaard shortcode met meerder producten</th><td><p><code>[clc-product isbn="9789491935060,9789491935251"]</code></p><p><code>[clc-product title="Nederig leven, X- Vermenigvuldigen"]</code></p></td></tr><tr><th scope="row">Voorbeeld van shortcode voor een smalle kolom</th><td><p><code>[clc-product isbn="9789491935060" type="small"]</code></p><p><code>[clc-product title="Nederig leven" type="small"]</code></p></td></tr><tr><th scope="row">Voorbeeld van shortcode voor een brede kolom</th><td><p><code>[clc-product isbn="9789491935060" type="large"]</code></p><p><code>[clc-product title="Nederig leven" type="large"]</code></p></td></tr><tr><th scope="row">Voorbeeld van shortcode voor een link</th><td><p><code>[clc-product isbn="9789491935060" type="link"]</code></p><p><code>[clc-product title="Nederig leven" type="link"]</code></p></td></tr></tbody></table><hr style="margin-bottom:2em;"/><form action="options.php" method="post"> <?php settings_fields( 'CLC_Products_options' ); do_settings_sections( 'CLC_Products' ); ?> <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" /></form><hr style="margin-bottom:2em;"/><h3>Voorbeeld voor custom layout</h3> <code>&lt;div class="product">&lt;div style="display:flex;width:100%;margin-bottom:2em;">&lt;div style="flex:45%;padding-right:5%;">&lt;a class="p_link" href="{{link}}">&lt;img class="p_image" src="{{image}}" width="100%" height="auto">&lt;/a>&lt;/div>&lt;div style="flex:65%;">&lt;h2 class="p_title">{{title}}&lt;/h2>&lt;h3 class="p_subtitle">{{subtitle}}&lt;/h3>&lt;p style="text-align:justify;">&lt;span class="p_description">{{description}}&lt;/span> &lt;a class="link" href="{{link}}">(Lees meer)&lt;/a>&lt;/p>&lt;h3 style="color:#900;font-weight: bold;">€ &lt;span class="p_price">{{price}}&lt;/span>&lt;/h3>&lt;a class="p_link" id="bestelknop_clc" href="{{link}}">Bestellen&lt;/a>&lt;/div>&lt;/div>&lt;/div></code><br/><h3>Variabelen in de custom layout</h3><ul style="list-style-type:none;"><li>Titel: <code>{{title}}</code></li><li>Ondertitel: <code>{{subtitle}}</code></li><li>Beschrijving: <code>{{description}}</code></li><li>Link: <code>{{link}}</code></li><li>Afbeelding: <code>{{image}}</code></li></ul><?php
}

function CLC_Products_register_settings() {
    register_setting( 'CLC_Products_options', 'CLC_Products_options' );
	
    add_settings_section( 'clc_settings', 'Instellingen', 'CLC_Products_section_text', 'CLC_Products' );

	add_settings_field( 'clc_plugin_setting_affiliate_tag', 'Affiliate Tag', 'clc_plugin_setting_affiliate_tag', 'CLC_Products', 'clc_settings' );
	add_settings_field( 'clc_plugin_setting_enable_carousel', 'Enable Carousel', 'clc_plugin_setting_enable_carousel', 'CLC_Products', 'clc_settings' );
	add_settings_field( 'clc_plugin_title', '', 'clc_plugin_title', 'CLC_Products', 'clc_settings' );
	add_settings_field( 'clc_plugin_setting_custom_layout_1', 'Custom Layout 1', 'clc_plugin_setting_custom_layout_1', 'CLC_Products', 'clc_settings' );
	add_settings_field( 'clc_plugin_setting_custom_layout_2', 'Custom Layout 2', 'clc_plugin_setting_custom_layout_2', 'CLC_Products', 'clc_settings' );
	add_settings_field( 'clc_plugin_setting_custom_layout_3', 'Custom Layout 3', 'clc_plugin_setting_custom_layout_3', 'CLC_Products', 'clc_settings' );
	add_settings_field( 'clc_plugin_setting_custom_styling', 'Custom Styling', 'clc_plugin_setting_custom_styling', 'CLC_Products', 'clc_settings' );
}

function CLC_Products_options_validate( $input ) {
    return;
}

function CLC_Products_section_text(){
	return;
}

function clc_plugin_title(){
	echo "<h3>Gebruik onderstaand gedeelte alleen als u zeker weet wat u doet!</h3>";
}

function clc_plugin_setting_affiliate_tag() {
    $options = get_option( 'CLC_Products_options' );
    echo "<input id='clc_plugin_setting_affiliate_tag' name='CLC_Products_options[affiliate_tag]' type='text' value='".esc_attr( $options['affiliate_tag'] )."' /><p>Heeft u geen Affiliate Tag? Stuur dan een mailtje naar <a href='mailto:webshop@clcnederland.com?subject=Mijn%20Affiliate%20Tag%3F'>webshop@clcnederland.com</a> om een Affiliate Tag aan te vragen.</p>";
}

function clc_plugin_setting_enable_carousel() {
	$checked = NULL;
    $options = get_option( 'CLC_Products_options' );
	if(isset($options['enable_carousel'])){
		$checked = 'checked';
	}
    echo "<input id='clc_plugin_setting_enable_carousel' name='CLC_Products_options[enable_carousel]' type='checkbox' value='1' ".$checked." />";
}

function clc_plugin_setting_custom_layout_1() {
    $options = get_option( 'CLC_Products_options' );
	echo "<textarea id='clc_plugin_setting_custom_layout_1' name='CLC_Products_options[custom_layout_1]' type='text' style='width:100%;min-height:150px;'>".$options['custom_layout_1']."</textarea><p>Shortcode voor deze custom layout: <code>[clc-product isbn=\"9789491935060\" type=\"custom_layout_1\"]</code></p>";  
}

function clc_plugin_setting_custom_layout_2() {
    $options = get_option( 'CLC_Products_options' );
	echo "<textarea id='clc_plugin_setting_custom_layout_2' name='CLC_Products_options[custom_layout_2]' type='text' style='width:100%;min-height:150px;'>".$options['custom_layout_2']."</textarea><p>Shortcode voor deze custom layout: <code>[clc-product isbn=\"9789491935060\" type=\"custom_layout_3\"]</code></p>";  
}

function clc_plugin_setting_custom_layout_3() {
    $options = get_option( 'CLC_Products_options' );
	echo "<textarea id='clc_plugin_setting_custom_layout_3' name='CLC_Products_options[custom_layout_3]' type='text' style='width:100%;min-height:150px;'>".$options['custom_layout_3']."</textarea><p>Shortcode voor deze custom layout: <code>[clc-product isbn=\"9789491935060\" type=\"custom_layout_3\"]</code></p>";  
}

function clc_plugin_setting_custom_styling() {
    $options = get_option( 'CLC_Products_options' );
	echo "<textarea id='clc_plugin_setting_custom_styling' name='CLC_Products_options[custom_styling]' type='text' style='width:100%;min-height:150px;'>".$options['custom_styling']."</textarea><p>Gebruikte Classes in CLC Products: <code>.p_link</code> <code>.p_image</code> <code>.p_title</code> <code>.p_subtitle</code> <code>.p_description</code> <code>.p_price</code></p>";  
}

function clc_settings_link( $links ) {
	// Create the link.
	$settings_link = "<a href='/wp-admin/options-general.php?page=clc-products'>" . __( 'Settings' ) . '</a>';
	// Adds the link to the end of the array.
	array_push(
		$links,
		$settings_link
	);
	return $links;
}

$options = get_option( 'CLC_Products_options' );
if(isset($options['enable_carousel'])){
	add_action ( 'wp_enqueue_scripts', 'add_glider_setup');
}

// add styling
add_action ( 'wp_enqueue_scripts', 'clc_custom_styling');

// register shortcode
add_shortcode('clc-product', 'clc_showProduct');
register_block_type('clc-products/isbn', array('render_callback' => 'clc_showProduct') );
register_block_type('clc-products/title', array('render_callback' => 'clc_showProduct') );
register_block_type('clc-products/cat', array('render_callback' => 'clc_showProduct') );
// register admin menu
add_action( 'admin_menu', 'CLC_Products_settings_page' );
// fill admin menu
add_action( 'admin_init', 'CLC_Products_register_settings' );
// add menu link in plugins page
add_filter( 'plugin_action_links_clc_product.php', 'clc_settings_link' );

// Load assets for wp-admin when editor is active
function clc_block_admin() {
   wp_enqueue_script(
      'clc-block-editor',
      plugins_url( 'clc-block.js', __FILE__ ),
      array( 'wp-blocks', 'wp-element' )
   );
}
 
add_action( 'enqueue_block_editor_assets', 'clc_block_admin' );