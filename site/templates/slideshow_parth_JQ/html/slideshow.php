<?php
/**
 * @version       $Id $
 * @package       RSGallery2
 * @copyright (C) 2003 - 2020 RSGallery2
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

//JHtml::_('behavior.framework', true);  // load mootools ToDo: Remove mootools
JHtml::_('jquery.framework'); // load jquery

global $rsgConfig;
$doc = JFactory::getDocument();

//get the array containing all the script declarations
$headData = $doc->getHeadData();
// Scripts like below
$script = $headData['script'];

// Is script not loaded from previous gallery?
if (strpos(json_encode($script), 'startGalleries') === false) {

    //Add stylesheets and scripts to header
    $css1 = JURI::base() . 'components/com_rsgallery2/templates/slideshow_parth_JQ/css/jquery.sleekgallery.css';
    $doc->addStyleSheet($css1);
    $css2 = JURI::base() . 'components/com_rsgallery2/templates/slideshow_parth_JQ/css/layout.css';
    $doc->addStyleSheet($css2);

    $js1 = JURI::base() . 'components/com_rsgallery2/templates/slideshow_parth_JQ/js/jqScrollTo/jquery.scrollTo.min.js';
    $doc->addScript($js1);
    $js2 = JURI::base() . 'components/com_rsgallery2/templates/slideshow_parth_JQ/js/jquery.sleekgallery.js';
    $doc->addScript($js2);
    $js3 = JURI::base() . 'components/com_rsgallery2/templates/slideshow_parth_JQ/js/jquery.sleekgallery.transitions.js';
    $doc->addScript($js3);

    //--- Override default CSS styles ---
    // Add styles

    /* Slideshow width and height */
    $style = '.myGallery, #myGallerySet, #flickrGallery {' . "\n"
        . '   width: ' . ($this->params->get('slideshowWidth') ? $this->params->get('slideshowWidth') : $this->maxSlideshowWidth) . 'px;' . "\n"
        . '   height:  ' . ($this->params->get('slideshowHeight') ? $this->params->get('slideshowHeight') : $this->maxSlideshowHeight) . 'px;' . "\n"
        . '   	}' . "\n"
        /* Background color for the slideshow element */
        . '   	.sgGallery .slideElement {' . "\n"
        . '   		background-color:  ' . $this->params->get('slideshowBackgroundcolor', '#000000') . ";\n"
        . '   	}' . "\n"
        /* Background color of links (Override personal.css) */
        . '   	#main a:hover, #main a:active, #main a:focus{' . "\n"
        . '   		background-color: transparent;' . "\n"
        . '   	}' . "\n"
        /* slideInfoZone text color */
        . '   	#main .slideInfoZone h2, #main .slideInfoZone p{ ' . "\n"
        . '   		color:  ' . $this->params->get('slideInfoZoneTextcolor', '#EEEEEE') . ";\n"
        . '   	}' . "\n"
        /* Carousel backgroundcolor, color item title, height */
        . '   	.sgGallery .carousel { ' . "\n"
        . '   		background-color:  ' . $this->params->get('carouselBackgroundcolor', '#000000') . ";\n"
        . '   		color:  ' . $this->params->get('carouselTextcolor', '#FFFFFF') . ";\n"
        . '   		height:	 ' . $this->params->get('carouselHeight', '135') . 'px' . ";\n"
        . '   	}' . "\n"
        /* Carousel height for thumbs-text position (= .sgGallery .carousel {height} + 20px ) */
        . '   	.sgGallery div.carouselContainer {' . "\n"
        . '   		height:	 ' . ($this->params->get('carouselHeight', '135') + 20) . 'px' . ";\n"
        . '   	}' . "\n"
        /* Carousel backgroundcolor thumbs-text */
        . '   	.sgGallery a.carouselBtn {' . "\n"
        . '   		background:  ' . $this->params->get('carouselBackgroundcolor', '#333333') . ";\n"
        . '   		color:	 ' . $this->params->get('carouselTextcolor', '#FFFFFF') . ";\n"
        . '   	}' . "\n"
        /* Carousel color numberlabel */
        . '   	.sgGallery .carousel .label .number {' . "\n"
        . '   		color: 	 ' . $this->params->get('carouselNumberlabelColor', '#B5B5B5') . ";\n"
        . '   	}' . "\n"
        /* slideInfoZone background color, height */
        . '   	.sgGallery .slideInfoZone, .sgGallery .slideInfoZone h2 {' . "\n"
        . '   		background-color:  ' . $this->params->get('slideInfoZoneBackgroundcolor', '#333333') . ";\n"
        . '   		height:  ' . $this->params->get('slideInfoZoneHeight', '60') . 'px' . ";\n"
        . '   	}' . "\n";

    $doc->addStyleDeclaration($style);

	/* user has the last word ... */
    $css3 = JURI::base() . 'components/com_rsgallery2/templates/slideshow_parth/css/user.css';
    if(file_exists($css3))
    {
        $doc->addStyleSheet($css3);
    }
    $javascript = '';

    // Variable declaration
    /* Automated slideshow */
    $timed = $this->params->get('automated_slideshow', 1);
    /* Show the thumbs carousel */
    $showCarousel = $this->params->get('showCarousel', 1);
    /* Text on carousel tab */
    $textShowCarousel = ($this->params->get('textShowCarousel') == '')
        ? JText::_('COM_RSGALLERY2_SLIDESHOW_PARTH_THUMBS')
        : $this->params->get('textShowCarousel');
    /* Thumbnail height */
    $thumbHeight = $this->params->get('thumbHeight', 50);
    /* Thumbnail width*/
    $thumbWidth = $this->params->get('thumbWidth', 50);
    /* Fade duration in milliseconds (500 equals 0.5 seconds)*/
    $fadeDuration = $this->params->get('fadeDuration', 500);
    /* Delay in milliseconds (6000 equals 6 seconds)*/
    $delay = $this->params->get('delay', 6000);
    /* Disable the 'open image' link for the images */
    $embedLinks = $this->params->get('embedLinks', 1);
    $defaultTransition = $this->params->get('defaultTransition', 'fade');
    $showInfopane = $this->params->get('showInfopane', 1);
    $slideInfoZoneSlide = $this->params->get('slideInfoZoneSlide', 1);
    $showArrows = $this->params->get('showArrows', 1);


    $javascript = <<<SQL
        /* <![CDATA[ */
        function startGallery() {
            var sleekGallery = jQuery('.myGallery').sleekGallery({
                timed: {$timed},
                showCarousel: {$showCarousel},
                thumbHeight: {$thumbHeight},
                thumbWidth: {$thumbWidth},
                fadeDuration: {$fadeDuration},
                delay: {$delay},
                embedLinks: {$embedLinks},
                defaultTransition: '{$defaultTransition}', // defaultTransition: 'continuoushorizontal'
                showInfopane: {$showInfopane},
                slideInfoZoneSlide: {$slideInfoZoneSlide},
                showArrows: {$showArrows},
                textShowCarousel: '{$textShowCarousel}',
            });
        }
        jQuery(document).ready(function() {
            // alert("jQury Version: " + jQuery.fn.jquery );
            startGallery();
        });
        /* ]]> */
