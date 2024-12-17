<?php
/*
Plugin Name: Encuestas Simples
Description: Plugin para crear encuestas simples de dos opciones con resultados y exportación.
Version: 2.1
Author: Jesús Rojas
Author URI: https://www.linkedin.com/in/jesusjrojasr/
*/

// Registrar el Custom Post Type Encuestas
function encuestas_register_cpt() {
    register_post_type('encuestas', [
        'labels' => [
            'name' => 'Encuestas',
            'singular_name' => 'Encuesta'
        ],
        'public' => true,
        'show_in_menu' => true,
        'supports' => ['title', 'editor'],
        'menu_icon' => 'dashicons-chart-bar'
    ]);
}
add_action('init', 'encuestas_register_cpt');

// Agregar Metaboxes
function encuestas_add_metabox() {
    add_meta_box('encuesta_options', 'Opciones, Resultados y Shortcode', 'encuestas_metabox_callback', 'encuestas', 'normal', 'default');
}
add_action('add_meta_boxes', 'encuestas_add_metabox');

function encuestas_metabox_callback($post) {
    global $wpdb;
    $opcion1 = get_post_meta($post->ID, 'encuesta_opcion1', true);
    $opcion2 = get_post_meta($post->ID, 'encuesta_opcion2', true);
    $shortcode = '[encuesta id="' . $post->ID . '"]';
    $resultados = encuestas_obtener_resultados($post->ID);

    ?>
    <p><strong>Shortcode:</strong> <code><?php echo esc_html($shortcode); ?></code></p>
    <p><strong>Opción 1:</strong> <input type="text" name="encuesta_opcion1" value="<?php echo esc_attr($opcion1); ?>" style="width: 100%;"></p>
    <p><strong>Opción 2:</strong> <input type="text" name="encuesta_opcion2" value="<?php echo esc_attr($opcion2); ?>" style="width: 100%;"></p>

    <h4>Resultados</h4>
    <ul>
        <li><?php echo esc_html($opcion1); ?>: <?php echo $resultados['1']; ?> votos</li>
        <li><?php echo esc_html($opcion2); ?>: <?php echo $resultados['2']; ?> votos</li>
        <li><strong>Total:</strong> <?php echo array_sum($resultados); ?> votos</li>
    </ul>
    <?php
}

function encuestas_obtener_resultados($encuesta_id) {
    global $wpdb;
    $tabla = $wpdb->prefix . 'encuestas_resultados';
    $resultados = $wpdb->get_results($wpdb->prepare(
        "SELECT opcion, COUNT(*) as total FROM $tabla WHERE encuesta_id = %d GROUP BY opcion",
        $encuesta_id
    ));
    return [
        '1' => $resultados[0]->total ?? 0,
        '2' => $resultados[1]->total ?? 0
    ];
}

// Guardar Metaboxes
function encuestas_save_metabox($post_id) {
    if (array_key_exists('encuesta_opcion1', $_POST)) {
        update_post_meta($post_id, 'encuesta_opcion1', sanitize_text_field($_POST['encuesta_opcion1']));
    }
    if (array_key_exists('encuesta_opcion2', $_POST)) {
        update_post_meta($post_id, 'encuesta_opcion2', sanitize_text_field($_POST['encuesta_opcion2']));
    }
}
add_action('save_post', 'encuestas_save_metabox');

