<?php
/*
 * Template Name: Контакти
 * */

/* Get ACF*/
$fields = get_fields();

get_header();
while (have_posts()){
    the_post();
}
?>
    <div class="page_content_wrap page_paddings_no">
        <div class="content_wrap">
            <div class="content">
                <article class="post_item post_item_single post-15 page hentry">
                    <section class="post_content">
                        <div class="vc_row wpb_row vc_row-fluid">
                            <div class="wpb_column vc_column_container vc_col-sm-12">
                                <div class="vc_column-inner ">
                                    <div class="wpb_wrapper">
                                        <div class="vc_empty_space em_height_6-5">
                                            <span class="vc_empty_space_inner"></span>
                                        </div>
                                        <iframe class="sc_googlemap"
                                                src="https://maps.google.com/maps?t=m&output=embed&iwloc=near&z=10&q=7337+Trade+St%2C+San+Diego%2C+CA+92121"
                                                aria-label="7337 Trade St, San Diego, CA 92121"></iframe>
                                        <div class="vc_empty_space em_height_6-5">
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
                                        <div class="columns_wrap sc_columns columns_nofluid sc_columns_count_4">
                                            <?php foreach ($fields['contacts'] as $obj){ ?>
                                            <div class="column-1_4 sc_column_item">
                                                <div class="sc_section sc_section_block aligncenter">
                                                    <div class="sc_section_inner">
                                                        <div class="sc_section_content_wrap">
                                                            <figure class="sc_image sc_image_shape_square">
                                                                <img src="<?=$obj['image']; ?>" alt="" />
                                                            </figure>
                                                            <h5 class="sc_title sc_title_regular sc_align_center text_uppercase"><?=$obj['title']; ?></h5>
                                                            <div class="wpb_text_column wpb_content_element ">
                                                                <div class="wpb_wrapper">
                                                                    <p><?=$obj['description']; ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div class="vc_empty_space em_height_4-2">
                                            <span class="vc_empty_space_inner"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-vc-full-width="true" data-vc-full-width-init="false" class="vc_row wpb_row vc_row-fluid vc_custom_1464101475265">
                            <div class="wpb_column vc_column_container vc_col-sm-12">
                                <div class="vc_column-inner ">
                                    <div class="wpb_wrapper">
                                        <div class="vc_empty_space em_height_12">
                                            <span class="vc_empty_space_inner"></span>
                                        </div>
                                        <h1 class="sc_title sc_title_regular sc_align_center text_uppercase">Обратний зв'язок</h1>
                                        <div id="sc_form_777_wrap" class="sc_form_wrap">
                                            <div id="sc_form_777" class="sc_form sc_form_style_form_1 aligncenter">
                                                <form id="sc_form_777_form" class="sc_input_hover_default" data-formtype="form_1" method="post" action="includes/sendmail.php">
                                                    <div class="sc_form_info">
                                                        <div class="columns_wrap">
                                                            <div class="sc_form_item sc_form_field label_over column-1_2">
                                                                <input id="sc_form_username" type="text" name="username" placeholder="Name *" aria-required="true">
                                                            </div>
                                                            <div class="sc_form_item sc_form_field label_over column-1_2">
                                                                <input id="sc_form_email" type="text" name="email" placeholder="E-mail *" aria-required="true">
                                                            </div>
                                                        </div>
                                                        <div class="sc_form_item sc_form_field label_over">
                                                            <input id="sc_form_subj" type="text" name="subject" placeholder="Subject" aria-required="true"> </div>
                                                    </div>
                                                    <div class="sc_form_item sc_form_message">
                                                        <textarea id="sc_form_message" name="message" placeholder="Message" aria-required="true"></textarea>
                                                    </div>
                                                    <button class="sc_form_item sc_form_button sc_button sc_button_color_1 small sc_button_size_small">Відправити повідомлення</button>
                                                    <div class="result sc_infobox"></div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="vc_empty_space em_height_6-7">
                                            <span class="vc_empty_space_inner"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="vc_row-full-width"></div>
                    </section>
                </article>
                <section class="related_wrap related_wrap_empty"></section>
            </div>
        </div>
    </div>

<?php
get_footer("woocommerce");