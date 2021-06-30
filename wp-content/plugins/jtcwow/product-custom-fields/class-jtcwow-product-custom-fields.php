<?php

/**
 * The product-custom-fields-facing functionality of the plugin.
 *
 * @link       http://central.tech
 * @since      1.0.0
 *
 * @package    Jtcwow
 * @subpackage Jtcwow/product-custom-fields
 */

/**
 * The product-custom-fields-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the product-custom-fields-facing stylesheet and JavaScript.
 *
 * @package    Jtcwow
 * @subpackage Jtcwow/product-custom-fields
 * @author     CTO-CNX <attawit@central.tech>
 */
class Jtcwow_Product_Custom_Fields {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
	 * Override product custom fields
	 * @since    1.0.0
	 *
	 * @return array
	 */
	public function register_custom_fields( $custom_fields )
	{
		$custom_fields = array();
		$default = array(
			'block_name' => '',
			'visibility' => '',
			'is_group' => '',
			'group_name' => '',
			'wcfm_product_custom_block_fields' => array(
				array(
					'type' => 'text',
					'label' => '',
					'name' => '',
					'options' => '',
					'content' => '',
					'help_text' => ''
				)
			)
		);

		$custom_fields[] = $default;
		$custom_fields[] = $this->field_product_size();
		$custom_fields[] = $this->field_video_url();
		// $custom_fields[] = $this->field_product_unit();
		$custom_fields[] = $this->field_product_properties();
		// $custom_fields[] = $this->field_product_certificate();
		// $custom_fields[] = $this->field_emoticon();

		return $custom_fields;
	}

    private function field_product_unit()
	{
		return array(
			'enable' => 'yes',
			'block_name' => 'Unit',
			'visibility' => '',
			'is_group' => 'yes',
			'group_name' => '_custom_product_unit',
			'wcfm_product_custom_block_fields' => array(
				array(
					'type' => 'select',
					'label' => 'Weight',
					'name' => '_custom_weight_unit',
					'options' => '|cts|g|kg|ton',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Size',
					'name' => '_custom_size_unit',
					'options' => '|mm|cm|in|ft|m',
					'content' => '',
					'help_text' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Quantity',
					'name' => '_custom_quantity_unit',
					'options' => 'pcs|lot|pair|set|strand|person|packs|sq m|room|hour|day|week|month|year',
					'content' => '',
					'help_text' => ''
				)
			)
		);
	}

    private function field_video_url()
	{
		return array(
			'enable'                                => 'yes',
			'block_name'                            => 'Video URL',
			'visibility'                            => '',
			'group_name'                            => '_video_url',
			'wcfm_product_custom_block_fields'      => array(
				array(
					'type'          => 'text',
					'label'         => 'Youtube',
					'name'          => '_video_url',
					'options'       => '',
					'content'       => '',
					'help_text'     => ''
				)
			)
		);
	}

