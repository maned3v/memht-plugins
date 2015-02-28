{if isset($plugin_forums) && sizeof($plugin_forums)>0}
	<div class="tpl_forums_table">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<thead>
				<tr class="forum_legend">
					<td width="2%">&nbsp;</td>
					<td width="50%">{t 1=FORUM}</td>
					<td width="10%" style="text-align:center;">{t 1=THREADS}</td>
					<td width="10%" style="text-align:center;">{t 1=POSTS}</td>
					<td width="28%" style="text-align:center;">{t 1=LAST_POST}</td>
				</tr>
			</thead>
			{foreach item=category from=$plugin_forums name=cnt}
				<thead>
					<tr class="cat_title">
						<th>&nbsp;</th>
						<th colspan="4">{$category.title}</th>
					</tr>
				</thead>
				{if isset($category.forums)}
					{foreach item=forum from=$category.forums name=cntf key=key}
						<tbody>
							<tr class="forum_title">
								<td width="2%" style="text-align:center;">.</td>
								<td width="50%">
									<strong><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;f={$forum.name}" title="{$forum.title}">{$forum.title}</a></strong>
									{if !empty($forum.description)}
										<div class="forum_description">{$forum.description}</div>
									{/if}
									
									{if isset($forum.subforums) && sizeof($forum.subforums)>0}
										<div class="subforum_title">
											<div>{t 1=SUBFORUMS}</div>
											{foreach item=subforum from=$forum.subforums name=cntsf}
												<div><img src="images/core/bullet.png" alt="{$key}" /><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;f={$subforum.name}" title="{$subforum.title}">{$subforum.title}</a></div>
											{/foreach}
										</div>
									{/if}
								</td>
								<td width="10%" style="text-align:center;">{$forum.threads}</td>
								<td width="10%" style="text-align:center;">{$forum.posts}</td>
								<td width="28%" style="text-align:center;">
									{if isset($forum.lastpost)}
										<div><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;f={$forum.name}&amp;t={$forum.lastpost.thread_id}{$forum.lastpost.pagination}#post{$forum.lastpost.id}">{$forum.lastpost.title}</a></div>
										<div><strong>{$forum.lastpost.author_name}</strong></div>
										<div>{$forum.lastpost.created}</div>
									{else}
										-
									{/if}
								</td>
							</tr>
						</tbody>
					{/foreach}
				{/if}
			{/foreach}
		</table>
	</div>
{/if}

{if isset($plugin_subforums) && sizeof($plugin_subforums)>0}
	<div class="tpl_forums_table">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<thead>
				<tr class="forum_legend">
					<td width="2%">&nbsp;</td>
					<td width="50%">{t 1=SUBFORUM}</td>
					<td width="10%" style="text-align:center;">{t 1=THREADS}</td>
					<td width="10%" style="text-align:center;">{t 1=POSTS}</td>
					<td width="28%" style="text-align:center;">{t 1=LAST_POST}</td>
				</tr>
			</thead>
			<thead>
				<tr class="cat_title">
					<th>&nbsp;</th>
					<th colspan="4">{$plugin_data.forum_title}</th>
				</tr>
			</thead>
			<tbody>
				{foreach item=subforum from=$plugin_subforums name=cntsf}
					<tr class="forum_title">
						<td style="text-align:center;">.</td>
						<td>
							<strong><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;f={$subforum.name}" title="{$subforum.title}">{$subforum.title}</a></strong>
							{if !empty($subforum.description)}
								<div class="forum_description">{$subforum.description}</div>
							{/if}
						</td>
						<td style="text-align:center;">{$subforum.threads}</td>
						<td style="text-align:center;">{$subforum.posts}</td>
						<td style="text-align:center;">
							{if isset($subforum.lastpost)}
								<div><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;f={$subforum.name}&amp;t={$subforum.lastpost.thread_id}{$subforum.lastpost.pagination}#post{$subforum.lastpost.id}">{$subforum.lastpost.title}</a></div>
								<div><strong>{$subforum.lastpost.author_name}</strong></div>
								<div>{$subforum.lastpost.created}</div>
							{else}
								-
							{/if}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{/if}

{if isset($plugin_threads) && isset($plugin_data.op) && $plugin_data.op == 'threads'}
	<div class="tpl_forums_buttons">
		{if isset($plugin_data.buttons.write) && $plugin_data.buttons.write>0} 
			<div><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;op=write&amp;f={$plugin_data.forum}" title="{t 1=NEW_THREAD}">{t 1=NEW_THREAD}</a></div>
		{/if}
		<span style="display:block;clear:both;"></span>
	</div>
	
	<div class="tpl_forums_table">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<thead>
				<tr class="forum_legend">
					<td width="2%">&nbsp;</td>
					<td width="50%">{t 1=THREAD}</td>
					<td width="10%" style="text-align:center;">{t 1=REPLIES}</td>
					<td width="10%" style="text-align:center;">{t 1=VIEWS}</td>
					<td width="28%" style="text-align:center;">{t 1=LAST_POST}</td>
				</tr>
			</thead>
			<thead>
				<tr class="thread_title">
					<th>&nbsp;</th>
					<th colspan="4">{$plugin_data.forum_title}</th>
				</tr>
			</thead>
			<tbody>
				{foreach item=thread from=$plugin_threads key=id name=cntsf}
					<tr class="thread_title">
						<td style="text-align:center;">.</td>
						<td>
							{if ($thread.flag>0)}
								{t 1=STICKY}:&nbsp;
							{/if}
							<strong><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;f={$thread.forum}&amp;t={$id}" title="{$thread.title}">{$thread.title}</a></strong>
							<div class="thread_author">{t 1=AUTHOR}: {$thread.author_name}</div>
						</td>
						<td style="text-align:center;">{$thread.replies}</td>
						<td style="text-align:center;">{$thread.views}</td>
						<td style="text-align:center;">
							{if isset($thread.lastpost)}
								<div><a href="index.php?{$smarty.const._NODE}={$smarty.const._PLUGIN}&amp;f={$thread.forum}&amp;t={$thread.lastpost.thread_id}{$thread.lastpost.pagination}#post{$thread.lastpost.id}">{$thread.lastpost.title}</a></div>
								<div><strong>{$thread.lastpost.author_name}</strong></div>
								<div>{$thread.lastpost.created}</div>
							{else}
								-
							{/if}
						</td>
					</tr>
				{foreachelse}
					<tr class="thread_title"><td colspan="5" style="text-align:center;">{t 1=NO_THREADS_CREATE_FIRST}</td></tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{/if}