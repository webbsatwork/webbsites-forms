$q = new WP_Query( array(
'meta_query' => array(
'relation' => 'AND',
'state_clause' => array(
'key' => 'state',
'value' => 'Wisconsin',
),
'city_clause' => array(
'key' => 'city',
'compare' => 'EXISTS',
),
),
'orderby' => array(
'city_clause' => 'ASC',
'state_clause' => 'DESC',
),
) );