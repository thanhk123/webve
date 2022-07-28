<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Posts extends Tag
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-posts';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return __('Posts', 'dynamic-content-for-elementor');
    }
    /**
     * Get Group
     *
     * @return string
     */
    public function get_group()
    {
        return 'dce';
    }
    /**
     * Get Categories
     *
     * @return array<string>
     */
    public function get_categories()
    {
        return ['base', 'text'];
    }
    /**
     * Register Controls
     *
     * @return void
     */
    protected function register_controls()
    {
        $this->add_control('separator', ['label' => __('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['new_line' => __('New Line', 'dynamic-content-for-elementor'), 'line_break' => __('Line Break', 'dynamic-content-for-elementor'), 'comma' => __('Comma', 'dynamic-content-for-elementor')], 'default' => 'new_line', 'multiple' => \true]);
        $this->add_control('post_type', ['label' => __('Post Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => Helper::get_post_types(), 'multiple' => \true, 'label_block' => \true]);
        $this->add_control('post_status', ['label' => __('Post Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => get_post_statuses(), 'multiple' => \true, 'label_block' => \true, 'default' => ['publish']]);
        $this->add_control('orderby', ['label' => __('Order By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_post_orderby_options(), 'default' => 'date']);
        $this->add_control('order', ['label' => __('Order', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ASC' => __('Ascending', 'dynamic-content-for-elementor'), 'DESC' => __('Descending', 'dynamic-content-for-elementor')], 'default' => 'DESC']);
        $this->add_control('posts', ['label' => __('Results', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '10']);
    }
    /**
     * Render
     *
     * @return void
     */
    public function render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $args = $this->get_args();
        $wp_query = new \WP_Query($args);
        if ($wp_query->have_posts()) {
            while ($wp_query->have_posts()) {
                // @phpstan-ignore-line
                $wp_query->the_post();
                if ('new_line' === $settings['separator'] || empty($settings['link'])) {
                    echo sanitize_text_field(get_the_title());
                } else {
                    echo '<a href=' . get_the_permalink() . '>' . sanitize_text_field(get_the_title()) . '</a>';
                }
                if ($wp_query->current_post + 1 !== $wp_query->post_count) {
                    echo $this->separator($settings['separator']);
                }
            }
            wp_reset_postdata();
            // @phpstan-ignore-line
        }
    }
    /**
     * Get Args
     *
     * @return array<string,int|string>
     */
    protected function get_args()
    {
        $settings = $this->get_settings_for_display();
        return ['post_type' => $settings['post_type'], 'posts_per_page' => $settings['posts'], 'order' => $settings['order'], 'orderby' => $settings['orderby'], 'post_status' => $settings['post_status']];
    }
    /**
     * Separator
     *
     * @param string $choice
     * @return string
     */
    protected function separator(string $choice)
    {
        switch ($choice) {
            case 'line_break':
                return '<br />';
            case 'new_line':
                return "\n";
            case 'comma':
                return ', ';
        }
        return '';
    }
}
