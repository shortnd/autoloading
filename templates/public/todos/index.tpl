{extends 'base-layout.tpl'}

{block title}Todos{/block}

{block content}
	<h1>Todos</h1>

	<a href="/todos/new">Add Todo</a>

	{if $todos|count}
		<ul>
			{foreach $todos as $todo}
				<li>
					{$todo->text}
				</li>
			{/foreach}
		</ul>
	{/if}
{/block}