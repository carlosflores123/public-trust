<?php
/**
 * Public-trust Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Public-trust
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_PUBLIC_TRUST_VERSION', '1.0.0' );
define( 'SCRIPT_DEBUG', true );
/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'public-trust-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_PUBLIC_TRUST_VERSION, 'all' );
	wp_enqueue_style( 'public-trust-theme-print-css', get_stylesheet_directory_uri() . '/print.css', array('astra-theme-css'), CHILD_THEME_PUBLIC_TRUST_VERSION, 'print' );
	wp_enqueue_style( 'nice-select-css', get_stylesheet_directory_uri() . '/nice-select.css', array('astra-theme-css'), CHILD_THEME_PUBLIC_TRUST_VERSION, 'all' );

	wp_enqueue_script( 'custom-nice-select.js', get_stylesheet_directory_uri().'/nice-select.js', array('jquery') );
	wp_enqueue_script( 'custom-script.js', get_stylesheet_directory_uri().'/script.js', array('jquery') );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );


add_filter( 'style_loader_src',  'sdt_remove_ver_css_js', 9999, 2 );
add_filter( 'script_loader_src', 'sdt_remove_ver_css_js', 9999, 2 );

function sdt_remove_ver_css_js( $src, $handle )
{
    $handles_with_version = [ 'style' ]; // <-- Adjust to your needs!

    if ( strpos( $src, 'ver=' ) && ! in_array( $handle, $handles_with_version, true ) )
        $src = remove_query_arg( 'ver', $src );

    return $src;
}

/*Ajax custom post type*/
function plugin_ajaxurl() { ?>
    <script type="text/javascript">
        var coolPluginAjaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>";
    </script>
<?php }

add_action('wp_head','plugin_ajaxurl');

add_action('wp_ajax_get_members', 'get_members');
add_action('wp_ajax_nopriv_get_members', 'get_members');

function get_members() {
	$group_number = $_POST['team_number'];
	$location_number = $_POST['location_number'];
	$flag = $_POST['flag'];

	$args = array(
	        'posts_per_page' => -1,
	        'post_type' => 'members',
	        'orderby' => 'publish_date',
    		'order' => 'ASC',
	    );
	if($flag == 0){
		$args['tax_query'] = array(
            array(
                'taxonomy' => 'team_group',
                'field' => 'term_id',
                'terms' => $group_number,
            )
        );
	}else{
		if ($location_number < 2) {
			$args['tax_query'] = array(
	            array(
	                'taxonomy' => 'team_group',
	                'field' => 'term_id',
	                'terms' => $group_number,
	            )
	        );
		}else{
			$args['tax_query'] = array(
				'relation' => 'AND',
	            array(
	                'taxonomy' => 'team_group',
	                'field' => 'term_id',
	                'terms' => $group_number,
	            ),
	            array(
					'taxonomy' => 'location',
					'field' => 'term_id',
					'terms' => $location_number,
				)
	        );
		}
	}

	$locations_info = array();

	$posts_array = get_posts( $args );

	$quantityTermObject = get_term_by('id', $group_number, 'team_group');
	$quantityTermName = $quantityTermObject->name;
	$quantityTermDescription = $quantityTermObject->description;

	$result = '<div class="team-group-info"><h1>'.$quantityTermName.'</h1><p>'.$quantityTermDescription.'</p></div><div class="team-members">';

	foreach ($posts_array as $key => $post) {

		$team_position = get_post_meta($post->ID, 'team_position', true );
		$location_total = get_the_terms($post->ID, 'location');
		$location_id = $location_total[0]->term_id;
		$location_name = $location_total[0]->name;
		if($location_id == ''){
			$location_id = -1;
			$location_name = 'Unknown';
		}

		if (array_key_exists($location_id, $locations_info)){

		}else{
			$locations_info[$location_id] = $location_name;
		}

		if ($location_number == -1 && $flag == 1){
			if($location_id == -1){
				$result .= '<div class="team-member"><div class="team-member-content"><img src="'.wp_get_attachment_url( get_post_thumbnail_id($post->ID) ).'"><h5>'.$post->post_title.'</h5><p>'.$team_position.'</p><span style="display: none;">'.$post->post_content.'</span></div></div>';
			}
		}else{
			$result .= '<div class="team-member"><div class="team-member-content"><img src="'.wp_get_attachment_url( get_post_thumbnail_id($post->ID) ).'"><h5>'.$post->post_title.'</h5><p>'.$team_position.'</p><span style="display: none;">'.$post->post_content.'</span></div></div>';
		}
	}

	$result .= '<div class="team_member_modal"><div class="modal-content"><span class="close_icon">&times;</span><div class="modal-image"></div><h3 class="modal-member-name"></h3><h5 class="modal-member-title"></h5><div class="modal-member-description"></div></div></div></div>';

	// return $result;

	$return_data = array();
	$return_data['members'] = $result;
	$return_data['locations'] = $locations_info;
	echo json_encode(array('result'=>$return_data, 'response'=>__('notadded','PBT')));
	die();
}


