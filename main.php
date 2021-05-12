<?php
/*
Plugin Name: Task
Description: Task
Version: 1.0
Author: DA
Author URI: #
*/

require_once plugin_dir_path(__FILE__) . 'kama.php';

add_action('init', 'geo_cpt_init');
function geo_cpt_init(){
    register_post_type('book', array(
        'labels'             => array(
            'name'               => 'Geo Data', // Основное название типа записи
            'singular_name'      => 'Geo', // отдельное название записи типа Book
            'add_new'            => 'Create Geo Data',
            'add_new_item'       => 'Add new Geo Data',
            'edit_item'          => 'Edit Geo Data',
            'new_item'           => 'New Geo Data',
            'view_item'          => 'View Geo Data',
            'search_items'       => 'Search Geo Data',
            'not_found'          => 'Geo Data Not Found',
            'not_found_in_trash' => 'Geo Data Not Found In Trash',
            'parent_item_colon'  => '',
            'menu_name'          => 'Geo Data'

        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title','editor','author','thumbnail','excerpt','comments')
    ) );
}



class_exists('Kama_Post_Meta_Box') && new Kama_Post_Meta_Box( array(
    'id'     => '_pers',
    'title'  => 'Personalaizer',
    'fields' => array(
        'select_field'    => array(
            'type'=>'select', 'title'=>'Выберите значение', 'options'=>array(''=>'', 'val_1'=>'Тест1', 'val_2'=>'Тест2')
        ),
    ),
) );

class_exists('Kama_Post_Meta_Box') && new Kama_Post_Meta_Box( array(
    'id'     => '_exp',
    'title'  => 'Expiration Date',
    'fields' => array(
        'special_field' => array(
            'title'=>'Post expires at the end of', 'callback'=>'special_field_out_function',
            // функция очистки полей
            'sanitize_func'=>function($array){ return array_map('sanitize_text_field', $array); }
        ),
    ),
) );

class_exists('Kama_Post_Meta_Box') && new Kama_Post_Meta_Box( array(
    'id'     => '_geo',
    'title'  => 'Geofencing Editor',
    'fields' => array(
        'special_field' => array(
            'callback'=>'special_field_out_function_for_map',
            // функция очистки полей
            'sanitize_func'=>function($array){ return array_map('sanitize_text_field', $array); }
        ),
    ),
) );

function special_field_out_function( $args, $post, $name, $val ){
    ob_start();
    ?>
    <div class="special_field_wrap">
        (Day)(Month)(Year): <input type="date" name="<?= $name ?>[box1]" min="<?php echo date("d-m-Y");?>" value="<?= esc_attr( @ $val['box1'] ) ?>">
        <p>Leave blank for no expiration date</p>
    </div>
    <?php
    return ob_get_clean();

}

function special_field_out_function_for_map(){
    ob_start();
    ?>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
    <input id="pac-input" class="controls" type="text" placeholder="Search Box">
    <div class="container" id="map-canvas" style="height:300px;"></div>
    <style>
        #map-canvas {
            margin: 0;
            padding: 0;
            height: 100%;
        }
    </style>

    <!-- Display geolocation data -->
    <script>
        function init() {
            var map = new google.maps.Map(document.getElementById('map-canvas'), {
                center: {
                    lat: 12.9715987,
                    lng: 77.59456269999998
                },
                zoom: 12
            });


            var searchBox = new google.maps.places.SearchBox(document.getElementById('pac-input'));
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(document.getElementById('pac-input'));
            google.maps.event.addListener(searchBox, 'places_changed', function() {
                searchBox.set('map', null);


                var places = searchBox.getPlaces();

                var bounds = new google.maps.LatLngBounds();
                var i, place;
                for (i = 0; place = places[i]; i++) {
                    (function(place) {
                        var marker = new google.maps.Marker({

                            position: place.geometry.location
                        });
                        marker.bindTo('map', searchBox, 'map');
                        google.maps.event.addListener(marker, 'map_changed', function() {
                            if (!this.getMap()) {
                                this.unbindAll();
                            }
                        });
                        bounds.extend(place.geometry.location);


                    }(place));

                }
                map.fitBounds(bounds);
                searchBox.set('map', map);
                map.setZoom(Math.min(map.getZoom(),12));

            });
        }
        google.maps.event.addDomListener(window, 'load', init);
    </script>
    <?php
    return ob_get_clean();

}
