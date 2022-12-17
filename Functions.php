// This is for custom search 


add_action('pre_get_posts', function($q) 
{
    
    if($title = $q->get( '_meta_or_title') )
    {
        add_filter( 'get_meta_sql', function( $sql ) use ($title)
        {
           
           global $wpdb;
           
        //   only run once:
        
        static $nr = 0;
        if( 0 != $nr++ ) return $sql;
        
        // Modify Where part:
        $sql['where'] = sprintf(
            
            " AND ( %s OR %s ) ",
            $wpdb->prepare( "{$wpdb->posts}.post_title LIKE '%s'", '%'.$title.'%'),
            mb_substr( $sql['where'], 5, mb_strlen( $sql['where'] ) )
            
            );
            
            return $sql;
            
        });
    }
    
});



add_filter('graphql_post_object_connection_query_args', function ($query_args, $source, $args, $context, $info) {

    if (isset($query_args['s'])) {
        $sku = $query_args['s'];
        unset($query_args['s']);
        
        $query_args['_meta_or_title'] = $sku;
            
        $query_args['meta_query'] = [
            [
                'key' => 'sku',
                'value' => $sku,
                'compare' => '='
            ]
        ];
    }
    
    // wp_send_json($query_args);

    return $query_args;
}, 10, 5);