function TeamMemberShortcode() {
	$group_number = 43;
	$location_number = 0;
	$args = array(
        'posts_per_page' => -1,
        'post_type' => 'members',
        'orderby' => 'publish_date',
		'order' => 'ASC',
    );

	if($location_number != 0){
		$args['tax_query'] = array(
			'relation' => 'AND',
            array(
                'taxonomy' => 'team_group',
                'field' => 'term_id',
                'terms' => $group_number,
            ),
            array(
				'taxonomy' => 'location',
				'field' => 'term_id',
				'terms' => $location_number,
			)
        );
	}else{
		$args['tax_query'] = array(
            array(
                'taxonomy' => 'team_group',
                'field' => 'term_id',
                'terms' => $group_number,
            )
        );
	}

	$posts_array = get_posts( $args );

	$quantityTermObject = get_term_by('id', $group_number, 'team_group');
	$quantityTermName = $quantityTermObject->name;
	$quantityTermDescription = $quantityTermObject->description;

	$result='<div class="team-group-info"><h1>'.$quantityTermName.'</h1><p>'.$quantityTermDescription.'</p></div><div class="team-members">';

	foreach ($posts_array as $key => $post) {
		$team_position = get_post_meta($post->ID, 'team_position', true );
		$result .= '<div class="team-member"><div class="team-member-content"><img src="'.wp_get_attachment_url( get_post_thumbnail_id($post->ID) ).'"><h5>'.$post->post_title.'</h5><p>'.$team_position.'</p><span style="display: none;">'.$post->post_content.'</span></div></div>';
	}

	$result .= '<div class="team_member_modal"><div class="modal-content"><span class="close_icon">&times;</span><div class="modal-image"></div><h3 class="modal-member-name"></h3><h5 class="modal-member-title"></h5><div class="modal-member-description"></div></div></div></div>';

	return $result;
}

