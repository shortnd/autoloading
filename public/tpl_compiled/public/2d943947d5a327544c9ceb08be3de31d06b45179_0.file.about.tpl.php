<?php
/* Smarty version 3.1.48, created on 2023-12-18 20:27:35
  from '/Users/collino/code/php/autoloading/templates/public/about.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6580ab3729c132_44030013',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2d943947d5a327544c9ceb08be3de31d06b45179' => 
    array (
      0 => '/Users/collino/code/php/autoloading/templates/public/about.tpl',
      1 => 1702670786,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6580ab3729c132_44030013 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1575734796580ab3729b7a5_46631458', 'title');
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_2319293076580ab3729bdb1_11440661', 'content');
$_smarty_tpl->inheritance->endChild($_smarty_tpl, 'base-layout.tpl');
}
/* {block 'title'} */
class Block_1575734796580ab3729b7a5_46631458 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'title' => 
  array (
    0 => 'Block_1575734796580ab3729b7a5_46631458',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>
About<?php
}
}
/* {/block 'title'} */
/* {block 'content'} */
class Block_2319293076580ab3729bdb1_11440661 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_2319293076580ab3729bdb1_11440661',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

	<h1>About</h1>
<?php
}
}
/* {/block 'content'} */
}
