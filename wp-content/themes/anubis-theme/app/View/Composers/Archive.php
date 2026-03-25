<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Archive extends Composer
{
    protected static $views = [
        'archive',
        'archive-*',
    ];

    public function with()
    {
        return $this->getArchiveData();
    }

    private function getArchiveData()
    {
        // récupération du type de post courrant
        $post_type = get_post_type();

        // définitions du nom de la taxonomie et des colonnes
        $config = [
            'is' => [
                'taxonomy' => 'is_category',
                'columns' => [
                    'id' => 'ID',
                    'name' => 'Nom vernaculaire',
                    'date_discover' => 'Date de découverte',
                    'etat' => 'État',
                ],
            ],
            'folder' => [
                'taxonomy' => 'folder_category',
                'columns' => [
                    'id' => 'Identifiant',
                    'date_opening' => 'Date d\'ouverture',
                    'date_closing' => 'Date de fermeture',
                ],
            ],
            'message' => [
                'columns' => [
                    'character' => 'Personnel',
                    'date_laster' => 'Date du dernier message',
                ],
            ],
        ];

        if (!isset($config[$post_type])) {
            return [];
        }

        $taxonomy = $config[$post_type]['taxonomy'] ?? null;

        // récupération des taxonomies du post courrant
        $filters = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
        ]);

        // récupération du 1er taxo
        $filters = array_values($filters);
        $first = reset($filters);
        $first_slug = $first->slug ?? null;

        // requete de base pour récupérer les posts courrants
        $args = [
            'post_type' => $post_type,
            'posts_per_page' => get_option('posts_per_page'),
            'paged' => max(1, get_query_var('paged')),
        ];

        // ajout du filtre
        if (is_tax($taxonomy)) {
            $args['tax_query'] = [[
                'taxonomy' => $taxonomy,
                'field' => 'slug',
                'terms' => get_queried_object()->slug,
            ]];
        } elseif ($first_slug) {
            $args['tax_query'] = [[
                'taxonomy' => $taxonomy,
                'field' => 'slug',
                'terms' => $first_slug,
            ]];
        }

        return [
            'taxonomy'  => $taxonomy,
            'columns' => $config[$post_type]['columns'],
            'filters' => $filters,
            'query'   => new \WP_Query($args),
            'label_cpt' => get_post_type_object($post_type)->label,
        ];
    }
}