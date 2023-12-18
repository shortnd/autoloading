<?php
/* Smarty version 3.1.48, created on 2023-12-15 20:08:20
  from '/Users/collino/code/php/autoloading/templates/base-layout.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_657cb2340ddf11_18968824',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0498b275f9e5adad0bb52b76777a103a0f88af93' => 
    array (
      0 => '/Users/collino/code/php/autoloading/templates/base-layout.tpl',
      1 => 1702670306,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_657cb2340ddf11_18968824 (Smarty_Internal_Template $_smarty_tpl) {
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
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1732177886657cb2340dd7e8_89430839', 'title');
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
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_949656442657cb2340ddc33_20946011', 'content');
?>

</body>
</html><?php }
/* {block 'title'} */
class Block_1732177886657cb2340dd7e8_89430839 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'title' => 
  array (
    0 => 'Block_1732177886657cb2340dd7e8_89430839',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>
GW Lava Autoloading<?php
}
}
/* {/block 'title'} */
/* {block 'content'} */
class Block_949656442657cb2340ddc33_20946011 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_949656442657cb2340ddc33_20946011',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'content'} */
}
