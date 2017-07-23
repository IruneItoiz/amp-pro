<?php

/**
* Print Google's AMP Analytics script.
*
* @since 0.0.1
*/
function amp_analytics_print_scripts(){
?>
<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
<?php
}
//add_action( 'amp_post_template_head', 'amp_analytics_print_scripts' );

/**
 * Print the JSON used for tracking.
 *
 * @since 0.0.1
 */
function amp_pro_add_analytics() {
    $options = get_option( 'amp_pro_analytics_settings' );
    $account = '"account": ' . '"' . esc_js( $options['amp_pro_analytics_ga_ua'] ) . '"' . PHP_EOL;

    $event_tracking =  isset( $options['amp_pro_analytics_outbound'] ) ? $options['amp_pro_analytics_outbound'] : 0;

    // Make sure we have a UA before injecting script
    if ( isset( $options['amp_pro_analytics_ga_ua'] ) && '' !== $options['amp_pro_analytics_ga_ua'] ) {
        $triggers['trackPageview'] = '"trackPageview": {
                            "on": "visible",
                            "request": "pageview"
                        }';

        if ($event_tracking) {
            $triggers['outboundLinks'] = "              \"outboundLinks\" :{
                    \"on\": \"click\",
                    \"selector\": \"a\",
                    \"request\": \"event\",
                    \"vars\": {
                        \"eventCategory\": \"click\",
                            \"eventAction\": ". '"${linkType}"'.",
                            \"eventLabel\": ". '"${outboundLink}"'."
                    }
\n              }";
        }
        ?>
<amp-analytics type="googleanalytics" id="googleanalytics1">
<script type="application/json">
    {
        "vars": {
          <?php echo $account;?>
        },
        "triggers": {
            <?php echo implode(",\n", $triggers); ?>

        }
    }
</script>
</amp-analytics>
        <?php
    }
}
add_action( 'amp_post_template_footer', 'amp_pro_add_analytics' );


add_action( 'pre_amp_render_post', 'amp_pro_add_custom_content_actions' );
function amp_pro_add_custom_content_actions() {
    add_filter( 'the_content', 'amp_pro_add_custom_outbound_tags' );
}

function amp_pro_add_custom_outbound_tags($content)
{
    $hrefPattern = '/<a[^>]+?href="(.+?)".*?>(.+?)<\/a>/i';

    $domain = get_site_url();

    $offset = 0;

    $options = get_option( 'amp_pro_analytics_settings' );


    while(preg_match($hrefPattern, $content, $hrefMatches, PREG_OFFSET_CAPTURE, $offset))
    {

        $hrefInner = $hrefMatches[1][0];
        $offset = $hrefMatches[1][1];
        $linkText = $hrefMatches[2][0];

        $external = false;
        $is_amazon = false;

        if((!strstr(strtolower($hrefInner),$domain)))
        {
            $external = true;

            if  ($options['amp_pro_analytics_amazon'])
            {
                $is_amazon = is_amazon_link($hrefInner);

            }
        } else {
            $external = false;
        }

        $data_type = ' data-vars-link-type="';
        if (!$external)
            $data_type .= 'internal';
        else if ($is_amazon)
            $data_type .= 'amazon';
        else $data_type .= 'external';


        $data_tag = $hrefInner.'" data-vars-outbound-link="'.strip_tags($linkText).'" '.$data_type;
        echo $data_tag;
        $data_length = strlen($data_tag);
        $content = str_replace( $hrefInner, $data_tag, $content);
        $offset += $data_length;
    }

    return $content;
}

function is_amazon_link($hrefInner)
{
    $amazon_domains = array (
            'amzn.to',
            'www.amazon'
    );

    foreach ( $amazon_domains as $url) {
        //if (strstr($string, $url)) { // mine version
        if (strpos($hrefInner, $url) !== FALSE) { // Yoshi version
            return true;
        }
    }
    return false;
}
