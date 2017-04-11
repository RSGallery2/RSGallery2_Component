<?php
/**
 * @package       RSGallery2
 * @copyright (C) 2003 - 2017 RSGallery2
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * RSGallery is Free Software
 */

// no direct access
defined('_JEXEC') or die();

global $Rsg2DebugActive;

//JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');

//JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
//JHtml::_('behavior.formvalidator');
//JHtml::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');
//JHtml::_('formbehavior.chosen', 'select', null, array('disable_search_threshold' => 3));

/*
JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "comment.cancel" || document.formvalidator.isValid(document.getElementById("item-form")))
		{
			Joomla.submitform(task, document.getElementById("item-form"));
		}
	};
');
()
/**/

?>

<div id="installer-install" class="clearfix">
    <?php if (!empty($this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
    <div id="j-main-container">
    <?php endif; ?>

        <form action="<?php echo JRoute::_('index.php?option=com_rsgallery2&view=develop&amp;layout=DebugGalleryOrder'); ?>"
                method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">

            <div class="">
                <table style="width:100%">
                    <tr>
                        <th>Id</th>
                        <th>Order</th>
                        <th>ParentId</th>
                        <th>Gallery name</th>
                    </tr>

                    <?php

                    /**

                    echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

                    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general',
                        empty($this->item->id) ? JText::_('COM_RSGALLERY2_NEW') : JText::_('COM_RSGALLERY2_EDIT')); ?>
                    <div class="row-fluid">
                        <div class="span6 form-horizontal">
                            <fieldset class="adminform">
                                <?php
                                echo $this->form->renderField('name');
                                echo $this->form->renderField('gallery_id');
                                echo $this->form->renderField('description');
                                echo $this->form->renderField('id');
                                ?>
                            </fieldset>
                        </div>
                        <div class="span3">
                            <fieldset class="adminform">
                                <?php
                                echo $this->form->renderField('published');
                                echo $this->form->renderField('ordering');
                                echo $this->form->renderField('userid');
                                echo $this->form->renderField('image_2nd_col name');
                                ?>
                            </fieldset>

                            <?php
                            echo '<fieldset class="adminform">';
                            echo '    <div class="control-group">';
                            echo '        <div class="control-label">';
                            echo '            <label id="jform_preview-lbl" class="" for="jform_preview">' . JText::_('COM_RSGALLERY2_ITEM_PREVIEW') . '</label>';
                            echo '        </div>';
                            echo '        <div class="controls">';
                            echo '            <input id="jform_preview" class="readonly input-large" name="jform[preview]"' .
                                ' type="image" src="' . $this->HtmlImageSrc
                                . '" alt="' . $this->escape($this->item->descr) . '" />';
                            echo '        </div>';
                            // 				<img src="<php echo $display->url() >" alt="<php echo htmlspecialchars( stripslashes( $item->descr ), ENT_QUOTES );>" />
                            echo '    </div>';
                            echo '</fieldset>';
                            //				echo    JText::_('COM_RSGALLERY2_ITEM_PREVIEW');
                            //				echo '</div>';
                            ?>

                            <BR>
                            <strong>
                                <?php echo JText::_('COM_RSGALLERY2_LINKS_TO_IMAGE') ?>
                            </strong>
                            <BR>
                            <BR>
                            <?php echo JText::_('COM_RSGALLERY2_THUMB'); ?>
                            <input type="text" name="thumb_url" class="text_area input-xxlarge" size="180" value="<?php echo $this->HtmlPathThumb; ?>" readonly />

                            <BR>
                            <?php echo JText::_('COM_RSGALLERY2_DISPLAY'); ?>
                            <input type="text" name="display_url" class="text_area input-xxlarge" size="180" value="<?php echo $this->HtmlPathDisplay; ?>" readonly />

                            <BR>
                            <?php echo JText::_('COM_RSGALLERY2_ORIGINAL'); ?>
                            <input type="text" name="original_url" class="text_area input-xxlarge" size="80" value="<?php echo $this->HtmlPathOriginal; ?>" readonly />

                        </div>
                    </div>
                    <?php echo JHtml::_('bootstrap.endTab'); ?>

                    <?php echo JHtml::_('bootstrap.addTab', 'myTab', '2nd_col', JText::_('COM_RSGALLERY2_IMAGE_PERMISSION')); ?>
                    <div class="row-fluid">
                        <div class="span10">
                            <fieldset class="panelform">
                                <?php
                                echo $this->form->getControlGroups('permission_col');
                                ?>
                            </fieldset>
                        </div>
                    </div>
                    <?php echo JHtml::_('bootstrap.endTab'); ?>

                    <?php echo JHtml::_('bootstrap.endTabSet'); ?>

                    <?php /**/
                    // Display gallery data subset
                    /**/
                    $Galleries = $this->GalleriesOrderModel->OrderedGalleries();
                    // echo json_encode($Galleries);

                    foreach ($Galleries as $Gallery)
                    {
                        echo "X:" . json_encode($Gallery) . '<br>';
                        /**
                        ?>
                        <tr>
                            <th><?php echo $Gallery['id']?></th>
                            <th><?php echo $Gallery['order']?></th>
                            <th><?php echo $Gallery['parent']?></th>
                            <th><?php echo $Gallery['name']?></th>
                        </tr>
                    <?php
                         /**/
                    }
                    /**/
                    ?>
                </Table>
            </div>

            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>

        </form>
    </div>
</div>