SQL;


    // Add Javascript
    $doc->addScriptDeclaration($javascript);
} // End script not loaded

/*------------------------------------------------------------
  Show form
------------------------------------------------------------*/

//--- back link to gallery view --------------------------------------

// Show link only when menu-item is not a direct link to the slideshow
$input = JFactory::getApplication()->input;
$view  = $input->get('view', '', 'CMD');
if ($view !== 'slideshow')
{
    $menuId = $input->get('Itemid', null, 'INT');
    $gid = $this->gid;

    $html = [];

    $html[] = '<div style="float: right;">' ."\n"
              //. '<a href="' .  JRoute::_('index.php?option=com_rsgallery2&Itemid=' . $menuId . '&gid=' . $gid) . '">'
              . '<a href="#">'
              .      JText::_('COM_RSGALLERY2_BACK_TO_GALLERY')
              . '</a>';
    $html[] = '</div>';

    echo implode("\n", $html);
}
// <!-- div class="rsg2-clr"></div -->

echo '<div class="parth_content">';

//--- Gallery title --------------------------------------

if (True)
{
	echo '<h3>';
	echo '    <div style="text-align:center;font-size:24px;">';
	echo '        ' . $this->galleryname;
	echo '    </div>';
	echo '</h3>';
}

//--- Gallery images --------------------------------------

echo '    <div class="rsg2-clr"></div>';
echo '    <div id="myGallery' . $this->gid . '" class="myGallery">';
echo          $this->slides;
echo '    </div><!-- end myGallery -->';

echo '</div><!-- End parth_content -->';
?>