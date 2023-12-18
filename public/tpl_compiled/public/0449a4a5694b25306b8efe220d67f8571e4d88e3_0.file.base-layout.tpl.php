<?php
/* Smarty version 3.1.48, created on 2023-12-18 20:27:35
  from '/Users/collino/code/php/autoloading/templates/public/base-layout.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6580ab37a25e60_37690543',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0449a4a5694b25306b8efe220d67f8571e4d88e3' => 
    array (
      0 => '/Users/collino/code/php/autoloading/templates/public/base-layout.tpl',
      1 => 1702670306,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6580ab37a25e60_37690543 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_20468660856580ab37a25372_43908924', 'title');
?>
</title>
</head>
<body>
	<header>
		<nav>
			<a href="/">Home</a> |
			<a href="/about">About</a>
		</nav>
	</header>
	<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_20560531716580ab37a259e5_27194013', 'content');
?>

</body>
</html><?php }
/* {block 'title'} */
class Block_20468660856580ab37a25372_43908924 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'title' => 
  array (
    0 => 'Block_20468660856580ab37a25372_43908924',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>
GW Lava Autoloading<?php
}
}
/* {/block 'title'} */
/* {block 'content'} */
class Block_20560531716580ab37a259e5_27194013 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_20560531716580ab37a259e5_27194013',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'content'} */
}
