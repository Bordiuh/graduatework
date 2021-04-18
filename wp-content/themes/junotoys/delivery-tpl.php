<?php
/*
    Template Name: Оплата і доставка
 */

$fields = get_fields()?:[];
$content_blocks = $fields["content_blocks"]?:[];
$delivery_methods = $fields['delivery_methods'] ?: [];
get_header();

the_content();
?>
    <div class="vc_row wpb_row vc_row-fluid">
        <div class="wpb_column vc_column_container vc_col-sm-12">
            <div class="vc_column-inner ">
                <div class="wpb_wrapper">
                    <h2 class="sc_title sc_title_regular sc_align_left"><?php echo $fields["title"] ?></h2>
                    <div class="columns_wrap sc_columns columns_nofluid sc_columns_count_2">
                        <?php foreach ($delivery_methods as $method) {
                            ?>
                            <div class="column-1_2 sc_column_item">
                                <div class="sc_dropcaps sc_dropcaps_style_1" style="margin-bottom:tiny;">
                                    <span class="sc_dropcaps_item"><?php echo $method['letter']; ?></span>
                                    <?php echo $method['content']; ?>
                                </div>
                            </div>
                            <?php
                        } ?>

                    </div>
                    <div class="vc_empty_space em_height_1">
                        <span class="vc_empty_space_inner"></span>
                    </div>
                    <div class="sc_line sc_line_position_center_center sc_line_style_solid"></div>
                    <div class="vc_empty_space em_height_3">
                        <span class="vc_empty_space_inner"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="vc_row wpb_row vc_row-fluid">
        <div class="wpb_column vc_column_container vc_col-sm-12">
            <div class="vc_column-inner ">
                <div class="wpb_wrapper">
                    <h2 class="sc_title sc_title_regular sc_align_left"><?php the_title(); ?></h2>
<?php foreach ($content_blocks as $block) {
    ?>
                    <figure class="sc_image alignleft sc_image_shape_square em_mgbot_1 widthhalf">
                        <img src="<?php echo $block["image"] ?>" alt=""/>
                    </figure>
                    <div class="wpb_text_column wpb_content_element vc_custom_1458549303665">
                        <div class="wpb_wrapper">
                           <?php echo $block["content"] ?>
                        </div>
                    </div>
    <?php }?>
                    <div class="vc_empty_space em_height_3">
                        <span class="vc_empty_space_inner"></span>
                    </div>
                    <div class="sc_line sc_line_position_center_center sc_line_style_solid"></div>
                    <div class="vc_empty_space em_height_3">
                        <span class="vc_empty_space_inner"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
get_footer('woocommerce');