function TeamMemberShortcodeNew(){
	$executive_number = 13;
	$result = '';
	$args = array(
        'posts_per_page' => -1,
        'post_type' => 'members',
        'orderby' => 'publish_date',
		'order' => 'ASC',
    );

	$args['tax_query'] = array(
        array(
            'taxonomy' => 'team_group',
            'field' => 'term_id',
            'terms' => $executive_number,
        )
    );

	$posts_array = get_posts( $args );

	$quantityTermObject = get_term_by('id', $executive_number, 'team_group');
	$quantityTermName = $quantityTermObject->name;
	$quantityTermDescription = $quantityTermObject->description;

	$result='<div class="team-group-info"><h1>'.$quantityTermName.'</h1><p>'.$quantityTermDescription.'</p></div><div class="team-members executive-team">';

	foreach ($posts_array as $key => $post) {
		$team_position = get_post_meta($post->ID, 'team_position', true );
		$result .= '<div class="team-member"><div class="team-member-content"><img src="'.wp_get_attachment_url( get_post_thumbnail_id($post->ID) ).'"><h5>'.$post->post_title.'</h5><p>'.$team_position.'</p><span style="display: none;">'.$post->post_content.'</span></div></div>';
	}

	$result .= '<div class="team_member_modal"><div class="modal-content"><span class="close_icon">&times;</span><div class="modal-image"></div><h3 class="modal-member-name"></h3><h5 class="modal-member-title"></h5><div class="modal-member-description"></div></div></div></div>';

	$terms = get_terms( array(
	    'taxonomy' => 'team_group',
	    'hide_empty' => false,
	    'orderby' => 'slug',
		'order' => 'ASC',
	) );

	foreach ($terms as $key => $term) {

		$group_number = $term->term_id;
		if(($executive_number != $group_number) && ($group_number != 43)){
			$group_name = $term->name;
			$quantityTermObject = get_term_by('id', $group_number, 'team_group');
			$group_Description = $quantityTermObject->description;

			$args = array(
		        'posts_per_page' => -1,
		        'post_type' => 'members',
		        'orderby' => 'publish_date',
				'order' => 'ASC',
		    );

			$args['tax_query'] = array(
		        array(
		            'taxonomy' => 'team_group',
		            'field' => 'term_id',
		            'terms' => $group_number,
		        )
		    );

			$posts_array = get_posts( $args );
			$result .= '<div class="team-group-info"><div class="other-team-header"><h2>'.$group_name.'</h2><div class="team-member-hide-tag">+</div></div><div class="team-info-active"><p>'.$group_Description.'</p><div class="team-members">';

			foreach ($posts_array as $key => $post) {
				$team_position = get_post_meta($post->ID, 'team_position', true );
				$result .= '<div class="team-member"><div class="team-member-content"><img src="'.wp_get_attachment_url( get_post_thumbnail_id($post->ID) ).'"><h5>'.$post->post_title.'</h5><p>'.$team_position.'</p><span style="display: none;">'.$post->post_content.'</span></div></div>';
			}
			$result .='</div></div></div>';
		}
	}
	return $result;

}

add_shortcode('team_member', 'TeamMemberShortcodeNew');


function TeamMemberSearchShortcode(){

	$group_number = 43;
	$location_number = 0;

	$terms = get_terms( array(
	    'taxonomy' => 'team_group',
	    'hide_empty' => false,
	) );

	$result = '<div id="search_form"><div class="selectdiv"><label><select class="select_team_form" name="group">';
	// if($group_number == 0){
	// 	$result .='<option value="0" selected>All Members</option>';
	// }else{
	// 	$result .='<option value="0">All Members</option>';
	// }

	foreach ($terms as $key => $term) {
		if ($group_number == $term->term_id) {
			$result .= '<option value="'.$term->term_id.'" selected>'.$term->name.' </option>';
		}else{
			$result .= '<option value="'.$term->term_id.'">'.$term->name.' </option>';
		}
	}

	$result .='</select></label></div><div class="team_member_or">OR</div><div class="selectdiv"><label><select class="select_location_form" name="location"><option value="0" selected>All locations</option>';

	$locations = get_terms( array(
	    'taxonomy' => 'location',
	    'hide_empty' => false,
	) );

	foreach ($locations as $key => $location) {
		if ($location_number == $location->term_id) {
			$result .= '<option value="'.$location->term_id.'" selected>'.$location->name.' </option>';
		}else{
			$result .= '<option value="'.$location->term_id.'">'.$location->name.' </option>';
		}
	}

	// $result .='</select></label></div><div class="searchdiv"><input placeholder="Search by location" class="search_form" type="search" name="search_form" title="Search" value="'.$location.'"><button class="search_location_form" type="submit">
	// 										<i class="fa fa-search" aria-hidden="true"></i>
	// 								</button></div>';

	return $result."</select></label></div></div>";
}

add_shortcode('team_member_search', 'TeamMemberSearchShortcode');

add_action(
    /* Pre-Elementor 2.5.1 use this:
    'elementor_pro/posts/query/lwp_related_posts', */
    'elementor/query/lwp_related_posts',
    'lwp_3266_related_posts'
);
function lwp_3266_related_posts( $query ) {
    global $post;
    $query->set(
        'post__in',
        km_rpbt_get_related_posts(
            $post->ID,
            array( 'fields' => 'ids' )
        )
    );
}

