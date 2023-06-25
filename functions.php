<?php

function alura_intercambios_registrando_taxonomia()
{
    register_taxonomy(
        taxonomy: 'paises',
        object_type: 'destinos',
        args: array(
            'labels' => array(
                'name' => 'Países'
            ),
            'hierarchical' => true
        )
    );
}

add_action('init', 'alura_intercambios_registrando_taxonomia');

function alura_intercambios_registrando_post_customizado()
{
    register_post_type(
        post_type: 'destinos',
        args: array(
            'labels' => array(
                'name' => 'Destinos'
            ),
            'public' => true,
            'menu_position' => 0,
            'supports' => array(
                'title',
                'editor',
                'thumbnail'
            ),
            'menu_icon' => 'dashicons-admin-site'
        )
    );
}

add_action('init', 'alura_intercambios_registrando_post_customizado');

function alura_intercambios_adicionando_recursos_ao_tema()
{
    add_theme_support(
        feature: 'custom-logo'
    );
    add_theme_support(
        feature: 'post-thumbnails'
    );
}

add_action('after_setup_theme', 'alura_intercambios_adicionando_recursos_ao_tema');

function alura_intercambios_registrando_menu()
{
    register_nav_menu(
        location: 'menu-navegacao',
        description: 'Menu navegação'
    );
}

add_action('init', 'alura_intercambios_registrando_menu');


function alura_intercambios_registrando_post_customizando_banner()
{
    register_post_type(
        post_type: 'banners',
        args: array(
            'labels' => array(
                'name' => 'Banner'
            ),
            'public' => true,
            'menu_position' => 1,
            'menu_icon' => 'dashicons-format-image',
            'supports' => array(
                'title',
                'thumbnail'
            )
        )
    );
}

add_action('init', 'alura_intercambios_registrando_post_customizando_banner');


function alura_intercambios_registrando_metabox()
{
    add_meta_box(
        id: 'ai_registrando_metabox',
        title: 'Texto para a home',
        callback: 'ai_funcao_callback',
        screen: 'banners'
    );
}

add_action('add_meta_boxes', 'alura_intercambios_registrando_metabox');

function ai_funcao_callback($post)
{

    $texto_home_1 = get_post_meta(
        $post->ID,
        key: '_texto_home_1',
        single: true
    );
    $texto_home_2 = get_post_meta(
        $post->ID,
        key: '_texto_home_2',
        single: true
    );
?>
    <label for="texto_home_1">Texto 1</label>
    <input type="text" name="texto_home_1" style="width: 100%" value="<?= $texto_home_1 ?>" />
    <br>
    <br>
    <label for="texto_home_2">Texto 2</label>
    <input type="text" name="texto_home_2" style="width: 100%" value="<?= $texto_home_2 ?>" />
<?php
}


function alura_intercambios_salvando_dados_metabox($post_id)
{
    foreach ($_POST as $key => $value) {
        if ($key !== 'texto_home_1' && $key !== 'texto_home_2') {
            continue;
        }

        update_post_meta(
            $post_id,
            meta_key: '_' . $key,
            meta_value: $_POST[$key]
        );
    }
}


add_action('save_post', 'alura_intercambios_salvando_dados_metabox');

function pegandoTextosParaBanner()
{

    $args = array(
        'post_type' => 'banners',
        'post_status' => 'publish',
        'posts_per_page' => 1
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            $texto1 = get_post_meta(
                get_the_ID(),
                key: '_texto_home_1',
                single: true
            );
            $texto2 = get_post_meta(
                get_the_ID(),
                key: '_texto_home_2',
                single: true
            );
            return array(
                'texto_1' => $texto1,
                'texto_2' => $texto2
            );
        endwhile;
    endif;
}

function alura_intercambios_adicionando_scripts()
{
    $textosBanner = pegandoTextosParaBanner();

    if (is_front_page()) {
        wp_enqueue_script(
            handle: 'typed-js',
            src: get_template_directory_uri() . '/js/typed.min.js',
            deps: array(),
            ver: false,
            in_footer: true
        );
        wp_enqueue_script(
            handle: 'texto-banner-js',
            src: get_template_directory_uri() . '/js/texto-banner.js',
            deps: array(
                'typed-js'
            ),
            ver: false,
            in_footer: true
        );
        wp_localize_script(
            handle: 'texto-banner-js',
            object_name: 'data',
            l10n: $textosBanner
        );
    }
}


add_action('wp_enqueue_scripts', 'alura_intercambios_adicionando_scripts');
