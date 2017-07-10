<?php


add_action( 'pre_amp_render_post', 'amp_pro_add_adsense_custom_content_actions' );
function amp_pro_add_adsense_custom_content_actions() {
    add_filter( 'the_content', 'amp_pro_add_adsense_inarticle' );
}

function amp_pro_add_adsense_inarticle($content)
{

    $options = get_option( 'amp_pro_adsense_settings' );

    if  ( isset($options['amp_pro_adsense_account']) && '' != $options['amp_pro_adsense_account'] &&
        isset($options['amp_pro_adsense_adslot']) && '' != $options['amp_pro_adsense_adslot']

    )
    {

        $adsense_code = '
        <div class="amp-ad-wrapper">
            <amp-ad class="amp-ad-4"
            type="adsense"
            width=336 height=280
            data-ad-client="'.$options['amp_pro_adsense_account'].'"
            data-ad-slot="'.$options['amp_pro_adsense_adslot'].'"></amp-ad>
        </div>
        ';

        //Find the first paragraph after the TOC
        $tocID = stripos($content, 'id="toc_container"');
        $afterTOC = stripos($content, '</div>',$tocID );
        $firstParagraphAfterTOC = stripos($content, '<p>', $afterTOC );
        $content_noTOC = substr($content, $firstParagraphAfterTOC);
        $content_array = explode('</p>', $content_noTOC);
        $i = 0;
        $content_adsense = '';
        $last_paragraph = count($content_array) - 2;
        foreach ($content_array as $paragraph) {
            if (!($i % 2) && ($i < $last_paragraph))
                $content_adsense .= "\n" . $adsense_code . "\n" . $paragraph . '</p>';
            else $content_adsense .= $paragraph . '</p>';

            $i++;
        }
        $content_pre_TOC = substr($content, 0, $firstParagraphAfterTOC);
        $content_adsense =  $content_pre_TOC.$content_adsense;

    } else return $content;

    return $content_adsense;
}