function LocationViewShortcode(){
	$args = array(
	        'posts_per_page' => -1,
	        'post_type' => 'office_locations',
	        'orderby' => 'publish_date',
    		'order' => 'ASC',
	);

	$array_posts = get_posts($args);
	$result = '';
	foreach ($array_posts as $key => $post) {
		$post_id = $post->ID;
		$phone_number = get_post_meta($post_id, 'phone_number', $single = false);
		$address = get_post_meta($post_id, 'address', $single = false);
		$image = get_the_post_thumbnail( $post_id, 'master', array( 'class' => 'alignleft' ) );
		// $result .= '<div class="office-location"><div class="office-location-info">' .$image.'<a href="'.get_post_permalink( $post_id, false, $sample = false ).'" class="location_title">'.$post->post_title.'</a><p class="address">'.$address[0].'</p><a href= "tel:'.$phone_number[0].'" class="phone_number">'.$phone_number[0].'</a></div></div>';
		$result .= '<div class="office-location"><div class="office-location-info">' .$image.'<div class="location_title">'.$post->post_title.'</div><p class="address">'.$address[0].'</p><a href= "tel:'.$phone_number[0].'" class="phone_number">'.$phone_number[0].'</a></div></div>';
	}
	return $result;
}

add_shortcode('location_view', 'LocationViewShortcode');

function StickyOnePost(){

	$posts = get_posts(array(
	  'post_type' => 'post',
	  'posts_per_page' => -1,
	  'tax_query' => array(
	    array(
	      'taxonomy' => 'post_tag',
	      'field' => 'term_id',
	      'terms' => 21
	    )
	  )
	));
	$sticky_post = $posts[0];
	$post_id = $sticky_post->ID;
	$category = get_the_category($post_id);
	$image = get_the_post_thumbnail( $post_id, 'master', array( 'class' => 'alignleft' ) );
	$result .= '<div class="sticky-first-post">'.$image.'<div class="sticky-post-info"><div class="sticky-first-post-title"><h4>'.$category[0]->name.'</h4><a href="'.get_post_permalink( $post_id, false, $sample = false ).'" class="sticky_title">'.$sticky_post->post_title.'</a></div><div class="sticky-first-post-excerpt">'.mb_strimwidth($sticky_post->post_excerpt, 0, 200, "...").'<a href="'.get_post_permalink( $post_id, false, $sample = false ).'" class="sticky_title">read more</a></div></div>';
	return $result;
}

add_shortcode('sticky_one_post', 'StickyOnePost');

function quarterEconomicGrowth($types){
	global $post;
	$post_id = $post->ID;
	// $type = $types['type'].'_post-link';

	// $special_post_id = get_post_meta($post_id, $type, false);
	// $result = '';
	// if($special_post_id[0]){
	// 	$spec_post = get_post($special_post_id[0]);
	// 	$spec_title = $spec_post->post_title;
	// 	$spec_date = date_format(date_create($spec_post->post_date), 'M d, Y');
	// 	// $spec_date = date_create($spec_post->post_date);
	// 	$author_id=$spec_post->post_author;
	// 	$spec_author = get_the_author_meta('display_name', $author_id);
	// 	$spec_content = $spec_post->post_content;
	// 	$social_icons = '<div class="social_icons"><span><i class="fa fa-twitter" aria-hidden="true"></i></span><span><i class="fa fa-linkedin" aria-hidden="true"></i></span><span><i class="fa fa-facebook" aria-hidden="true"></i></span><span><i class="fa fa-envelope" aria-hidden="true"></i></span><span><i class="fa fa-arrow-down" aria-hidden="true"></i></span></div>';
	// 	$result = '<h1>'.$spec_title.'</h1><h5>'.$spec_date.' | '.$spec_author.'</h5>'.$social_icons.'<div class="striped-border"></div><p>'.$spec_content.'</p>';
	// }
	$type = $types['type'].'_content';
	$content = get_post_meta($post_id, $type, false);

	return wpautop(do_shortcode($content[0]));
	// return $result;
}

