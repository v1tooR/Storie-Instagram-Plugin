<?php
/**
 * Plugin Name: Carousel Vídeos Final
 * Description: Carrossel com autoplay e layout customizado para vídeos com info de produto.
 * Version: 2.0.0
 * Author: Voia Agency
 */

if (!defined('ABSPATH')) exit;

function cvp_register_cpt() {
    register_post_type('carousel_video', array(
        'labels' => array(
            'name' => 'Vídeos do Carrossel',
            'singular_name' => 'Vídeo do Carrossel',
            'add_new' => 'Adicionar Vídeo',
            'add_new_item' => 'Adicionar Novo Vídeo',
        ),
        'public' => true,
        'has_archive' => false,
        'supports' => array('title', 'thumbnail'),
        'menu_icon' => 'dashicons-format-video',
    ));
}
add_action('init', 'cvp_register_cpt');

function cvp_register_acf_fields() {
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_carousel_video',
            'title' => 'Campos do Vídeo',
            'fields' => array(
                array(
                    'key' => 'field_video_file',
                    'label' => 'Arquivo de Vídeo',
                    'name' => 'video_file',
                    'type' => 'file',
                    'return_format' => 'url',
                ),
                array(
                    'key' => 'field_produto_img',
                    'label' => 'Imagem do Produto',
                    'name' => 'produto_img',
                    'type' => 'image',
                    'return_format' => 'url',
                ),
                array(
                    'key' => 'field_preco',
                    'label' => 'Preço',
                    'name' => 'preco',
                    'type' => 'text',
                )
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'carousel_video',
                    )
                )
            )
        ));
    }
}
add_action('acf/init', 'cvp_register_acf_fields');

function cvp_enqueue_scripts() {
    wp_enqueue_style('swiper-css', 'https://unpkg.com/swiper/swiper-bundle.min.css');
    wp_enqueue_script('swiper-js', 'https://unpkg.com/swiper/swiper-bundle.min.js', array(), null, true);
    wp_enqueue_script('cvp-js', plugin_dir_url(__FILE__) . 'js/cvp.js', array('swiper-js'), null, true);
    wp_enqueue_style('cvp-css', plugin_dir_url(__FILE__) . 'css/cvp.css');
}
add_action('wp_enqueue_scripts', 'cvp_enqueue_scripts');

function cvp_display_carousel() {
    $query = new WP_Query(array('post_type' => 'carousel_video', 'posts_per_page' => -1));
    if ($query->have_posts()) {
        ob_start();
        echo '<div class="swiper cvp-swiper"><div class="swiper-wrapper">';
        while ($query->have_posts()) {
            $query->the_post();
            $video = get_field('video_file');
            $img = get_field('produto_img');
            $preco = get_field('preco');
            $title = get_the_title();
            echo '<div class="swiper-slide">';
            echo '<div class="cvp-card">';
            echo '<video muted playsinline loop preload="auto" poster="' . esc_url($img) . '">';
            echo '<source src="' . esc_url($video) . '" type="video/mp4">';
            echo '</video>';
            echo '<div class="cvp-info">';
            echo '<img src="' . esc_url($img) . '" alt="' . esc_attr($title) . '">';
            echo '<div class="cvp-text"><h4>' . esc_html($title) . '</h4><p>' . esc_html($preco) . '</p></div>';
            echo '</div></div></div>';
        }
        echo '</div><div class="swiper-button-next"></div><div class="swiper-button-prev"></div></div>';
        wp_reset_postdata();
        return ob_get_clean();
    }
}
add_shortcode('cvp_carousel', 'cvp_display_carousel');
?>
