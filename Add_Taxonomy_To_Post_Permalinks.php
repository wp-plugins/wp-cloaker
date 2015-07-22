<?php

/**
 * A helper class for registering and handling a custom rewrite tag for a custom taxonomy.
 *
 * @version 1.1.0
 */
class Add_Taxonomy_To_Post_Permalinks {
 
    /**
     * Stores the taxonomy slug that this class will be handling. Don't edit this.
     *
     * @since 1.0.0
     * @var string
     */
    public $taxonomy;
 
    /**
     * Stores the rewrite tag complete with percentage signs. Don't edit this.
     *
     * @since 1.0.0
     * @var string
     */
    public $rewrite_tag;
 
    /**
     * Initializes the class by calling Add_Taxonomy_To_Post_Permalinks::register()
     * as well as registering a filter that runs in get_permalink().
     *
     * @since 1.0.0
     *
     * @param string $taxonomy A taxonomy slug. Use the same one that you used with register_taxonomy().
     * @return array $optional_args Optional configuration parameters. See Add_Taxonomy_To_Post_Permalinks::register().
     */
    function __construct( $taxonomy, $optional_args = array() ) {
        if ( ! $this->register( $taxonomy, $optional_args ) )
            return;
 
        // Normal posts
        add_filter( 'post_link', array( $this, 'filter_post_link' ), 10, 2 );
 
        // Custom post types
        add_filter( 'post_type_link', array( $this, 'filter_post_link' ), 10, 2 );
    }
 
    /**
     * Registers the rewrite tag using add_rewrite_tag().
     *
     * Can accept an array of optional parameters:
     *
     * * tagname: The rewrite tag to use (no percentage signs). Defaults to the taxonomy slug.
     * * regex: What regex to use to validate the value of the tag. Defaults to anything but a forward slash.
     *
     * @since 1.0.0
     *
     * @param string $taxonomy A taxonomy slug. Use the same one that you used with register_taxonomy().
     * @return array $optional_args Optional configuration parameters. See function description.
     */
    public function register( $taxonomy, $optional_args = array() ) {
        if ( ! taxonomy_exists( $taxonomy ) )
            return false;
 
        $this->taxonomy = $taxonomy;
 
        $this->rewrite_tag = ( ! empty( $optional_args['tagname'] ) ) ? $optional_args['tagname'] : $this->taxonomy;
        $this->rewrite_tag = '%' . $this->rewrite_tag . '%';
 
        $rewrite_tag_regex = ( ! empty( $optional_args['regex'] ) ) ? $optional_args['regex'] : '([^/]+)';
 
        // See http://codex.wordpress.org/Rewrite_API/add_rewrite_tag
        add_rewrite_tag( $this->rewrite_tag, $rewrite_tag_regex );
 
        return true;
    }
 
    /**
     * Filters a post permalink to replace the tag placeholder with the first
     * used term from the taxonomy in question.
     *
     * @since 1.0.0
     *
     * @param string $permalink The existing permalink URL.
     * @return object $post The post object that this permalink belongs to.
     */
    public function filter_post_link( $permalink, $post ) {
        // Abort early if the placeholder rewrite tag isn't in the generated URL
        if ( false === strpos( $permalink, $this->rewrite_tag ) )
            return $permalink;
 
        // Get the custom taxonomy terms in use by this post
        $terms = get_the_terms( $post->ID, $this->taxonomy );
 
        // If no terms are assigned to this post, use the taxonomy slug instead (can't leave the placeholder there)
        if ( empty( $terms ) ) {
            $permalink = str_replace( $this->rewrite_tag, 'link', $permalink );
        }
 
        // Replace the placeholder rewrite tag with the first term's slug
        else {
            $first_term = array_shift( $terms );
            $permalink = str_replace( $this->rewrite_tag, $first_term->slug, $permalink );
        }
 
        return $permalink;
    }
}