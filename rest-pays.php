<?php
/*
Plugin Name: API REST Pays
Description: Génère un menu dynamique des destinations de voyage par pays.
Version: 1.0
Author: Votre Nom
*/

// Enregistrement du point de terminaison REST API
add_action('rest_api_init', function () {
    register_rest_route('pays/v1', '/destinations/', array(
        'methods' => 'GET',
        'callback' => 'get_destinations',
    ));
});

function get_destinations($data) {
    $pays = $data['pays'] ? sanitize_text_field($data['pays']) : 'France';
    $args = array(
        'post_type' => 'post',
        'meta_query' => array(
            array(
                'key' => 'pays',
                'value' => $pays,
            ),
        ),
    );
    $query = new WP_Query($args);
    $destinations = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $destinations[] = array(
                'title' => get_the_title(),
                'image' => get_the_post_thumbnail_url() ?: 'https://via.placeholder.com/150',
            );
        }
        wp_reset_postdata();
    }

    return $destinations;
}

// Shortcode pour afficher le menu
function display_pays_menu() {
    ob_start();
    ?>
    <div id="pays-menu">
        <button class="pays-btn" data-pays="France">France</button>
        <button class="pays-btn" data-pays="États-Unis">États-Unis</button>
        <button class="pays-btn" data-pays="Canada">Canada</button>
        <button class="pays-btn" data-pays="Argentine">Argentine</button>
        <button class="pays-btn" data-pays="Chili">Chili</button>
        <button class="pays-btn" data-pays="Belgique">Belgique</button>
        <button class="pays-btn" data-pays="Maroc">Maroc</button>
        <button class="pays-btn" data-pays="Mexique">Mexique</button>
        <button class="pays-btn" data-pays="Japon">Japon</button>
        <button class="pays-btn" data-pays="Italie">Italie</button>
        <button class="pays-btn" data-pays="Islande">Islande</button>
        <button class="pays-btn" data-pays="Chine">Chine</button>
        <button class="pays-btn" data-pays="Grèce">Grèce</button>
        <button class="pays-btn" data-pays="Suisse">Suisse</button>
    </div>
    <div id="destinations"></div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.pays-btn');
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const pays = this.getAttribute('data-pays');
                fetch(`<?php echo get_rest_url(null, 'pays/v1/destinations/'); ?>?pays=${pays}`)
                    .then(response => response.json())
                    .then(data => {
                        const destinationsDiv = document.getElementById('destinations');
                        destinationsDiv.innerHTML = '';
                        data.forEach(destination => {
                            const destinationDiv = document.createElement('div');
                            destinationDiv.classList.add('destination');
                            destinationDiv.innerHTML = `
                                <h3>${destination.title}</h3>
                                <img src="${destination.image}" alt="${destination.title}">
                            `;
                            destinationsDiv.appendChild(destinationDiv);
                        });
                    });
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('pays_menu', 'display_pays_menu');
