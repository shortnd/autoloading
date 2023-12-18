<?php
/* Smarty version 3.1.48, created on 2023-12-18 14:37:01
  from '/Users/collino/code/php/autoloading/templates/public/todos/index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6580590d67fc41_77098705',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '361843beab80cb7030901eedffc41aefd3722e09' => 
    array (
      0 => '/Users/collino/code/php/autoloading/templates/public/todos/index.tpl',
      1 => 1702674440,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6580590d67fc41_77098705 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_9478185296580590d67d3d5_19930452', 'title');
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_20935486166580590d67daa2_74869483', 'content');
$_smarty_tpl->inheritance->endChild($_smarty_tpl, 'base-layout.tpl');
}
/* {block 'title'} */
class Block_9478185296580590d67d3d5_19930452 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'title' => 
  array (
    0 => 'Block_9478185296580590d67d3d5_19930452',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>
Todos<?php
}
}
/* {/block 'title'} */
/* {block 'content'} */
class Block_20935486166580590d67daa2_74869483 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_20935486166580590d67daa2_74869483',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

	<h1>Todos</h1>

	<a href="/todos/new">Add Todo</a>

	<?php if (count($_smarty_tpl->tpl_vars['todos']->value)) {?>
		<ul>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['todos']->value, 'todo');
$_smarty_tpl->tpl_vars['todo']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['todo']->value) {
$_smarty_tpl->tpl_vars['todo']->do_else = false;
?>
				<li>
					<?php echo $_smarty_tpl->tpl_vars['todo']->value->text;?>

				</li>
			<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</ul>
	<?php }
}
}
/* {/block 'content'} */
}
