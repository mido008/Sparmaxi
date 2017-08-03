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
$ordering = ($this->lists['order'] == 'a.ordering');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
?>
<form action="index.php?option=com_eshop&view=lengths" method="post" name="adminForm" id="adminForm">
	<table width="100%">
		<tr>
			<td align="left">
				<?php echo JText::_( 'ESHOP_FILTER' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->state->search; ?>" class="text_area search-query" onchange="document.adminForm.submit();" />		
				<button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'ESHOP_GO' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();" class="btn"><?php echo JText::_( 'ESHOP_RESET' ); ?></button>		
			</td>
			<td align="right">
				<?php echo $this->lists['filter_state']; ?>
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
					<th class="text_left" width="30%">
						<?php echo JHtml::_('grid.sort',  JText::_('ESHOP_NAME'), 'a.length_name', $this->lists['order_Dir'], $this->lists['order'] ); ?>				
					</th>
					<th class="text_left" width="10%">
						<?php echo JHtml::_('grid.sort',  JText::_('ESHOP_LENGTH_UNIT'), 'b.length_unit', $this->lists['order_Dir'], $this->lists['order'] ); ?>				
					</th>
					<th class="text_left" width="10%">
						<?php echo JHtml::_('grid.sort',  JText::_('ESHOP_WEIGHT_EXCHANGED_VALUE'), 'a.exchanged_value', $this->lists['order_Dir'], $this->lists['order'] ); ?>				
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
					<th width="10%" class="text_right">
						<?php echo JHtml::_('grid.sort',  JText::_('ESHOP_ORDER'), 'a.ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?>
						<?php echo JHtml::_('grid.order',  $this->items , 'filesave.png', 'length.save_order' ); ?>
					</th>
					<th width="5%" class="text_center">
						<?php echo JHtml::_('grid.sort',  JText::_('ESHOP_ID'), 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>													
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="8">
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
				$link 	= JRoute::_( 'index.php?option=com_eshop&task=length.edit&cid[]='. $row->id);
				$checked 	= JHtml::_('grid.id',   $i, $row->id );
				$published 	= JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png', 'length.' );			
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td class="text_center">
						<?php echo $this->pagination->getRowOffset( $i ); ?>
					</td>
					<td class="text_center">
						<?php echo $checked; ?>
					</td>
					<?php
					if (EshopHelper::isJ3())
					{
						?>
						<td class="text_center">
							<div class="btn-group">
								<?php
								echo JHtml::_('jgrid.published', $row->published, $i, 'length.');
								echo $this->addDropdownList(JText::_('ESHOP_COPY'), 'copy', $i, 'length.copy');
								echo $this->addDropdownList(JText::_('ESHOP_DELETE'), 'trash', $i, 'length.remove');
								echo $this->renderDropdownList($this->escape($row->length_name));
								?>
							</div>
						</td>
						<?php
					}
					?>
					<td class="text_left">
						<a href="<?php echo $link; ?>"><?php echo $row->length_name; ?></a>
					</td>
					<td class="text_left">
						<?php echo $row->length_unit; ?>
					</td>
					<td class="text_left">
						<?php echo $row->exchanged_value; ?>
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
					<td class="order text_right">
						<span><?php echo $this->pagination->orderUpIcon( $i, true, 'length.orderup', 'Move Up', $ordering ); ?></span>
						<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'length.orderdown', 'Move Down', $ordering ); ?></span>
						<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>				
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="input-mini" style="text-align: center" <?php echo $disabled; ?> />
					</td>	
					<td class="text_center">
						<?php echo $row->id; ?>
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