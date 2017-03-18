<?php
    /*
    Plugin Name: yoel  easy responsive carousel
    Plugin URI:
    Description: create a responsive carousel easily
    Author: yoel rosfisher
    Version: 1.0
    Author URI:
    */

	include( 'cuztom/cuztom.php' );
	///500 error becuse its not on init sanitize_categoryaction add_action
	//add_action('init', array( $this, 'register_post_type' ) ); to
	//post type class constructor


/*
usge


*/


error_reporting(E_ALL);
ini_set('display_errors', 1);
////////carousels///////////
//postype
$carousels = new Cuztom_Post_Type( 'yoel_carousels', array(
    'has_archive' => true,
    'supports' => array( 'title' )
	) );
	//carousel category s

	//images
	$carousels->add_meta_box(
        'carousel_imgs',
    'carousels content',
    array(

		//add imges
		'bundle',
        array(
         /*   array(
                'name'          => 'img_title',
                'label'         => 'img title',
                'description'   => 'the imgs title',
                'type'          => 'text'
            ),*/
            array(
						'name'          => 'carousel_img',
						'label'         => 'inner Image',
						'description'   => 'select an img that will appear inside the carousel lightbox',
						'type'          => 'image',
					)
        )
    )
	);//end bundle



//shortcodes
//enqu scripts

///carousels output and shortcodes

///shortcodes
//[ycarousels name="carouselstst"]
function ycarousels_shortcode($atts)
{
   extract(shortcode_atts(array(

	  'class' => "carouselstst",
	  'name' => "carouselstst",
	  'size'=>"none"


   ), $atts));

	return yget_carousels($class,$name,$size);
}//end ycarousels_shortcode

add_shortcode('ycarousel', 'ycarousels_shortcode');

function yget_carousels($class,$name,$size=300)
{

	$html = "";

	//get carousels post id by name
	$post =  yget_post_by_title($name,'yoel_carousels');
	$id = $post->ID;

	$html .="<div class='ycarousels_container ".$class."'>";



	$imgs =  get_post_meta($id, '_carousel_imgs', true);

	//$all_meta = get_post_meta($id, '', true);
		//var_dump($all_meta );

	//container

	$html .="<div class='y_single_carousels_container ' id='carousels".$name."'>";


	foreach($imgs as $yimg)
	{
		//var_dump($yimg);
		//$img_title = $yimg['_img_title'];
		$img_id = $yimg['_carousel_img'];

		//get img by id
		$html .= wp_get_attachment_image( $img_id,"full");
	

	}




	$html .="</div></div><!--end ycarousels_container-->";
	wp_reset_query();
	if($size==""){$size=300;}
	$html .= ycarousels_fotter_js($class,$size) ;
	return $html;//echo carousels



}//end yget_carousels


//carousels js

//js and php logic fun
function ycarousels_fotter_js( $name,$size)
{
	$size2 = $size+100;
	$html = '<script>
	
    $( document ).ready(function () {





	
		$("#'.$name.'").coverflow({
			active: 2,
			select: function(event, ui){
				console.log("here");
			}
		});
		//click
		$("#'.$name.' img").click(function() {
			if( ! $(this).hasClass("ui-state-active")){
				return;
			}

			$("#'.$name.'").coverflow("next");


		});
		
		//touch
		$("#'.$name.' img").on("touchstart",function(){
			if( ! $(this).hasClass("ui-state-active")){
				return;
			}
			$("#'.$name.'").coverflow("next");
		});
		
		$("#'.$name.' img").css({width:"'.$size.'px",height:"'.$size.'px !important"});
		//$("#'.$name.'").parent().css({height:"'.$size2.'px"});

});




	';
	//if($type == "norm")	{}//end norm




	//end script
	$html .= ' </script>';
	return $html;
}//end ycarousels_fotter_js


////////on activate plugin/////////

function yoel_bootstrap_carousel_catalog_activation_function()
{


}
register_activation_hook( __FILE__, 'yoel_bootstrap_carousel_catalog_activation_function' );


add_action( 'init', 'ycarousel_init' );

function ycarousel_init()
 {

}

////////render helper funcs (genrate html or js)//////////






function single_carousel_img($img,$height)
{
		$html = "";
		//get the img id
		$img_id = $img["_carousel_img"];
		$html .= "<li>";
		//get the img url
		$img_url = wp_get_attachment_image_src($img_id);
		//$img_id .=wp_get_attachment_image($img_id);
		$html .= '<img src="'.$img_url[0].'" style="height: '.$height.'px;" />';
		if(isset($img["_img_title"]))
		{
				$html .='<p class="caption">'.$img["_img_title"].'</p>';
		}
		$html .= "</li>";
		return $html;
}//end single_slide



///end carousels output and shortcodes



//jqury (also makes sure no conflicts)


