<?php
/**
 * @version		1.4.1
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2012 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
$ordering = ($this->lists['order'] == 'a.ordering');
?>
<form action="index.php?option=com_eshop&view=shippings&type=0" method="post" name="adminForm" enctype="multipart/form-data" id="adminForm">
	<table width="100%">
		<tr>
			<td align="left">
				<?php echo JText::_( 'ESHOP_FILTER' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->state->search;?>" class="text_area search-query" onchange="document.adminForm.submit();" />		
				<button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'ESHOP_GO' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();" class="btn"><?php echo JText::_( 'ESHOP_RESET' ); ?></button>		
			</td>	
			<td style="text-align: right;">		
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
					<th class="text_left" widht="15%">
						<?php echo JHtml::_('grid.sort',  JText::_('ESHOP_NAME'), 'a.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<th class="text_left" width="15%">
						<?php echo JHtml::_('grid.sort', JText::_('ESHOP_TITLE'), 'a.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<th class="text_left" width="15%">
						<?php echo JHtml::_('grid.sort', JText::_('ESHOP_AUTHOR') , 'a.author', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<th class="text_left" width="15%">
						<?php echo JHtml::_('grid.sort', JText::_('ESHOP_AUTHOR_EMAIL'), 'a.email', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<?php
					if (!EshopHelper::isJ3())
					{
						?>
						<th class="text_center" width="10%">
							<?php echo JHtml::_('grid.sort', JText::_('ESHOP_PUBLISHED') , 'a.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
						</th>
						<?php
					}
					?>
					<th width="15%" class="text_right">
						<?php echo JHtml::_('grid.sort',  'ESHOP_ORDER', 'a.ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?>
						<?php echo JHtml::_('grid.order',  $this->items , 'filesave.png', 'save_shipping_order' ); ?>
					</th>												
					<th class="text_center" width="5%">
						<?php echo JHtml::_('grid.sort', JText::_('ESHOP_ID') , 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
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
				$link 	= JRoute::_( 'index.php?option=com_eshop&task=shipping.edit&cid[]='. $row->id );
				$checked 	= JHtml::_('grid.id',   $i, $row->id );
				$published 	= JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png', 'shipping.' );
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
								echo JHtml::_('jgrid.published', $row->published, $i, 'shipping.');
								echo $this->addDropdownList(JText::_('ESHOP_DELETE'), 'trash', $i, 'shipping.remove');
								echo $this->renderDropdownList($this->escape($row->name));
								?>
							</div>
						</td>
						<?php
					}
					?>
					<td>
						<a href="<?php echo $link; ?>">
							<?php echo $row->name; ?>
						</a>
					</td>
					<td>
						<?php echo $row->title; ?>
					</td>												
					<td>
						<?php echo $row->author; ?>
					</td>
					<td>
						<?php echo $row->author_email;?>
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
						<span><?php echo $this->pagination->orderUpIcon( $i, true,'shipping.orderup', 'Move Up', $ordering ); ?></span>
						<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'shipping.orderdown', 'Move Down', $ordering ); ?></span>
						<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="input-mini" style="text-align: center" />
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
		<table class="adminform" style="margin-top: 20px;">
			<tr>
				<td>
					<fieldset class="adminform">
						<legend><?php echo JText::_('ESHOP_INSTALL_NEW_SHIPPING_PLUGIN'); ?></legend>
						<table>
							<tr>
								<td>
									<input type="file" name="plugin_package" id="plugin_package" size="57" class="input_box" />
									<input type="button" class="btn btn-primary" value="<?php echo JText::_('ESHOP_INSTALL'); ?>" onclick="installPlugin();" />
								</td>
							</tr>
						</table>					
					</fieldset>
				</td>
			</tr>		
		</table>
	</div>
	<input type="hidden" name="option" value="com_eshop" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />	
	<?php echo JHtml::_( 'form.token' ); ?>				 
	<script type="text/javascript">
		function installPlugin() {
			var form = document.adminForm;
			if (form.plugin_package.value =="") {
				alert("<?php echo JText::_('ESHOP_CHOOSE_SHIPPING_PLUGIN'); ?>");
				return;	
			}
			
			form.task.value = 'shipping.install';
			form.submit();
		}
	</script>
</form>