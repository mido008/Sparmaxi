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
defined( '_JEXEC' ) or die;
$addressTypes = array(
	'A' => JText::_('ESHOP_ALL'),
	'B' => JText::_('ESHOP_BILLING_ADDRESS'),
	'S' => JText::_('ESHOP_SHIPPING_ADDRESS')	
);
?>
<form action="index.php?option=com_eshop&view=fields" method="post" name="adminForm" id="adminForm">
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
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th class="text_left">
						<?php echo JHtml::_('grid.sort',  'ESHOP_NAME', 'a.name', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
					<th class="text_left">
						<?php echo JHtml::_('grid.sort',  'ESHOP_TITLE', 'a.title', $this->lists['order_Dir'], $this->lists['order']); ?>
					</th>
					<th class="text_center">
						<?php echo JHtml::_('grid.sort',  'ESHOP_FIELD_TYPE', 'a.field_type', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>			
					<th class="text_center">
						<?php echo JHtml::_('grid.sort',  'ESHOP_ADDRESS_TYPE', 'a.is_core', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<th class="text_center">
						<?php echo JHtml::_('grid.sort',  'ESHOP_REQUIRED', 'a.required', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>
					<th class="text_center">
						<?php echo JHtml::_('grid.sort',  'ESHOP_PUBLISHED', 'a.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
					</th>			
					<th width="8%" class="text_right">
						<?php echo JHtml::_('grid.sort',  'ESHOP_ORDER', 'a.ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?>
						<?php echo JHtml::_('grid.order',  $this->items , 'filesave.png', 'field.save_order' ); ?>
					</th>			  					
					<th width="5%" class="text_center">
						<?php echo JHtml::_('grid.sort',  'ID', 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
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
			$ordering = ($this->lists['order'] == 'a.ordering');
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)
			{
				$row = $this->items[$i];
				$link 	= JRoute::_( 'index.php?option=com_eshop&task=field.edit&cid[]='. $row->id );
				$checked 	= JHtml::_('grid.id',   $i, $row->id );		
				$published = JHtml::_('grid.published', $row, $i, 'tick.png', 'publish_x.png', 'field.' );					
				$img 	= $row->required ? 'tick.png' : 'publish_x.png';
				$task 	= $row->required ? 'un_required' : 'required';
				$alt 	= $row->required ? JText::_( 'Required' ) : JText::_( 'Not required' );
				$action = $row->required ? JText::_( 'Not Require' ) : JText::_( 'Require' );
		        $img = JHtml::_('image','admin/'.$img, $alt, array('border' => 0), true);
		        $href = '<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\'field.'. $task .'\')" title="'. $action .'">'. $img .'</a>';
		        $img 	= $row->is_core ? 'tick.png' : 'publish_x.png';
		        $img = JHtml::_('image','admin/'.$img, $alt, array('border' => 0), true);
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<a href="<?php echo $link; ?>">
							<?php echo $row->name; ?>
						</a>
					</td>
					<td>
						<a href="<?php echo $link; ?>">
							<?php echo $row->title; ?>
						</a>
					</td>
					<td class="center">
						<?php
							echo $row->fieldtype;
					 	?>
					</td>
					<td class="center">
						<?php echo $addressTypes[$row->address_type]; ?>
					</td>
					<td class="center">
						<?php echo $href; ?>
					</td>
					<td class="center">
						<?php echo $published; ?>
					</td>			
					<td class="order text_right">
						<span><?php echo $this->pagination->orderUpIcon( $i, true,'field.orderup', 'Move Up', $ordering ); ?></span>
						<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'field.orderdown', 'Move Down', $ordering ); ?></span>
						<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="input-mini" style="text-align: center" />
					</td>			  
					<td class="center">			
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
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>