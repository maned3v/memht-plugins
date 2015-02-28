{if isset($plugin_form)}
	<form action="{$plugin_form.form.action}" method="{$plugin_form.form.method}" enctype="{$plugin_form.form.enctype}">
		<div class="tpl_forums_row">
			<span class="tpl_forums_label"><label>{t 1=TITLE}</label></span>
			<input type="text" name="title" value="{$plugin_form.form.title}" class="tpl_forums_input" />
		</div>
		<div class="tpl_forums_row">
			<span class="tpl_forums_label"><label>{t 1=TEXT}</label></span>
			<textarea name="text" class="tpl_forums_textarea bbcode">{$plugin_form.form.text}</textarea>
		</div>
		{if isset($plugin_form.form.additional)}
			{foreach item=addon from=$plugin_form.form.additional name=cnt}
				<div class="tpl_forums_row">
					{$addon.content}
				</div>
			{/foreach}
		{/if}
		<div class="tpl_forums_row">
			<input type="submit" name="submit" value="{t 1=SAVE}" class="tpl_forums_button" />
		</div>
		<input name="ctok" type="hidden" value="{$plugin_form.form.ctok}" />
		<input name="ftok" type="hidden" value="{$plugin_form.form.ftok}" />
	</form>
{/if}