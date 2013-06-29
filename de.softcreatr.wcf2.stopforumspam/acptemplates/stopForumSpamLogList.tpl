{include file='header' pageTitle='wcf.acp.stopforumspam.log'}

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		WCF.Language.addObject({
			'wcf.acp.stopforumspam.log.clear.confirm': '{lang}wcf.acp.stopforumspam.log.clear.confirm{/lang}',
			'wcf.acp.stopforumspam.log.error.details': '{lang}wcf.acp.stopforumspam.log.error.details{/lang}'
		});

		/**
		 * ---
		 */
		WCF.ACP.StopForumSpam = { };

		/**
		 * ---
		 */
		WCF.ACP.StopForumSpam.LogList = Class.extend({
			/**
			 * error message dialog
			 * @var	jQuery
			 */
			_dialog: null,
			
			/**
			 * Initializes WCF.ACP.StopForumSpam.LogList object.
			 */
			init: function() {
				// bind event listener to delete StopForumSpam log button
				$('.jsStopForumSpamLogDelete').click(function() {
					WCF.System.Confirmation.show(WCF.Language.get('wcf.acp.stopforumspam.log.clear.confirm'), function(action) {
						if (action == 'confirm') {
							new WCF.Action.Proxy({
								autoSend: true,
								data: {
									actionName: 'clearAll',
									className: 'wcf\\data\\stopforumspam\\log\\StopForumSpamLogAction'
								},
								success: function() {
									window.location.reload();
								}
							});
						}
					});
				});
				
				// bind event listeners to error badges
				$('.jsStopForumSpamError').click($.proxy(this._showError, this));
			},
			
			/**
			 * Shows certain error message
			 * 
			 * @param	object		event
			 */
			_showError: function(event) {
				var $errorBadge = $(event.currentTarget);
				
				if (this._dialog === null) {
					this._dialog = $('<div style="overflow: auto"><pre>' + $errorBadge.next().html() + '</pre></div>').hide().appendTo(document.body);
					this._dialog.wcfDialog({
						title: WCF.Language.get('wcf.acp.stopforumspam.log.error.details')
					});
				}
				else {
					this._dialog.html('<pre>' + $errorBadge.next().html() + '</pre>');
					this._dialog.wcfDialog('open');
				}
			}
		});
		
		new WCF.ACP.StopForumSpam.LogList();
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.acp.stopforumspam.log{/lang}</h1>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller="StopForumSpamLogList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	
	{hascontent}
		<nav>
			<ul>
				{if $objects|count}
					<li><a title="{lang}wcf.acp.stopforumspam.log.clear{/lang}" class="button jsStopForumSpamLogDelete"><span class="icon icon16 icon-remove"></span> <span>{lang}wcf.acp.stopforumspam.log.clear{/lang}</span></a></li>
				{/if}
				
				{content}
					{event name='contentNavigationButtonsTop'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

{hascontent}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}wcf.acp.stopforumspam.log{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>
		
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnLogID{if $sortField == 'logID'} active {@$sortOrder}{/if}"><a href="{link controller='StopForumSpamLogList'}pageNo={@$pageNo}&sortField=logID&sortOrder={if $sortField == 'logID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle columnUsername{if $sortField == 'username'} active {@$sortOrder}{/if}"><a href="{link controller='StopForumSpamLogList'}pageNo={@$pageNo}&sortField=username&sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.stopforumspam.log.username{/lang}</a></th>
					<th class="columnDigits columnIPAddress{if $sortField == 'ipAddress'} active {@$sortOrder}{/if}"><a href="{link controller='StopForumSpamLogList'}pageNo={@$pageNo}&sortField=ipAddress&sortOrder={if $sortField == 'ipAddress' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.stopforumspam.log.ipaddress{/lang}</a></th>
					<th class="columnText columnEmail{if $sortField == 'email'} active {@$sortOrder}{/if}"><a href="{link controller='StopForumSpamLogList'}pageNo={@$pageNo}&sortField=email&sortOrder={if $sortField == 'email' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.stopforumspam.log.email{/lang}</a></th>
					<th class="columnDate columnLogDate{if $sortField == 'logDate'} active {@$sortOrder}{/if}"><a href="{link controller='StopForumSpamLogList'}pageNo={@$pageNo}&sortField=logDate&sortOrder={if $sortField == 'logDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.stopforumspam.log.logdate{/lang}</a></th>
					<th class="columnText columnEventClassName{if $sortField == 'eventClassName'} active {@$sortOrder}{/if}"><a href="{link controller='StopForumSpamLogList'}pageNo={@$pageNo}&sortField=eventClassName&sortOrder={if $sortField == 'eventClassName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.stopforumspam.log.eventclassname{/lang}</a></th>
					<th class="columnText columnEventName{if $sortField == 'eventName'} active {@$sortOrder}{/if}"><a href="{link controller='StopForumSpamLogList'}pageNo={@$pageNo}&sortField=eventName&sortOrder={if $sortField == 'eventName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.stopforumspam.log.eventname{/lang}</a></th>
					<th class="columnText columnStatus{if $sortField == 'status'} active {@$sortOrder}{/if}"><a href="{link controller='StopForumSpamLogList'}pageNo={@$pageNo}&sortField=status&sortOrder={if $sortField == 'status' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.stopforumspam.log.status{/lang}</a></th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{content}
					{foreach from=$objects item=stopForumSpamLog}
						<tr>
							<td class="columnID columnLogID">{@$stopForumSpamLog->logID}</td>
							<td class="columnTitle columnUsername">
								{if !$stopForumSpamLog->username|empty}
									{if $stopForumSpamLog->userID}
										<a title="{lang}wcf.acp.user.edit{/lang}" href="{link controller='UserEdit' id=$stopForumSpamLog->userID}{/link}">{$stopForumSpamLog->username}</a>
									{else}
										{$stopForumSpamLog->username}
									{/if}
								{else}
									&mdash;
								{/if}
							</td>
							{if !$stopForumSpamLog->ipAddress|empty}
								<td class="columnURL columnIPAddress">
									{assign var=ipCheckLink value="http://www.stopforumspam.com/search/"|concat:$stopForumSpamLog->ipAddress|rawurlencode}
									<a href="{@$__wcf->getPath()}acp/dereferrer.php?url={$ipCheckLink}">{$stopForumSpamLog->ipAddress}</a>
								</td>
							{else}
								<td class="columnText columnIPAddress">
									&mdash;
								</td>
							{/if}
							{if !$stopForumSpamLog->email|empty}
								<td class="columnURL columnEmail">
									{assign var=ipCheckLink value="http://www.stopforumspam.com/search/"|concat:$stopForumSpamLog->email|rawurlencode}
									<a href="{@$__wcf->getPath()}acp/dereferrer.php?url={$ipCheckLink}">{$stopForumSpamLog->email}</a>
								</td>
							{else}
								<td class="columnText columnEmail">
									&mdash;
								</td>
							{/if}
							<td class="columnDate columnLogDate">{@$stopForumSpamLog->logDate|time}</td>
							<td class="columnText columnEventClassName">{$stopForumSpamLog->eventClassName}</td>
							<td class="columnText columnEventName">{$stopForumSpamLog->eventName}</td>
							<td class="columnText columnStatus">
								{if $stopForumSpamLog->error}
									<a class="badge red jsTooltip jsStopForumSpamError" title="{lang}wcf.acp.stopforumspam.log.error.showdetails{/lang}">{lang}wcf.stopforumspam.log.error{/lang}</a>
									<span style="display: none">{@$stopForumSpamLog->error|language}</span>
								{elseif $stopForumSpamLog->status}
									<span class="badge red">{lang}wcf.stopforumspam.log.blocked{/lang}</span>
								{else}
									<span class="badge green">{lang}wcf.stopforumspam.log.success{/lang}</span>
								{/if}
							</td>
							
							{event name='columns'}
						</tr>
					{/foreach}
				{/content}
			</tbody>
		</table>
	</div>
	
	<div class="contentNavigation">
		{@$pagesLinks}
		
		<nav>
			<ul>
				<li><a title="{lang}wcf.acp.stopforumspam.log.clear{/lang}" class="button jsStopForumSpamLogDelete"><span class="icon icon16 icon-remove"></span> <span>{lang}wcf.acp.stopforumspam.log.clear{/lang}</span></a></li>
				
				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>
	</div>
{hascontentelse}
	<p class="info">{lang}wcf.acp.stopforumspam.log.noEntries{/lang}</p>
{/hascontent}

{include file='footer'}