// Shortcode para Mostrar Encuesta
function encuestas_shortcode($atts) {
    global $wpdb;
    $atts = shortcode_atts(['id' => 0], $atts);
    $post_id = intval($atts['id']);
    $opcion1 = get_post_meta($post_id, 'encuesta_opcion1', true);
    $opcion2 = get_post_meta($post_id, 'encuesta_opcion2', true);

    ob_start();
    ?>
    <div id="encuesta-<?php echo $post_id; ?>">
        <h2><?php echo get_the_title($post_id); ?></h2>
        <div id="encuesta-contenido-<?php echo $post_id; ?>">
            <?php if (!isset($_COOKIE['encuesta_votada_' . $post_id])) : ?>
                <button onclick="votarEncuesta('<?php echo $post_id; ?>', '1', '<?php echo esc_js($opcion1); ?>')">
                    <?php echo esc_html($opcion1); ?></button>
                <button onclick="votarEncuesta('<?php echo $post_id; ?>', '2', '<?php echo esc_js($opcion2); ?>')">
                    <?php echo esc_html($opcion2); ?></button>
            <?php else: ?>
                <div>
                    <p>Gracias por tu participación.</p>
                    <?php encuestas_mostrar_resultados_html($post_id); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function votarEncuesta(postId, opcion, nombreOpcion) {
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=encuestas_votar&encuesta_id=${postId}&opcion=${opcion}`
            }).then(response => response.text()).then(data => {
                document.getElementById('encuesta-contenido-' + postId).innerHTML = `<p>Gracias por tu participación. Votaste por: <strong>${nombreOpcion}</strong></p>` + data;
                document.cookie = `encuesta_votada_${postId}=true; path=/; max-age=86400;`;
            });
        }
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('encuesta', 'encuestas_shortcode');

function encuestas_mostrar_resultados_html($encuesta_id) {
    $resultados = encuestas_obtener_resultados($encuesta_id);
    $opcion1 = get_post_meta($encuesta_id, 'encuesta_opcion1', true);
    $opcion2 = get_post_meta($encuesta_id, 'encuesta_opcion2', true);

    echo '<ul>';
    echo '<li>' . esc_html($opcion1) . ': ' . $resultados['1'] . ' votos</li>';
    echo '<li>' . esc_html($opcion2) . ': ' . $resultados['2'] . ' votos</li>';
    echo '</ul>';
}

// Procesar Voto
function encuestas_votar() {
    global $wpdb;
    $encuesta_id = intval($_POST['encuesta_id']);
    $opcion = sanitize_text_field($_POST['opcion']);

    if (!in_array($opcion, ['1', '2'])) {
        wp_die('Opción inválida');
    }

    $wpdb->insert($wpdb->prefix . 'encuestas_resultados', [
        'encuesta_id' => $encuesta_id,
        'opcion' => $opcion,
        'user_ip' => $_SERVER['REMOTE_ADDR']
    ]);

    encuestas_mostrar_resultados_html($encuesta_id);
    wp_die();
}
add_action('wp_ajax_encuestas_votar', 'encuestas_votar');
add_action('wp_ajax_nopriv_encuestas_votar', 'encuestas_votar');


// Agregar Submenú para Exportar Resultados
function encuestas_exportar_menu() {
    add_submenu_page(
        'edit.php?post_type=encuestas',
        'Exportar Resultados',
        'Reportes',
        'manage_options',
        'exportar_encuestas',
        'encuestas_exportar_pagina'
    );
}
add_action('admin_menu', 'encuestas_exportar_menu');

// Contenido de la Página de Reportes
function encuestas_exportar_pagina() {
    ?>
    <div class="wrap">
        <h1>Exportar Resultados de Encuestas</h1>
        <p>Haga clic en el botón de abajo para exportar un archivo CSV con todas las encuestas y sus resultados compactos.</p>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="encuestas_exportar_resultados">
            <button type="submit" class="button button-primary">Exportar Reporte Completo</button>
        </form>
    </div>
    <?php
}

// Exportar Resultados Generales a CSV
function encuestas_exportar_resultados() {
    global $wpdb;
    $tabla = $wpdb->prefix . 'encuestas_resultados';
    $encuestas = $wpdb->get_results("SELECT encuesta_id, opcion, COUNT(*) as total FROM $tabla GROUP BY encuesta_id, opcion");

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=encuestas_reportes.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Encuesta ID', 'Titulo Encuesta', 'Opcion 1', 'Votos Opcion 1', 'Opcion 2', 'Votos Opcion 2', 'Total Votos']);

    $resultados_compactos = [];
    foreach ($encuestas as $encuesta) {
        $encuesta_id = $encuesta->encuesta_id;
        $opcion = $encuesta->opcion;
        $total = $encuesta->total;

        if (!isset($resultados_compactos[$encuesta_id])) {
            $resultados_compactos[$encuesta_id] = [
                'titulo' => get_the_title($encuesta_id),
                'opcion1' => get_post_meta($encuesta_id, 'encuesta_opcion1', true),
                'opcion2' => get_post_meta($encuesta_id, 'encuesta_opcion2', true),
                'votos1' => 0,
                'votos2' => 0
            ];
        }

        if ($opcion == '1') {
            $resultados_compactos[$encuesta_id]['votos1'] = $total;
        } elseif ($opcion == '2') {
            $resultados_compactos[$encuesta_id]['votos2'] = $total;
        }
    }

    foreach ($resultados_compactos as $id => $data) {
        $total_votos = $data['votos1'] + $data['votos2'];
        fputcsv($output, [$id, $data['titulo'], $data['opcion1'], $data['votos1'], $data['opcion2'], $data['votos2'], $total_votos]);
    }

    fclose($output);
    exit;
}
add_action('admin_post_encuestas_exportar_resultados', 'encuestas_exportar_resultados');
