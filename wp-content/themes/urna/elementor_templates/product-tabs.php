<?php 
/**
 * Templates Name: Elementor
 * Widget: Products Tabs
 */
$style = $category = '';
extract( $settings );

if( !empty($_css_classes) ) {  
    $this->add_render_attribute('wrapper', 'class', $_css_classes);
}

$this->settings_layout();

$_id = urna_tbay_random_key();

if( is_array($categories) && count($categories) === 1 ) {
    $category = $categories[0];
} else if( !is_array($categories) && !empty($categories) ) {
    $category = $categories;
}

$this->add_render_attribute('wrapper', 'class', ['tbay-addon-products', 'products', 'tbay-addon-'. $layout_type] );
?>

<div <?php echo trim($this->get_render_attribute_string('wrapper')); ?>>
    
    <div class="tabs-container tab-heading <?php echo ( isset($heading_title) && $heading_title ) ? 'has-title' : ''; ?>">

        <?php $this->render_element_heading(); ?>

        <ul class="tabs-list nav nav-tabs">
            <?php $__count = 0;?>
            <?php foreach ($list_product_tabs as $key) {
                $active = ($__count==0)? 'active':'';

                $product_tabs = $key['product_tabs'];
                $title = $this->get_title_product_type($product_tabs);
                if(!empty($key['product_tabs_title']) ) {
                    $title = $key['product_tabs_title'];
                }
                $this->render_product_tabs($product_tabs,$_id,$title,$active); 
                $__count++;   
            }
            ?>
        </ul>
    </div>

    
    <div class="tbay-addon-content tab-content woocommerce">
        <?php $__count = 0;?>
        <?php foreach ($list_product_tabs as $key) {
            $tab_active = ($__count==0)? 'active':'';
            $product_tabs = $key['product_tabs'];
                $this->render_content_tab($product_tabs,$tab_active,$_id); 
            $__count++;   
        }
        ?>
    </div>

    <?php $this->render_item_button($category); ?>

</div>