    private function field_product_properties()
	{
		return array(
			'enable' => 'yes',
			'block_name' => 'Product detail',
			'visibility' => '',
			'is_group' => '',
			'group_name' => '_properties',
			'wcfm_product_custom_block_fields' => array(
				array(
					'type' => 'select',
					'label' => 'Shape',
					'name' => '_prop_shape',
					'options' => '|Baguette|Bead|Buddha|Butterfly|Cushion|Dragon|Drop|Elephant|Emerald|Fancy|Flower|Happy Buddha|Heart|Hexagon|Hoop|Horse|Irregular|Marquise|Octagon|Oval|Pear|Rectangular|Rough|Rough Crystal|Round|Semi-Oval|Shell|Sphere|Square|Tiger|Triangle|Triangular Fancy|Trillion|Twist',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Cutting Style',
					'name' => '_prop_cutting_style',
					'options' => '|Bangle|Brilliant|Briolette|Buff-top|Cabochon|Cameo|Carving|Checkerboard|Faceted|Hollow Cabochon|Intaglio|Mixed|Polished|Preformed|Pseudo-crystal|Scissor|Slab|Sugarloaf',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Colour Grade',
					'name' => '_prop_colour_grade',
					'options' => '|Rose red|Tamarind|Raspberry|Strawberry|Crimson|Traffic Light red|Rose pink|Violet red|Lotus pink (pink with orange)|Hot pink|Mandarin orange|Sunkish orange|Mekong Whisky Yellow|Light Yellow|Greenish Yellow|Brownish Yellow|Honey Yellow|Ocean Blue|Midnight Blue|Royal Blue|Sky Blue|Violetish Blue|Greenish blue|Cornflower Blue|Dark Velvet Blue|Velvet Blue|Neon Blue|Steel Blue|Tiffany Blue|Lettuce green|Apple green|Mango green|Lemon green|Imperial green|Moss green|Olive green|Neon green|Teal green',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'text',
					'label' => 'Other Colour Grade',
					'name' => '_prop_other_colour_grade',
					'options' => '',
					'content' => '',
					'help_text' => 'Please leave blank above field if you enter this field',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Hue',
					'name' => '_prop_hue',
					'options' => '|Type A - Red 10%|Type A- Red 35%|Tyoe A - Red 50%|Type A- Red 75%|Type A- Red 90%|Type B - Purplish or Pinkish red 10%|Type B - Purplish or Pinkish red 35%|Type B - Purplish or Pinkish red 50%|Type B - Purplish or Pinkish red 75%|Type B - Purplish or Pinkish red 90%|Type C - Orange red 10%|Type C - Orange red 35%|Type C - Orange red 50%|Type C - Orange red 75%|Type C - Orange red 90%|Type D - Deep red 10%|Type D - Deep red 35%|Type D - Deep red 50%|Type D - Deep Red 75%|Type D - Deep red 90%|Type E - Pink 10%|Type E - Pink 35%|Type E - Pink 50%|Type E - Red 75%|Type E - Pink 90%|Type A - Violet-blue 10%|Type A- Violet-blue 35%|Type A - Violet-blue  50%|Type A- Violet-blue 75%|Type A - Violet-blue 90%|Type B - Milky blue 10%|Type B - Milky blue 35%|Type B - Milky Blue 50%|Type B - Milky blue 75%|Type B - Milky blue 90%|Type C - Blue 10%|Type C - Blue 35%|Type C - Blue 50%|Type C - Blue 75%|Type C - Blue 90%|Type D - Inky blue 10%|Type D - Inky blue 35%|Type D - Inky blue 50%|Type D - Inky blue 75%|Type D - Inky blue 90%',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Saturation',
					'name' => '_prop_saturation',
					'options' => '|Fair Vivid|Medium Vivid|Vivid|Vibrant Vivid',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'FSCR',
					'name' => '_prop_fscr',
					'options' => '|FF|F|FS|S|SC|C|CR|R|RR',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Polish',
					'name' => '_prop_polish',
					'options' => '|Excellent|Very Good – Excellent|Very Good|Good – Very Good|Good|Fair – Good|Fair|Poor – Fair|Poor',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Rarity',
					'name' => '_prop_rarity',
					'options' => '|Rare(R)|Very Rare(RR)|Extremely Rare(RRR)',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Culet',
					'name' => '_prop_culet',
					'options' => '|Pointed|Faceted|Broken|Abraded|Pitted',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Fluorescence',
					'name' => '_prop_fluorescence',
					'options' => '|None|Faint|Slight|Medium|Strong|Extreme',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Cut Proportions',
					'name' => '_prop_cut_proportions',
					'options' => '|Excellent|Very Good – Excellent|Very Good|Good – Very Good|Good|Fair – Good|Fair|Poor – Fair|Poor',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Zone Name',
					'name' => '_prop_zone_name',
					'options' => '|Gemwow 3|Gemwow 7|Gemwow 11|Gemwow 13|Gemwow 16|Gemwow 18|Gemwow 22|Gemwow 24|Ebay|EGA|AZG|JDA|IDP|FCC|GWB|SGA|AGM|MSS|CDA|PLB|KRA|GSA|WSA|ACA|DCFA|2nd floor|A 1|A 2|A 7|A 10|AIGS gem chart|GAP|Safe|MYC|MCG|OCG|Jewelry|Cabinet|KEEP NOT FOR SALE|D|Box A|Box B|Box C|Box D|Box E|Box F|Box G|Box H|Box Carving|CGA|LAY|RLK|RSA|Tray',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Clarity Icons',
					'name' => '_prop_clarity_icons',
					'options' => '|LI1|LI2|MI 1|MI 2|VI 1|VI 2|HI',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Enhancement Icons',
					'name' => '_prop_enhancement_icons',
					'options' => '|Bleaching|Coating|Dyeing|Filling|Laser|Irradiation|Diffusion|Routinely enchanced|Heat & Pressure|Indication of heating|Impregnating|No indication of heating|Oil|Resin|Wax',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Gem Type',
					'name' => '_prop_gem_type',
					'options' => '|Ruby – A|Ruby – B|Ruby – C|Ruby – D|Ruby – E|Sapphire – A|Sapphire – B|Sapphire – C|Sapphire – D|Sapphire – F|Sapphire – G|Sapphire – H|Sapphire – K',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Gem Girdle',
					'name' => '_prop_gem_girdle',
					'options' => '|Extremely Thin|Very Thin|Thin|Medium|Slightly Thick|Thick|Very Thick|Extremely Thick|Faceted|Smooth|Polished|Very Thin – Thin|Very Thin – Medium|Very Thin – Thick|Very Thin – Very Thick|Thin – Very Thin|Thin – Medium|Thin – Thick|Thin – Very Thick|Medium – Very Thin|Medium – Thin|Medium – Thick|Medium – Very Thick|Thick – Very Thin|Thick – Thin|Thick – Medium|Thick – Thick|Thick – Very Thick|Very Thick – Very Thin|Very Thick – Thin|Very Thick – Medium|Very Thick – Thick',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Transparency',
					'name' => '_prop_transparency',
					'options' => '|Transparent|Semi–Transparent|Translucent|Semi–Translucent|Opaque',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Tone',
					'name' => '_prop_tone',
					'options' => '|Very Light - 10%|Light -35%|Medium - 50%|Dark - 75%|Very Dark - 90%',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Inclusion Icons',
					'name' => '_prop_inclusion_icons',
					'options' => '|2 Phase|3 Phase|Angular Color Banding|Boehmite Needle|Cloud|Crystal|Curved Color Banding|Doubling|Fingerprint|Straight color banding|Rutile Needle|Silk|Octahedron Crystal|Tension Disc|Snow Ball|Abrasion|Hexagonal Color Banding|Halo|Minute',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Symmetry',
					'name' => '_prop_symmetry',
					'options' => '|Poor Brilliancy|Fair Brilliancy|Good Brilliancy|Very good Brilliancy|Excellent Brilliancy|Poor Finish|Fair Finish|Good Finish|Very Good Finish|Excellent Finish|Poor Symmetry|Fair Symmetry|Good Symmetry|Very good Symmetry|Excellent Symmetry|Wavy Girdle|Very Thin Girdle|Thin Girdle|Medium Girdle|Thick Girdle|Very thick Girdle|No Pavilion|Slight Pavillion Bulge|Moderate Pavillion Bulge|Large Pavillion Bulge|Mis-Shapen Facet|Off Center Culet|Thick Crown|High Crown Angles|Heavy Bottom|Flat Bottom|Lop Sided Pavilion|Flat Crown|Flatish Crown|Large face|Small Window|Medium Window|Big Window',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Star',
					'name' => '_prop_star',
					'options' => '|1|2|3|4|5',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
				array(
					'type' => 'select',
					'label' => 'Smiley',
					'name' => '_prop_smiley',
					'options' => '|1|2|3|4|5|6|7|8|9|10',
					'content' => '',
					'help_text' => '',
					'required' => ''
                ),
                array(
					'type' => 'select',
					'label' => 'Origin',
					'name' => '_prop_origin',
					'options' => '|Africa|Burmar|Srilanka|Vietnam|Thai|Others',
					'content' => '',
					'help_text' => '',
					'required' => ''
                ),
				array(
					'type' => 'text',
					'label' => 'Origin Others',
					'name' => '_prop_origin_others',
					'content' => '',
					'help_text' => 'Fill custom origin if you select others origin.',
					'required' => ''
                ),
				array(
					'type' => 'text',
					'label' => 'Zoning',
					'name' => '_prop_zoning',
					'content' => '',
					'help_text' => '',
					'required' => ''
                ),
                array(
					'type'          => 'checkbox',
					'label'         => 'Available Guarantee',
					'name'          => '_available_guarantee',
					'options'       => '',
					'content'       => '',
					'help_text'     => ''
				),
				array(
					'type'          => 'checkbox',
					'label'         => 'QAA',
					'name'          => '_qaa',
					'options'       => '',
					'content'       => '',
					'help_text'     => ''
				),
				array(
					'type'          => 'checkbox',
					'label'         => 'MGR Report',
					'name'          => '_mgr_report',
					'options'       => '',
					'content'       => '',
					'help_text'     => ''
				),
				array(
					'type'          => 'upload',
					'label'         => 'Certificate file',
					'name'          => '_certificate_file',
					'options'       => '',
					'content'       => '',
					'help_text'     => ''
				),
			)
		);
	}

    private function field_product_size()
	{
		return array(
			'enable' => 'yes',
			'block_name' => 'Product Size',
			'visibility' => '',
			'is_group' => 'yes',
			'group_name' => '_product_size',
			'wcfm_product_custom_block_fields' => array(
                array(
					'type' => 'text',
					'label' => 'Length' . ' (' . get_option('woocommerce_dimension_unit') . ')',
					'name' => '_prod_length',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
                array(
					'type' => 'text',
					'label' => 'Width' . ' (' . get_option('woocommerce_dimension_unit') . ')',
					'name' => '_prod_width',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
                array(
					'type' => 'text',
					'label' => 'Height' . ' (' . get_option('woocommerce_dimension_unit') . ')',
					'name' => '_prod_height',
					'content' => '',
					'help_text' => '',
					'required' => ''
				),
                array(
					'type' => 'text',
					'label' => 'Weight' . ' (' . get_option('woocommerce_weight_unit') . ')',
					'name' => '_prod_weight',
					'content' => '',
					'help_text' => '',
					'required' => ''
				)
			)
		);
	}

    private function field_product_certificate()
	{
		return array(
			'enable' => 'yes',
			'block_name' => 'Certificate',
			'visibility' => '',
			'is_group' => '',
			'group_name' => '_certificate',
			'wcfm_product_custom_block_fields' => array(
                array(
					'type'          => 'upload',
					'label'         => 'Upload certificate file',
					'name'          => '_certificate_file',
					'options'       => '',
					'content'       => '',
					'help_text'     => ''
				),
			)
		);
	}

    private function field_emoticon()
	{
		return array(
			'enable' => 'yes',
			'block_name' => 'Emoticon',
			'visibility' => 'jctwow_with_summery',
			'is_group' => 'yes',
			'group_name' => '_emoticon',
			'wcfm_product_custom_block_fields' => array(
				array(
					'type' => 'checkbox',
					'label' => '<img src="/wp-content/themes/urna-child/images/jtc/emoticons/superlike.png">',
					'name' => 'superlike',
					'options' => '',
					'content' => '',
					'help_text'
				),
				array(
					'type' => 'checkbox',
					'label' => '<img src="/wp-content/themes/urna-child/images/jtc/emoticons/unlike.png">',
					'name' => 'unlike',
					'options' => '',
					'content' => '',
					'help_text'
				),
				array(
					'type' => 'checkbox',
					'label' => '<img src="/wp-content/themes/urna-child/images/jtc/emoticons/sunglasses_man(50x50).png">',
					'name' => 'sunglasses_man',
					'options' => '',
					'content' => '',
					'help_text'
				),
				array(
					'type' => 'checkbox',
					'label' => '<img src="/wp-content/themes/urna-child/images/jtc/emoticons/sale-icon.png">',
					'name' => 'sale-icon',
					'options' => '',
					'content' => '',
					'help_text'
				),
				array(
					'type' => 'checkbox',
					'label' => '<img src="/wp-content/themes/urna-child/images/jtc/emoticons/limitedoffer.png">',
					'name' => 'limitedoffer',
					'options' => '',
					'content' => '',
					'help_text'
				),
				array(
					'type' => 'checkbox',
					'label' => '<img src="/wp-content/themes/urna-child/images/jtc/emoticons/LIKE(50x50x).png">',
					'name' => 'like',
					'options' => '',
					'content' => '',
					'help_text'
				),
				array(
					'type' => 'checkbox',
					'label' => '<img src="/wp-content/themes/urna-child/images/jtc/emoticons/Gold_Ribbon(50x50).png">',
					'name' => 'gold_ribbon',
					'options' => '',
					'content' => '',
					'help_text'
				),
				array(
					'type' => 'checkbox',
					'label' => '<img src="/wp-content/themes/urna-child/images/jtc/emoticons/crown.png">',
					'name' => 'crown',
					'options' => '',
					'content' => '',
					'help_text'
				),
				array(
					'type' => 'checkbox',
					'label' => '<img src="/wp-content/themes/urna-child/images/jtc/emoticons/bestvalue.png">',
					'name' => 'bestvalue',
					'options' => '',
					'content' => '',
					'help_text'
				),
				array(
					'type' => 'checkbox',
					'label' => '<img src="/wp-content/themes/urna-child/images/jtc/emoticons/bestprice.png">',
					'name' => 'bestprice',
					'options' => '',
					'content' => '',
					'help_text'
				)
			)
		);
	}

    public function remove_filter_product_custom_fields()
	{
		remove_filter( 'option_wcfm_product_custom_fields', array( $this, 'register_custom_fields' ) );
	}

    public function override_woocommerce_format_weight( $weight_string, $weight )
	{
		global $product;
		if ( $product instanceof WC_Product ) {
			$weight_unit = get_post_meta( $product->get_id(), '_weight_unit', true );
			if ( $weight_unit ) {
				$weight_string = wc_format_localized_decimal( $weight );

				if ( ! empty( $weight_string ) ) {
					$weight_string .= ' ' . $weight_unit;
				} else {
					$weight_string = __( 'N/A', 'woocommerce' );
				}
			}
		}
		return $weight_string;
	}

	public function override_woocommerce_format_dimensions( $dimension_string, $dimensions )
	{
		global $product;
		if ( $product instanceof WC_Product ) {
			$size_unit = get_post_meta( $product->get_id(), '_size_unit', true );
			if ( $size_unit ) {
				$dimension_string = implode( ' &times; ', array_filter( array_map( 'wc_format_localized_decimal', $dimensions ) ) );

				if ( ! empty( $dimension_string ) ) {
					$dimension_string .= ' ' . $size_unit;
				} else {
					$dimension_string = __( 'N/A', 'woocommerce' );
				}
			}
		}
		return $dimension_string;
	}

    public function icon_single_product_summary()
	{
		global $WCFM, $product, $post;
		$product_id = 0;
		if ( is_object( $product ) && method_exists( $product, 'get_id' ) ) {
			$product_id   		= $product->get_id();
		} else if ( is_product() ) {
			$product_id   		= $post->ID;
		}

		if( $product_id ) {
			$wcfm_product_custom_fields = (array) get_option( 'wcfm_product_custom_fields' );
			if( $wcfm_product_custom_fields && is_array( $wcfm_product_custom_fields ) && !empty( $wcfm_product_custom_fields ) ) {
				foreach( $wcfm_product_custom_fields as $wpcf_index => $wcfm_product_custom_field ) {
					if( !isset( $wcfm_product_custom_field['enable'] ) ) continue;

					$visibility = isset( $wcfm_product_custom_field['visibility'] ) ? $wcfm_product_custom_field['visibility'] : '';
					if( !$visibility ) continue;
					if( $visibility != 'jctwow_with_summery' ) continue;

					$display_data = $this->wcfm_custom_field_display_data( $product_id, $wcfm_product_custom_field );
					echo $display_data;
				}
			}
		}
	}

    public function wcfm_custom_field_display_data( $product_id, $wcfm_product_custom_field ) {
        global $WCFM, $product, $post;

        $display_data = '';
        $block_name = !empty( $wcfm_product_custom_field['block_name'] ) ? $wcfm_product_custom_field['block_name'] : '';
        if( !$block_name ) return '';
        $exclude_product_types = isset( $wcfm_product_custom_field['exclude_product_types'] ) ? $wcfm_product_custom_field['exclude_product_types'] : array();
        $is_group = !empty( $wcfm_product_custom_field['is_group'] ) ? 'yes' : 'no';
        $is_group = !empty( $wcfm_product_custom_field['group_name'] ) ? $is_group : 'no';
        $group_name = !empty( $wcfm_product_custom_field['group_name'] ) ? $wcfm_product_custom_field['group_name'] : '';
        $group_value = array();
        if( $product_id && $is_group && $group_name ) {
            $group_value = (array) get_post_meta( $product_id, $group_name, true );
            $group_value = apply_filters( 'wcfm_custom_field_group_data_value', $group_value, $group_name );
        }

        $product = wc_get_product( $product_id );
        $product_type = $product->get_type();

        $is_virtual = ( get_post_meta( $product_id, '_virtual', true) == 'yes' ) ? 'enable' : '';
        if( $is_virtual && in_array( 'virtual', $exclude_product_types ) ) return '';

        $wcfm_product_custom_block_fields = $wcfm_product_custom_field['wcfm_product_custom_block_fields'];
        if( !empty( $wcfm_product_custom_block_fields ) && !in_array( $product_type, $exclude_product_types ) ) {

            $vendor_id = wcfm_get_vendor_id_by_post( $product_id );
            if( $vendor_id ) {
                if( !$WCFM->wcfm_vendor_support->wcfm_vendor_allowed_element_capability( $vendor_id, 'allowed_custom_fields', sanitize_title($block_name) ) ) return;
            }

            $display_data .= '<div class="wcfm_custom_field_display wcfm_custom_field_display_'.sanitize_title($block_name).'">';

            if( apply_filters( 'wcfm_is_allow_custom_field_display_as_table', false ) ) { $display_data .= "<table class='wcfm_custom_field_display_table'><tr>"; }
            foreach( $wcfm_product_custom_block_fields as $wcfm_product_custom_block_field ) {
                if( !$wcfm_product_custom_block_field['name'] ) continue;
                $field_value = '';
                $field_name = $wcfm_product_custom_block_field['name'];
                if( $is_group == 'yes' ) {
                    $field_name = $group_name . '[' . $wcfm_product_custom_block_field['name'] . ']';
                    if( $product_id ) {
                        if( $wcfm_product_custom_block_field['type'] == 'checkbox' ) {
                            $field_value = isset( $group_value[$wcfm_product_custom_block_field['name']] ) ? 'yes' : 'no';
                        } elseif( $wcfm_product_custom_block_field['type'] == 'upload' ) {
                            if( isset( $group_value[$wcfm_product_custom_block_field['name']] ) ) {
                                $field_value = '<a class="wcfm_linked_images" href="' . wcfm_get_attachment_url( $group_value[$wcfm_product_custom_block_field['name']] ) . '" target="_blank">' . wcfm_removeslashes( __( $wcfm_product_custom_block_field['label'], 'wc-frontend-manager') ) . '</a>';
                            }
                        } else {
                            if( isset( $group_value[$wcfm_product_custom_block_field['name']] )) {
                                $field_value = $group_value[$wcfm_product_custom_block_field['name']];
                            }
                        }
                    }
                } else {
                    if( $product_id ) {
                        if( $wcfm_product_custom_block_field['type'] == 'checkbox' ) {
                            $field_value = get_post_meta( $product_id, $field_name, true ) ? get_post_meta( $product_id, $field_name, true ) : 'no';
                        } elseif( $wcfm_product_custom_block_field['type'] == 'upload' ) {
                            if( get_post_meta( $product_id, $field_name, true ) ) {
                                $field_value = '<a class="wcfm_linked_images" href="' . wcfm_get_attachment_url( get_post_meta( $product_id, $field_name, true ) ) . '" target="_blank">' . wcfm_removeslashes( __( $wcfm_product_custom_block_field['label'], 'wc-frontend-manager') ) . '</a>';
                            }
                        } else {
                            $field_value = get_post_meta( $product_id, $field_name, true );
                        }
                    }
                }

                $field_value =  apply_filters( 'wcfm_custom_field_value', $field_value, $field_name, $product_id, $wcfm_product_custom_block_field['type'], $wcfm_product_custom_block_field );

                if( ( $wcfm_product_custom_block_field['type'] == 'checkbox' ) && apply_filters( 'wcfm_is_allow_custom_field_display_by_icon', true ) ) {
                    if( $field_value == 'no' ) {
                        continue;
                    }
                }

                if( $wcfm_product_custom_block_field['type'] == 'textarea' ) {
                    $field_value = wcfm_stripe_newline( $field_value );
                }

                if( !$field_value ) continue;

                if( is_array( $field_value ) ) $field_value = implode( ', ', $field_value );

                if( apply_filters( 'wcfm_is_allow_custom_field_display_as_table', false ) ) { $display_data .= "<td>"; }
                if( ( $wcfm_product_custom_block_field['type'] != 'upload' ) && apply_filters( 'wcfm_is_allow_custom_field_label_display', true ) ) {
                  $display_data .= "<label class='wcfm_custom_field_display_label' style='margin-right: 10px; max-width: 50px !important;'>" . wcfm_removeslashes( __( $wcfm_product_custom_block_field['label'], 'wc-frontend-manager') ) . "</label>";
                }
                if( apply_filters( 'wcfm_is_allow_custom_field_display_as_table', false ) ) { $display_data .= "<br />"; }
                // $display_data .= "<span class='wcfm_custom_field_display_value'>" . $field_value . "</span><br />";
                if( apply_filters( 'wcfm_is_allow_custom_field_display_as_table', false ) ) { $display_data .= "</td>"; }
            }
            if( apply_filters( 'wcfm_is_allow_custom_field_display_as_table', false ) ) { $display_data .= "</tr></table>"; }
            $display_data .= '</div><div class="wcfm-clearfix"></div><br />';
        }
        return $display_data;
    }

    public function custom_product_tabs( $tabs )
    {
        $tabs['product_detail'] = array(
            'title'     => 'Product Detail',
            'priority'  => 11,
            'callback'  => array( $this, 'tab_content_product_detail' ),
        );

        return $tabs;
    }

    public function tab_content_product_detail()
    {
        global $product;

		$wcfm_product_custom_fields = get_option( 'wcfm_product_custom_fields', array() );
		$wcfm_product_custom_field_properties = null;

		foreach( $wcfm_product_custom_fields as $key => $value ) {
			if ( $value['group_name'] == '_properties' )
				$wcfm_product_custom_field_properties = $value;
		}

		$wcfm_product_custom_field_properties_data = $this->get_wcfm_custom_field_display_data( $product->get_id(), $wcfm_product_custom_field_properties );

		foreach( $wcfm_product_custom_field_properties_data as $value ) {
			$product_attributes[] = array(
				'label' => $value['label'],
				'value' => $value['value'],
			);
		}

		$product_size = $product->get_meta( '_product_size' );

		if ( ! empty( $product_size ) ) {
			if ( isset( $product_size['_prod_weight'] ) && ! empty( $product_size['_prod_weight'] ) ) {
				$product_attributes[] = array(
					'label' => '<strong>' . __( 'Approx. Weight', 'jtcwow' ) . '</strong>',
					'value' => $product_size['_prod_weight'],
				);
			}

			if ( isset( $product_size['_prod_length'] )
				&& ! empty( $product_size['_prod_length'] )
				&& isset( $product_size['_prod_width'] )
				&& ! empty( $product_size['_prod_width'] )
				&& isset( $product_size['_prod_height'] )
				&& ! empty( $product_size['_prod_height'] ) ) {

					$product_attributes[] = array(
						'label' => '<strong>' . __( 'Approx. Dimensions', 'jtcwow' ) . '</strong>',
						'value' => "{$product_size['_prod_width']} x {$product_size['_prod_length']} x {$product_size['_prod_height']}",
					);

					$depth_percentage = ( ( $product_size['_prod_height'] / $product_size['_prod_width'] ) ) * 100;
					$product_attributes[] = array(
						'label' => '<strong>' . __( 'Depth Percentage', 'jtcwow' ) . '</strong>',
						'value' => number_format( $depth_percentage, 2, '.', ',') . " %",
					);
			}
		}

        wc_get_template(
            'single-product/product-attributes.php',
            array(
                'product_attributes' => $product_attributes,
            )
        );
    }

	function get_wcfm_custom_field_display_data( $product_id, $wcfm_product_custom_field ) {
		global $WCFM, $product, $post;

		$display_data = array();
		$block_name = !empty( $wcfm_product_custom_field['block_name'] ) ? $wcfm_product_custom_field['block_name'] : '';
		if( !$block_name ) return '';
		$exclude_product_types = isset( $wcfm_product_custom_field['exclude_product_types'] ) ? $wcfm_product_custom_field['exclude_product_types'] : array();
		$is_group = !empty( $wcfm_product_custom_field['is_group'] ) ? 'yes' : 'no';
		$is_group = !empty( $wcfm_product_custom_field['group_name'] ) ? $is_group : 'no';
		$group_name = !empty( $wcfm_product_custom_field['group_name'] ) ? $wcfm_product_custom_field['group_name'] : '';
		$group_value = array();
		if( $product_id && $is_group && $group_name ) {
			$group_value = (array) get_post_meta( $product_id, $group_name, true );
			$group_value = apply_filters( 'wcfm_custom_field_group_data_value', $group_value, $group_name );
		}

		$product = wc_get_product( $product_id );
		$product_type = $product->get_type();

		$is_virtual = ( get_post_meta( $product_id, '_virtual', true) == 'yes' ) ? 'enable' : '';
		if( $is_virtual && in_array( 'virtual', $exclude_product_types ) ) return '';

		$wcfm_product_custom_block_fields = $wcfm_product_custom_field['wcfm_product_custom_block_fields'];
		if( !empty( $wcfm_product_custom_block_fields ) && !in_array( $product_type, $exclude_product_types ) ) {

			$vendor_id = wcfm_get_vendor_id_by_post( $product_id );
			if( $vendor_id ) {
				if( !$WCFM->wcfm_vendor_support->wcfm_vendor_allowed_element_capability( $vendor_id, 'allowed_custom_fields', sanitize_title($block_name) ) ) return;
			}

			foreach( $wcfm_product_custom_block_fields as $wcfm_product_custom_block_field ) {
				if( !$wcfm_product_custom_block_field['name'] ) continue;
				$field_value = '';
				$field_name = $wcfm_product_custom_block_field['name'];
				if( $is_group == 'yes' ) {
					$field_name = $group_name . '[' . $wcfm_product_custom_block_field['name'] . ']';
					if( $product_id ) {
						if( $wcfm_product_custom_block_field['type'] == 'checkbox' ) {
							$field_value = isset( $group_value[$wcfm_product_custom_block_field['name']] ) ? 'yes' : 'no';
						} elseif( $wcfm_product_custom_block_field['type'] == 'upload' ) {
							if( isset( $group_value[$wcfm_product_custom_block_field['name']] ) ) {
								$field_value = '<a class="wcfm_linked_images" href="' . wcfm_get_attachment_url( $group_value[$wcfm_product_custom_block_field['name']] ) . '" target="_blank"><span class="wcfmfa fa-eye" style="color:#20c997"></span> ' . wcfm_removeslashes( __( 'View', 'wc-frontend-manager') ) . '</a>';
							}
						} else {
							if( isset( $group_value[$wcfm_product_custom_block_field['name']] )) {
								$field_value = $group_value[$wcfm_product_custom_block_field['name']];
							}
						}
					}
				} else {
					if( $product_id ) {
						if( $wcfm_product_custom_block_field['type'] == 'checkbox' ) {
							$field_value = get_post_meta( $product_id, $field_name, true ) ? get_post_meta( $product_id, $field_name, true ) : 'no';
						} elseif( $wcfm_product_custom_block_field['type'] == 'upload' ) {
							if( get_post_meta( $product_id, $field_name, true ) ) {
								$field_value = '<a class="wcfm_linked_images" href="' . wcfm_get_attachment_url( get_post_meta( $product_id, $field_name, true ) ) . '" target="_blank"><span class="wcfmfa fa-eye" style="color:#20c997"></span> ' . wcfm_removeslashes( __( 'View', 'wc-frontend-manager') ) . '</a>';
							}
						} else {
							$field_value = get_post_meta( $product_id, $field_name, true );
						}
					}
				}

				$field_value =  apply_filters( 'wcfm_custom_field_value', $field_value, $field_name, $product_id, $wcfm_product_custom_block_field['type'], $wcfm_product_custom_block_field );

				if( ( $wcfm_product_custom_block_field['type'] == 'checkbox' ) && apply_filters( 'wcfm_is_allow_custom_field_display_by_icon', true ) ) {
					if( $field_value == 'no' ) {
						$field_value = '<span class="wcfmfa fa-times-circle" style="color:#f86c6b"></span>';
					} else {
						$field_value = '<span class="wcfmfa fa-check-circle" style="color:#20c997"></span>';
					}
				}

				if( $wcfm_product_custom_block_field['type'] == 'textarea' ) {
					$field_value = wcfm_stripe_newline( $field_value );
				}

				if( !$field_value ) continue;

				$display_data[] = array(
					'label' => '<strong>' . wcfm_removeslashes( __( $wcfm_product_custom_block_field['label'], 'wc-frontend-manager') ) . '</strong>',
					'value' => $field_value
				);
			}
		}

		return $display_data;
	}

	/**
	 * Show WCFM products additional data
	 *
	 * @param boolean $hidden
	 * @return boolean
	 */
	public function show_wcfm_products_additonal_data( $hidden )
	{
		$hidden = false;
		return $hidden;
	}

	public function wcfm_products_additonal_data( $data, $product_id )
	{
		$data = '';
		$product = wc_get_product( $product_id );
		$cost = $product->get_meta('_alg_wc_cog_cost');

		if ( ! empty( $cost ) )
			$data .= "<p><strong>Cost</strong>: {$cost}</p>";

		$product_size = $product->get_meta( '_product_size' );

		if ( ! empty( $product_size) && isset( $product_size['_prod_weight'] ) && ! empty( $product_size['_prod_weight'] ) )
			$data .= "<p><strong>Weight</strong>: {$product_size['_prod_weight']}</p>";

		if ( empty( $data ) )
			$data = '&ndash;';

		return $data;
	}

	public function wcfm_delete_empty_product_custom_field( $new_product_id, $field_name, $field_type, $field_value )
	{
		if ( empty( $field_value ) )
			delete_post_meta( $new_product_id, $field_name );
	}

	public function disallow_policy_product_settings( $allow )
	{
		if ( ! current_user_can( 'manage_options' ) )
			$allow = false;

		return $allow;
	}

	public function override_wcfm_product_policy_tab_title( $title, $product_id )
	{
		$title = __( 'Store policy', 'jtcwow' );

		return $title;
	}

	public function convert_wcfm_text( $translation, $text, $domain )
	{
		if ( $text == 'Taxonomies' && $domain == 'wc-frontend-manager' )
			$translation = __( 'Categories', 'wc-frontend-manager' );

		return $translation;
	}

	public function convert_wc_text( $translation, $text, $domain )
	{
		if ( $domain != 'woocommerce' )
			return $translation;

		switch( $text ) {
			case 'Catalog visibility:':
				$translation = __( 'Product visibility:', 'jtcwow' );
				break;
			case 'A note has been added to your order':
				$translation = __( 'Status update', 'jtcwow' );
				break;
		}

		return $translation;

	}

	/**
	 * Hide WCFM Product policies tab label
	 *
	 * @param [type] $array
	 * @param [type] $product_id
	 * @return void
	 */
	public function hide_wcfm_product_policies_tab_label( $array, $product_id )
	{
		if ( isset( $array['wcfm_policy_tab_title']) ) {
			$array['wcfm_policy_tab_title'] = array('label' => __('Policy Tab Label', 'wc-frontend-manager') , 'type' => 'hidden', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => 'Store policy' );
		}

		return $array;
	}

}
