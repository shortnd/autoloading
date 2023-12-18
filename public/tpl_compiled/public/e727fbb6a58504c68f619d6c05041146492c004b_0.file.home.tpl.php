<?php
/* Smarty version 3.1.48, created on 2023-12-18 20:27:35
  from '/Users/collino/code/php/autoloading/templates/public/home.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6580ab37a21676_25453994',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e727fbb6a58504c68f619d6c05041146492c004b' => 
    array (
      0 => '/Users/collino/code/php/autoloading/templates/public/home.tpl',
      1 => 1702670279,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6580ab37a21676_25453994 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_2700418206580ab37a208b1_72989857', 'title');
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_982726016580ab37a21143_57569613', 'content');
$_smarty_tpl->inheritance->endChild($_smarty_tpl, 'base-layout.tpl');
}
/* {block 'title'} */
class Block_2700418206580ab37a208b1_72989857 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'title' => 
  array (
    0 => 'Block_2700418206580ab37a208b1_72989857',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

	Home
<?php
}
}
/* {/block 'title'} */
/* {block 'content'} */
class Block_982726016580ab37a21143_57569613 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_982726016580ab37a21143_57569613',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

	<h1>Home</h1>
<?php
}
}
/* {/block 'content'} */
}