add_shortcode('quarter_economic_growth', 'quarterEconomicGrowth');


function QuarterPrevious(){
	global $post;
	$post_date = get_the_date();
	$query_string = array(
	      'post_type' => 'post',
	      'date_query' => array(
	        'before' => $post_date
	      ),
	      'tax_query' => array(
	          array(
	             'taxonomy' => 'category',
	             'field' => 'slug',
	             'terms' => 'economic-update-quarter'
	          )
	      ),
	      'post_status' => 'publish',
	      'posts_per_page' => 3
	);
	$privous_posts = get_posts($query_string);
	$result = '<div class="privous_quarters">';

	$privous_posts = array_reverse($privous_posts);

	foreach ($privous_posts as $key => $privous_post) {
		$result .= '<div class="privous_quarter"><div class="privous_quarter_info">';
		$privous_post_id = $privous_post->ID;
		$privous_post_title = $privous_post->post_title;
		$privous_post_title = str_replace("Quarter ","Q", $privous_post_title);
		$privous_post_excerpt = $privous_post->post_excerpt;
		$result .='<a href ="'.get_post_permalink($privous_post_id).'">'.$privous_post_title.'</a><p>'.$privous_post_excerpt.'</p></div></div>';
	}
	return $result.'</div>';
}

add_shortcode('quarter_previous', 'QuarterPrevious');


function MonthPrevious(){
	global $post;
	$post_date = get_the_date();
	$query_string = array(
	      'post_type' => 'post',
	      'date_query' => array(
	        'before' => $post_date
	      ),
	      'tax_query' => array(
	          array(
	             'taxonomy' => 'category',
	             'field' => 'slug',
	             'terms' => 'economic-update-month'
	          )
	      ),
	      'post_status' => 'publish',
	      'posts_per_page' => 3
	);
	$privous_posts = get_posts($query_string);
	$result = '<div class="privous_quarters">';

	$privous_posts = array_reverse($privous_posts);

	foreach ($privous_posts as $key => $privous_post) {
		$result .= '<div class="privous_quarter"><div class="privous_quarter_info">';
		$privous_post_id = $privous_post->ID;
		$privous_post_title = $privous_post->post_title;
		$privous_post_excerpt = $privous_post->post_excerpt;
		$result .='<a href ="'.get_post_permalink($privous_post_id).'">'.$privous_post_title.'</a><p>'.$privous_post_excerpt.'</p></div></div>';
	}
	return $result.'</div>';
}

add_shortcode('month_previous', 'MonthPrevious');


function CurrentEconomicTable(){
	global $post;

	$rows = get_field('current_economic_releases');

	$result = '<table style="width:100%"><tr><th>Data</th><th>Period</th><th>Value</th></tr>';

  	foreach ($rows as $key => $row) {
  		$result .='<tr><td width="22%">'.$row["data"].'</td><td width="22%">'.$row["period"].'</td><td>'.$row["value"].'</td></tr>';
  	}
  	$result .= '</table>';

	return $result;
}

add_shortcode('current_economic_table', 'CurrentEconomicTable');

function TreasuryTable(){
	global $post;

	$rows = get_field('treasury_yields');

	$period_1 = get_post_meta($post->ID, 'treasury_period_1');
	$period_1 = date_format(date_create($period_1[0]), 'm/d/y');

	$period_2 = get_post_meta($post->ID, 'treasury_period_2');
	$period_2 = date_format(date_create($period_2[0]), 'm/d/y');

	$result = '<table style="width:100%"><tr><th>Maturity</th><th>'.$period_1.'</th><th>'.$period_2.'</th><th>Change</th></tr>';

  	foreach ($rows as $key => $row) {
  		$result .='<tr><td width="22%">'.$row["treasury_maturity"].'</td><td width="22%">'.$row["treasury_period_Value_1"].'</td><td width="22%">'.$row["treasury_period_value_2"].'</td><td class="month_table_bold">'.$row["treasury_change"].'</td></tr>';
  	}
  	$result .= '</table>';

	return $result;
}

