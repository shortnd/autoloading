<?php
/* Smarty version 3.1.48, created on 2023-12-15 19:59:34
  from '/Users/collino/code/php/autoloading/templates/home.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_657cb026785dc6_04389191',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '79260e4bd754745c78428183d40570ff27b1327e' => 
    array (
      0 => '/Users/collino/code/php/autoloading/templates/home.tpl',
      1 => 1702670279,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_657cb026785dc6_04389191 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1787869927657cb026785402_95371602', 'title');
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1128663569657cb026785a28_79846407', 'content');
$_smarty_tpl->inheritance->endChild($_smarty_tpl, 'base-layout.tpl');
}
/* {block 'title'} */
class Block_1787869927657cb026785402_95371602 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'title' => 
  array (
    0 => 'Block_1787869927657cb026785402_95371602',
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
class Block_1128663569657cb026785a28_79846407 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_1128663569657cb026785a28_79846407',
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