function my_jquery_enqueue_yoel_carousels() {
   wp_deregister_script('jquery');
   wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js", false, null);
   wp_enqueue_script('jquery');

	//coverflow
   wp_register_script('coverflow',  plugins_url('js/coverflow.min.js', __FILE__));
    wp_enqueue_script('coverflow');
}



add_action( 'wp_enqueue_scripts', 'my_jquery_enqueue_yoel_carousels' );

////encue style
function ywp_adding_styles_yoel_carousels()
{

	//responsive carousels css
	//wp_enqueue_style('responsiveslides_css', plugins_url('css/responsiveslides.css', __FILE__));
	//wp_enqueue_script('responsiveslides_css');

	wp_enqueue_style('coverflow_css', plugins_url('css/coverflow.css', __FILE__));
	wp_enqueue_script('coverflow_css');
	wp_enqueue_style('ycarusel_css', plugins_url('css/ycarusel.css', __FILE__));
	wp_enqueue_script('ycarusel_css');

	//add bootstrap
	wp_enqueue_style( 'bootstrap-css', "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" );
	wp_enqueue_script( 'bootstrap-js', "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js");

}

add_action( 'wp_enqueue_scripts', 'ywp_adding_styles_yoel_carousels' );

//change viewport for bootstrap
add_action( 'wp_head', function() {
	//echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
} );

////permissions
add_action( 'in_admin_header', function()
{
	//only admin can edit
	if (!current_user_can('activate_plugins') && $_GET['post_type']=="yoel_carousels"){
        /*do something*/
			//return;

			exit("<h1>you dont have permission to use this page</h1>");
	}
} );


////add help page
function yadd_submenus_pages_yoel_carousels()
{
	add_submenu_page(
		'edit.php?post_type=yoel_carousels',
		'how to use yoel carousels', /*page title*/
		'how to use yoel carousels', /*menu title*/
		'manage_options', /*roles and capabiliyt needed*/
		'wnm_fund_set',
		'yhelp_page_yoel_carousels' /*replace with your own function*/
	);
}
add_action( 'admin_menu', 'yadd_submenus_pages_yoel_carousels' );

function yhelp_page_yoel_carousels()
{
	//chack user level_10
	if (current_user_can('level_10')){
        /*do something*/
		//	return;
	}
	echo '<div class="wrap"><h2>how to use</h2></div>';
	//$src = plugin_dir_path( __FILE__ ."help.swf");
	//$src =  plugins_url( 'help.swf' , __FILE__ );
	//vidio link
	//echo '<a href="'.$src.'">video tutorial</a>';
	//example shortodes
	echo "<h2>example shortcodes</h2>";


	echo "<h3>get carousels by carousel post name</h3>";
	echo '<input type="text" value=\'[ycarousel name="some post name" ]\' size="33" style="
    direction: ltr;
"/></br></br>';



}//end yhelp_page_yoel_carousels

////////end carousels///////////


////////castum colloums/////////////////////
$postype = "yoel_carousels";

///////add colums////////
add_filter( 'manage_edit-'.$postype.'_columns',

function ( $columns ) {
	//cullom names
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'title' ),
		"shortcode" =>__( 'shortcode' )

		//
		//'title' => __( 'Movie' ),
		//'duration' => __( 'Duration' ),
		//'genre' => __( 'Genre' ),
		//'date' => __( 'Date' )
	);

	return $columns;
});

///add collum data///
add_action( 'manage_'.$postype.'_posts_custom_column',

function ( $column, $post_id )
{
	//collums content
	global $post;
	/* If displaying the 'name_date' column. */
	if( $column == "shortcode")
	{

		//[yslider name="slider_name"]
		echo "<input type='text' size='35' value='[ycarousel name=\"".$post->post_title."\"]'  style='
				direction: ltr;
			'/>"  ;

	}
}, 10, 2 );


////////end castum colloums/////////////////////


////helper funcs/////
if(!function_exists('yget_post_by_title')) {

function yget_post_by_title($page_title,$postype, $output = OBJECT) {
    global $wpdb;
        $post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='".$postype."'", $page_title ));
        if ( $post )
            return get_post($post, $output);

    return null;
}

}//end function_exists

function yget_carousels_by_cat($cat)
{
	if($cat == "none")
	{
		$quary = 'post_type=yoel_carousels';
	}
	else
	{
		$cat = get_term_by('name', $cat, 'carousels_category');//get cat id

		if(!$cat){return;}//if not exist exit
		//$quary = 'post_type=yoel_carousels&carousels_category='.$cat->term_id;

		$quary = array("post_type"=>'yoel_carousels','tax_query' => array(
        array(
            'taxonomy' => 'carousels_category',
            'field' => 'id',
            'terms' =>  $cat->term_id,
            'operator' => 'AND' )
		));

	}

	return new WP_Query($quary);
}
?>