add_shortcode('treasury_table', 'TreasuryTable');

function AgencyTable(){
	global $post;

	$rows = get_field('agency_yields');

	$period_1 = get_post_meta($post->ID, 'agency_period_1');
	$period_1 = date_format(date_create($period_1[0]), 'm/d/y');

	$period_2 = get_post_meta($post->ID, 'agency_period_2');
	$period_2 = date_format(date_create($period_2[0]), 'm/d/y');

	$result = '<table style="width:100%"><tr><th>Maturity</th><th>'.$period_1.'</th><th>'.$period_2.'</th><th>Change</th></tr>';

  	foreach ($rows as $key => $row) {
  		$result .='<tr><td width="22%">'.$row["agency_maturity"].'</td><td width="22%">'.$row["agency_period_value_1"].'</td><td width="22%">'.$row["agency_period_value_2"].'</td><td class="month_table_bold">'.$row["agency_change"].'</td></tr>';
  	}
  	$result .= '</table>';

	return $result;
}

add_shortcode('agency_table', 'AgencyTable');

function CommercialTable(){
	global $post;

	$rows = get_field('commercial_paper');

	$period_1 = get_post_meta($post->ID, 'commercial_period_1');
	$period_1 = date_format(date_create($period_1[0]), 'm/d/y');

	$period_2 = get_post_meta($post->ID, 'commercial_period_2');
	$period_2 = date_format(date_create($period_2[0]), 'm/d/y');

	$result = '<table style="width:100%"><tr><th>Maturity</th><th>'.$period_1.'</th><th>'.$period_2.'</th><th>Change</th></tr>';

  	foreach ($rows as $key => $row) {
  		$result .='<tr><td width="22%">'.$row["commercial_maturity"].'</td><td width="22%">'.$row["commercial_period_value_1"].'</td><td width="22%">'.$row["commercial_period_value_2"].'</td><td class="month_table_bold">'.$row["commercial_change"].'</td></tr>';
  	}
  	$result .= '</table>';

	return $result;
}

add_shortcode('commercial_table', 'CommercialTable');

function EventAgendaInfo(){
	global $post;
	$gravity_flag = get_post_meta($post->ID, 'event_gravity', true );
	$rows = get_field('agenda');
	$result = '';
	if($gravity_flag == 0){
		$result = '<h2 class="agenda_header_style">Agenda<h2>';
	  	if(count($rows) > 0){
		  	foreach ($rows as $key => $row) {
		  		$result .= '<div class="agenta-info"><div class="agenda-left-info"><span>'.$row["agenda_time"].'</span></div><div class="agenda-right-info"><h3>'.$row["agenda_title"].'</h3><p>'.do_shortcode($row["agenda_description"]).'</p></div></div>';
		  	}
		}
	}else{
		$gravity_title = get_post_meta($post->ID, 'event_gravity_title', true );
		$gravity_id = get_post_meta($post->ID, 'event_gravity_id', true );

		$result = '<div class="event_gravity_info"><div class="agenda_header_style" style="text-align: center;">'.$gravity_title.'</div>'.event_gravity_short($gravity_id).'</div>';
	}

	return $result;
}

function event_gravity_short($gravity_id){
	return do_shortcode("[gravityform id=$gravity_id title=false description=false ajax=true]");
}

add_shortcode('event_agenda_info', 'EventAgendaInfo');


