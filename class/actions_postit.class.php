<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_postit.class.php
 * \ingroup postit
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class Actionspostit
 */
class Actionspostit
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	//function printTopRightMenu($parameters, &$object, &$action, $hookmanager)
	function formObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		$error = 0; // Error counter
		
		if (in_array('globalcard', explode(':', $parameters['context'])))
		{
			global $langs;
				
			$a = '<a href="javascript:addNote()" style="position:absolute; left:-30px; top:0px; display:block;">'.img_picto('', 'post-it.png@postit',' style="width:32px; height:32px;" ').'</a>';
			
			?>
			<script language="javascript">
				$(document).ready(function() {
					$a = $('<?php echo $a ?>');
					$('div.login_block_other').append($a);	
					
					$.ajax({
						url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
						,data: {
							get:'postit-of-object'
							,fk_object:<?php echo $object->id ?>
							,type_object:"<?php echo $object->element ?>"
						
						}
						,dataType:"json"
					}).done(function(Tab) {
						
						for(x in Tab) {
							id = Tab[x];
							addNote(id);
						}
						
					});
									
				});
				
				function addNote(idPostit) {
					
					$div = $('<div><div rel="postit-title"><?php echo $langs->trans('NewNote') ?></div><div rel="postit-comment"><?php echo $langs->trans('NoteComment') ?></div></div>');
					
					if(idPostit) $div.attr('id-post-it', idPostit);
					
					$div.find('[rel=postit-title]').click(function() {
						console.log('title');
					});
					$div.find('[rel=postit-comment]').click(function() {
						console.log('comment');
					});
					
					$div.dialog({
						title:""
						,width:100
						,height:200
						,dialogClass:'yellowPaperTemporary postit'
						,closeOnEscape: false
						,position: { at : "center bottom", of:"div.login_block_other" }
						,dragStop:function(event, ui) {
							
							var $div = $(this);
							var idPostit = $(this).attr('id-post-it');
							
							$.ajax({
								url:"<?php echo dol_buildpath('/postit/script/interface.php',1) ?>"
								,data: {
									put:'postit'
									,id:idPostit
									,fk_object:<?php echo $object->id ?>
									,type_object:"<?php echo $object->element ?>"
									,top:ui.position.top
									,left:	ui.position.left
								}
								,method:'post'
							}).done(function(idPostit) {
								$div.attr('id-post-it', idPostit);
							});
							
						}
						
					})
					
				}
				
				
			</script>
			<?php
			
		}

		if (! $error)
		{
			return 0; // or return 1 to replace standard code
		}
		else
		{
			return -1;
		}
	}
}