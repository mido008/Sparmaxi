<?php
/**
 * @version		1.4.1
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2012 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die();

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
?>
<form action="index.php?option=com_eshop&view=reviews" method="post" name="adminForm" id="adminForm">
	<table width="100%">
		<tr>
			<td align="left">
				<?php echo JText::_( 'ESHOP_FILTER' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->state->search; ?>" class="text_area search-query" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'ESHOP_GO' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();" class="btn"><?php echo JText::_( 'ESHOP_RESET' ); ?></button>
			</td>
		</tr>
	</table>
	<div id="editcell">
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="2%" class="text_center">
						<?php echo JText::_( 'ESHOP_NUM' ); ?>
					</th>			
					<th width="2%" class="text_center">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<?php
					if (EshopHelper::isJ3())
					{
						?>
						<th width="1%" class="text_center" style="min-width:55px">
							<?php echo JHtml::_('grid.sort', JText::_('JSTATUS'), 'a.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
						</th>
                    	<?php
					}
					?>
					<th class="text_left" width="25%">
						<?php echo JHtml::_('grid.sort',  JText::_('ESHOP_PRODUCT'), 'b.product_name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<th class="text_center" width="20%">
						<?php echo JHtml::_('grid.sort',  JText::_('ESHOP_AUTHOR'), 'a.author', $this->lists['order_Dir'], $this->lists['order'] ); ?>				
					</th>
					<th class="text_center" width="20%">
						<?php echo JHtml::_('grid.sort',  JText::_('ESHOP_RATING'), 'a.rating', $this->lists['order_Dir'], $this->lists['order'] ); ?>				
					</th>
					<?php
					if (!EshopHelper::isJ3())
					{
						?>
						<th width="5%" class="text_center">
							<?php echo JHtml::_('grid.sort',  JText::_('ESHOP_PUBLISHED'), 'a.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
						</th>
						<?php
					}
					?>
					<th width="15%" class="text_center">
						<?php echo JHtml::_('grid.sort',  JText::_('ESHOP_CREATED_DATE'), 'a.created_date', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<th width="5%" class="text_center">
						<?php echo JHtml::_('grid.sort',  JText::_('ESHOP_ID'), 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<th width="10%" class="text_center">
						<?php echo JText::_('ESHOP_ACTION'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="9">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
				$row = &$this->items[$i];
				$link 	= JRoute::_( 'index.php?option=com_eshop&task=review.edit&cid[]='. $row->id);
				$editProductLink = JRoute::_( 'index.php?option=com_eshop&task=product.edit&cid[]='. $row->product_id);
				$checked 	= JHtml::_('grid.id',   $i, $row->id );				
				$published 	= JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png', 'review.' );			
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $this->pagination->getRowOffset( $i ); ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>
					<?php
					if (EshopHelper::isJ3())
					{
						?>
						<td class="text_center">
							<div class="btn-group">
								<?php
								echo JHtml::_('jgrid.published', $row->published, $i, 'review.');
								echo $this->addDropdownList(JText::_('ESHOP_DELETE'), 'trash', $i, 'review.remove');
								echo $this->renderDropdownList($this->escape($row->product_name));
								?>
							</div>
						</td>
						<?php
					}
					?>
					<td>
						<a href="<?php echo $editProductLink; ?>"><?php echo $row->product_name; ?></a>
					</td>
					<td class="text_center">
						<?php echo $row->author; ?>
					</td>
					<td class="text_center">
						<?php echo $row->rating; ?>
					</td>
					<?php
					if (!EshopHelper::isJ3())
					{
						?>
						<td class="text_center">
							<?php echo $published; ?>
						</td>
						<?php
					}
					?>
					<td class="text_center">
						<?php
							if ($row->created_date != $this->nullDate)
								echo JHtml::_('date', $row->created_date,EshopHelper::getConfigValue('date_format', 'm-d-Y'));
						?>
					</td>
					<td class="text_center">
						<?php echo $row->id; ?>
					</td>
					<td class="text_center">
						<a href="<?php echo $link; ?>"><?php echo JText::_('ESHOP_EDIT'); ?></a>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="option" value="com_eshop" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>