function EventSpeakersInfo(){
	global $post;
	$gravity_flag = get_post_meta($post->ID, 'event_gravity', true );
	$rows = get_field('speakers');
	$result = '';
	if($gravity_flag == 0){
		$result = '<h2 class="agenda_header_style" style="text-align: center;">Speakers<h2>';
	  	if(count($rows) > 0){
	  		foreach ($rows as $key => $row) {
	  			if($row['member'] != ''){
	  				$member_title = get_the_title($row['member']);
	  				$member_img_url = get_the_post_thumbnail_url($row['member']);
	  				$member_position = get_post_meta($row['member'], 'team_position');
	  				$member_post = get_post($row['member']);
					$member_description = $member_post->post_content;

	  				$result .= '<div class="speaker-infoes"><div class="speaker-info"><img src="'.$member_img_url.'">'.'<h3>'.$member_title.'</h3>'.'<p>'.$member_position[0].'</p><span style="display: none;">'.$member_description.'</span></div></div>';
	  			}else{
	  				$result .= '<div class="speaker-infoes"><div class="speaker-info"><img src="'.$row["speaker_image"].'">'.'<h3>'.$row["speaker_name"].'</h3>'.'<p>'.$row["speaker_title"].'</p><span style="display: none;">'.$row["speaker_description"].'</span></div></div>';
	  			}
	  		}
	  	}
	  	$result .= '<div class="team_member_modal"><div class="modal-content"><span class="close_icon">&times;</span><div class="speaker-modal-image"></div><h3 class="modal-speaker-name"></h3><h5 class="modal-speaker-title"></h5><div class="modal-member-description speaker-desc-info"></div></div></div>';
  	}
	return $result;
}

add_shortcode('event_speakers_info', 'EventSpeakersInfo');

add_action( 'elementor/query/upcoming_event', function( $query ) {
	// Get current meta Query
	$meta_query = $query->get( 'meta_query' );
	// Append our meta query
	$today = date("Y-m-d");


	// $meta_query[] = [
	// 	'key' => 'event_date',
	// 	'meta-value' => $value,
 //       	'value' => $today,
 //       	'compare' => '>=',
	// ];
	$meta_query = array(
        array(
           'key' => 'event_date',
           'meta-value' => $value,
           'value' => $today,
           'compare' => '>=',
       )
    );

	$query->set( 'meta_query', $meta_query );
} );

add_action( 'elementor/query/previous_monthly_reports', function( $query ) {
	global $post;

	$post_date_month = get_the_date();

	$query->set( 'date_query', array('before' => $post_date_month,));
	$query->set( 'cat', 24 );
} );

add_filter( 'gform_confirmation', function ( $confirmation, $form, $entry, $ajax ) {

    $confirmation .= '<script type="text/javascript">jQuery(document).ready(function() {
    						setTimeout(function(){
								if(jQuery(".gform_confirmation_message_6")[0]){
									jQuery("#elementor-tab-title-7392").trigger( "click" );
								}
							}, 500);
    						setTimeout(function(){jQuery("html, body").animate({
							    scrollTop: jQuery(".gform_confirmation_message").offset().top - 50
							}, 100)}, 1000);

						});
					</script>';

    return $confirmation;
}, 10, 4 );


