<?php
add_action('the_post', 'af_check_post_query', 10, 2);

// save the unique queries on the page, so that if a post is contained within another post, we can properly scope the
// footnote numbering to that particular post
function af_check_post_query($scoped_post, $scoped_query = null)
{
    global $af_active_query;
    if (isset($scoped_query)) {
        $af_active_query = $scoped_query;
    }
}
