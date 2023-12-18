<?php
/* Smarty version 3.1.48, created on 2023-12-15 20:08:20
  from '/Users/collino/code/php/autoloading/templates/public/home.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_657cb2340da8b5_56757607',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4dee5892d2ec828be7a34a0a1d6ecc375e199572' => 
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
function content_657cb2340da8b5_56757607 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_969769802657cb2340d9f21_28365102', 'title');
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1364808186657cb2340da536_69707308', 'content');
$_smarty_tpl->inheritance->endChild($_smarty_tpl, 'base-layout.tpl');
}
/* {block 'title'} */
class Block_969769802657cb2340d9f21_28365102 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'title' => 
  array (
    0 => 'Block_969769802657cb2340d9f21_28365102',
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
class Block_1364808186657cb2340da536_69707308 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_1364808186657cb2340da536_69707308',
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