if ( ! function_exists( 'astra_footer_markup' ) ) {

	/**
	 * Site Footer - <footer>
	 *
	 * @since 1.0.0
	 */
	function astra_footer_markup_old() {
		if(!is_page('contact-us')){
		?>

		<footer itemtype="https://schema.org/WPFooter" itemscope="itemscope" id="colophon" <?php astra_footer_classes(); ?> role="contentinfo">

			<?php astra_footer_content_top(); ?>

			<?php astra_footer_content(); ?>

			<?php astra_footer_content_bottom(); ?>

		</footer><!-- #colophon -->
		<?php }
	}
}

	function astra_primary_navigation_markup() {

		$disable_primary_navigation = astra_get_option( 'disable-primary-nav' );
		$custom_header_section      = astra_get_option( 'header-main-rt-section' );

		if ( $disable_primary_navigation ) {

			$display_outside = astra_get_option( 'header-display-outside-menu' );

			if ( 'none' != $custom_header_section && ! $display_outside ) {

				echo '<div class="main-header-bar-navigation ast-header-custom-item ast-flex ast-justify-content-flex-end">';
				/**
				 * Fires before the Primary Header Menu navigation.
				 * Disable Primary Menu is checked
				 * Last Item in Menu is not 'none'.
				 * Take Last Item in Menu outside is unchecked.
				 *
				 * @since 1.4.0
				 */
				do_action( 'astra_main_header_custom_menu_item_before' );

				echo astra_masthead_get_menu_items();

				/**
				 * Fires after the Primary Header Menu navigation.
				 * Disable Primary Menu is checked
				 * Last Item in Menu is not 'none'.
				 * Take Last Item in Menu outside is unchecked.
				 *
				 * @since 1.4.0
				 */
				do_action( 'astra_main_header_custom_menu_item_after' );

				echo '</div>';

			}
		} else {

			$submenu_class = apply_filters( 'primary_submenu_border_class', ' submenu-with-border' );

			// Menu Animation.
			$menu_animation = astra_get_option( 'header-main-submenu-container-animation' );
			if ( ! empty( $menu_animation ) ) {
				$submenu_class .= ' astra-menu-animation-' . esc_attr( $menu_animation ) . ' ';
			}

			/**
			 * Filter the classes(array) for Primary Menu (<ul>).
			 *
			 * @since  1.5.0
			 * @var Array
			 */
			$primary_menu_classes = apply_filters( 'astra_primary_menu_classes', array( 'main-header-menu', 'ast-nav-menu', 'ast-flex', 'ast-justify-content-flex-end', $submenu_class ) );

			// Fallback Menu if primary menu not set.
			$fallback_menu_args = array(
				'theme_location' => 'primary',
				'menu_id'        => 'primary-menu',
				'menu_class'     => 'main-navigation',
				'container'      => 'div',

				'before'         => '<ul class="' . esc_attr( implode( ' ', $primary_menu_classes ) ) . '">',
				'after'          => '</ul>',
				'walker'         => new Astra_Walker_Page(),
			);

			$items_wrap  = '<nav itemtype="https://schema.org/SiteNavigationElement" itemscope="itemscope" id="site-navigation" class="ast-flex-grow-1 navigation-accessibility" role="navigation" aria-label="' . esc_attr( 'Site Navigation', 'astra' ) . '">';
			$items_wrap .= '<div class="main-navigation">';
			$items_wrap .= '<ul id="%1$s" class="%2$s">%3$s</ul>';
			$items_wrap .= '</div>';
			$items_wrap .= '</nav>';

			// Primary Menu.
			$primary_menu_args = array(
				'theme_location'  => 'primary',
				'menu_id'         => 'primary-menu',
				'menu_class'      => esc_attr( implode( ' ', $primary_menu_classes ) ),
				'container'       => 'div',
				'container_class' => 'main-header-bar-navigation',
				'items_wrap'      => $items_wrap,
			);

			if ( has_nav_menu( 'primary' ) ) {
				// To add default alignment for navigation which can be added through any third party plugin.
				// Do not add any CSS from theme except header alignment.
				echo '<div class="ast-main-header-bar-alignment">';
				echo '<div class="main-header-bar-navigation custom-mobile-menu-bar">';
					wp_nav_menu(array(
						// 'menu'         => 'mobile header menu',
                        'theme_location'  => 'above_header_menu',
						'menu_class'      => esc_attr( implode( ' ', $primary_menu_classes ) ),
						'container'       => 'div',
						'items_wrap'      => $items_wrap,
					));
					echo '<div class="ast-search-menu-icon slide-search" id="ast-search-form"><div class="ast-search-icon"><a class="slide-search astra-search-icon" href="#"><span class="screen-reader-text">';
					esc_html_e( 'Search', 'astra' );
					echo '</a></div>';
					astra_get_search_form();
				echo '</div><div class="mobile-login-btn"><a href="/login">Client Login</a></div></div>';
					wp_nav_menu( $primary_menu_args );
				echo '</div>';
			} else {

				echo '<div ' . astra_attr( 'ast-main-header-bar-alignment' ) . '>';
					echo '<div class="main-header-bar-navigation">';
						echo '<nav itemtype="https://schema.org/SiteNavigationElement" itemscope="itemscope" id="site-navigation" class="ast-flex-grow-1 navigation-accessibility" role="navigation" aria-label="' . esc_attr( 'Site Navigation', 'astra' ) . '">';
							wp_page_menu( $fallback_menu_args );
						echo '</nav>';
					echo '</div>';
				echo '</div>';
			}
		}

	}
