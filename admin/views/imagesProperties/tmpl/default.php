<?php // no direct access
/**
 * @package       RSGallery2
 * @copyright (C) 2003 - 2017 RSGallery2
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * RSGallery is Free Software
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

global $Rsg2DebugActive;
// global $rsgConfig;

/**
$sortColumn    = $this->escape($this->state->get('list.ordering')); //Column
$sortDirection = $this->escape($this->state->get('list.direction'));
/**/

$user   = JFactory::getUser();
$userId = $user->id;

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
			<form action="<?php echo JRoute::_('index.php?option=com_rsgallery2&view=imagesProperties'); ?>"
					method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
				<?php
				// Search tools bar
				// OK: echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
				echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));

				//echo JLayoutHelper::render('joomla.searchtools.default', $data, null, array('component' => 'none'));
				// I managed to add options as always open
				//echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filtersHidden' => false ($hidden) (true/false) )));
				?>
				<?php if (empty($this->items)) : ?>
					<div class="alert alert-no-items">
yyy						<?php echo JText::_('COM_RSGALLERY2_GALLERY_HAS_NO_IMAGES_ASSIGNED'); ?>
					</div>
				<?php else : ?>

					<table class="table table-striped table-hover" id="imagessList">
						<thead>
						<tr>
							<th width="1%">
								<?php echo JText::_('COM_RSGALLERY2_NUM'); ?>
							</th>

							<th width="1%" class="center">
								<?php echo JHtml::_('grid.checkall'); ?>
							</th>

							<th width="1%" style="min-width:55px" class="nowrap center">
								<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $sortDirection, $sortColumn); ?>
							</th>

							<th width="20%" class="">
								<?php echo JHtml::_('searchtools.sort', 'COM_RSGALLERY2_TITLE', 'a.title', $sortDirection, $sortColumn); ?>
							</th>

							<th width="20%" class="hidden-phone">
								<?php echo JHtml::_('searchtools.sort', 'COM_RSGALLERY2_NAME', 'a.name', $sortDirection, $sortColumn); ?>
							</th>

							<th width="10%" class="">
								<?php echo JHtml::_('searchtools.sort', 'COM_RSGALLERY2_GALLERY', 'gallery_name', $sortDirection, $sortColumn); ?>
							</th>

							<th width="4%" class="center">
								<?php echo JHtml::_('searchtools.sort', 'COM_RSGALLERY2_ORDER', 'a.ordering', $sortDirection, $sortColumn); ?>
								&nbsp
								<?php if ($user->authorise('core.edit.state')): ?>
									<button id="filter_go" class="btn btn-micro"
											onclick="Joomla.submitbutton('images.saveOrdering')"
											title="<?php echo JText::_('COM_RSGALLERY2_ASSIGN_CHANGED_ORDER'); ?>">
										<i class="icon-save"></i>
									</button>
								<?php endif; ?>
							</th>

							<th width="8%" class="center nowrap hidden-phone">
								<?php echo JHtml::_('searchtools.sort', 'COM_RSGALLERY2_DATE__TIME', 'a.date', $sortDirection, $sortColumn); ?>
							</th>

							<th width="1%" class="center nowrap hidden-phone">
								<?php echo JHtml::_('searchtools.sort', 'COM_RSGALLERY2_VOTES', 'a.votes', $sortDirection, $sortColumn); ?>
							</th>

							<th width="1%" class="center nowrap hidden-phone">
								<?php echo JHtml::_('searchtools.sort', 'COM_RSGALLERY2_RATING', 'a.rating', $sortDirection, $sortColumn); ?>
							</th>

							<th width="1%" class="center nowrap hidden-phone">
								<?php echo JHtml::_('searchtools.sort', 'COM_RSGALLERY2_COMMENTS', 'a.comments', $sortDirection, $sortColumn); ?>
							</th>

							<th width="1%" class="center nowrap hidden-phone">
								<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $sortDirection, $sortColumn); ?>
							</th>

							<th width="1%" class="center nowrap hidden-phone">
								<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $sortDirection, $sortColumn); ?>
							</th>

						</tr>

						</thead>
						<tfoot>
						<tr>
							<td colspan="15">
								<?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
						</tfoot>
						<tbody>
						<?php

						foreach ($this->items as $i => $item)
						{
							/**/
							// Get permissions
							$canEditOwnImage = $user->authorise('core.edit.own', 'com_rsgallery2.image.' . $item->id) AND ($item->userid == $userId);
							$canEditImage = $user->authorise('core.edit', 'com_rsgallery2.image.' . $item->id) || $canEditImage;

							$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;

							$canEditStateOwnImage = $user->authorise('core.edit.state.own', 'com_rsgallery2.image.' . $item->id) AND ($item->userid == $userId);
							$canEditStateImage = $user->authorise('core.edit.state', 'com_rsgallery2.image.' . $item->id) || $canEditStateOwnImage;

							?>
							<tr>
								<td>
									<?php echo $this->pagination->getRowOffset($i); ?>
								</td>

								<td>
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								</td>

								<td>
									<?php echo JHtml::_('jgrid.published', $item->published, $i); //, 'articles.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
								</td>
								<td class="left has-context">
									<div class="pull-left break-word">
										<?php
										if ($item->checked_out)
										{
											echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'images.', $canCheckin);
										}
										?>
										<strong>

											<?php
											$link = JRoute::_("index.php?option=com_rsgallery2&view=image&task=image.edit&id=" . $item->id);

											$src   = $this->HtmlPathThumb . $this->escape($item->name) . '.jpg';
											$style = '';
											//$style .= 'max-width:' . '200' . 'px;';
											//$style .= 'max-height:' . '200' . 'px;';
											//$style .= 'width:' . '100' . 'px;';
											//$style .= ' height:' . '100' . 'px;';
											$img = '<img src="' . $src . '" alt="' . $this->escape($item->name) . '" style="' . $style . '" />';

											/**/
											echo JHtml::tooltip($img,
												JText::_('COM_RSGALLERY2_EDIT_IMAGE'),
												$this->escape($item->title),
//														htmlspecialchars(stripslashes($item->title), ENT_QUOTES),
												$this->escape($item->title),
												$link
											); // display link yes / no
											/**/
											?>
										</strong>

										<?php
										/**
											<span class="small break-word">
												<?php
												// echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));
												?>
											</span>
										 */
										?>
									</div>
								</td>
								<td class="left hidden-phone ">
									<div class="pull-left break-word">
										<?php
										$link = JRoute::_("index.php?option=com_rsgallery2&view=image&task=image.edit&id=" . $item->id);
										//$link = JRoute::_("index.php?option=com_rsgallery2&amp;rsgOption=images&amp;task=editA&amp;hidemainmenu=1&amp;id=" . $item->id);
										if ($canEditImage)
										{
											echo '<a href="' . $link . '"">' . $this->escape($item->name) . '</a>';
										}
										else
										{
											echo $this->escape($item->name);
										}
										?>
									</div>
								</td>

								<td class="left">
									<?php
									$link = JRoute::_("index.php?option=com_rsgallery2&view=gallery&task=gallery.edit&id=" . $item->gallery_id);
									//$link = JRoute::_("index.php?option=com_rsgallery2&rsgOption=galleries&task=editA&hidemainmenu=1&id=". $item->gallery_id);
									//echo '<a href="' . $link . '"">' . $item->gallery_id . '</a>';
									echo '<a href="' . $link . '"">' . $this->escape($item->gallery_name) . '</a>';
									?>
								</td>

								<td class="center">
									<?php if ($canEditStateImage): ?>
										<div class="form-group">
											<label class="hidden" for="order[]">Ordering</label>
											<input name="order[]" type="number"
													class="input-mini form-control changeOrder"
													min="0" step="1"
													id="ordering_<?php echo $item->id; ?>"
													value="<?php echo $item->ordering; ?>"
													gallery_id="<?php echo $item->gallery_id; ?>"
											</input>
										</div>
									<?php else : ?>
										<div class="form-group">
											<?php echo $item->ordering; ?>
										</div>
									<?php endif; ?>
								</td>

								<td class="nowrap small hidden-phone center">
									<?php echo JHtml::_('date', $item->date, JText::_('COM_RSGALLERY2_DATE_FORMAT_WITH_TIME')); ?>
								</td>

								<td class="hidden-phone center">
									<?php echo (int) $item->votes; ?>
								</td>

								<td class="hidden-phone center">
									<?php echo (int) $item->rating; ?>
								</td>

								<td class="hidden-phone center">
									<?php echo (int) $item->comments; ?>
								</td>

								<td class="hidden-phone center">
									<?php echo (int) $item->hits; ?>
								</td>

								<td>
									<?php echo (int) $item->id; ?>
									<input type="hidden" name="ids[]" value="<?php echo (int) $item->id; ?>" />
								</td>

							</tr>

							<?php
						}
						?>

						</tbody>
					</table>

					<?php // Load the batch processing form. ?>
					<?php if ($user->authorise('core.create', 'com_rsgallery2')
						&& $user->authorise('core.edit', 'com_rsgallery2')
						&& $user->authorise('core.edit.state', 'com_rsgallery2')
					) : ?>
						<?php echo JHtml::_(
							'bootstrap.renderModal',
							'collapseModal',
							array(
								'title'  => JText::_('COM_CONTENT_BATCH_OPTIONS'),
								'footer' => $this->loadTemplate('batch_footer')
							),
							$this->loadTemplate('batch_body')
						); ?>
					<?php endif; ?>

				<?php endif; ?>

				<div>
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="boxchecked" value="0" />

					<?php echo JHtml::_('form.token'); ?>
				</div>

			</form>

		</div>

		<div id="loading"></div>
	</div>
