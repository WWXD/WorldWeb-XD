{foreach $categories as $cat}
    <table class="outline margin forumlist">
        <tr class="header1">
            <th style="text-align:center!important;">{$cat.name}</th>
        </tr>
        <tr class="cell1">
            <td class="center">
                <table style="table-layout:fixed;{if $cat.forums|@count < 4}width:{$cat.forums|@count * 25}%;margin-left:auto;margin-right:auto;{/if}"><tr style="display:table-row;">
                    {foreach $cat.forums as $forum}
                        <td style="vertical-align:top;"{if $forum.ignored} class="ignored"{/if}>
                            <h3>{$forum.new} {$forum.link}</h3>
                            {if $forum.description}{$forum.description}<br>{/if}
                            {if $forum.localmods}Moderated by: {$forum.localmods}<br>{/if}

                            <div class="smallFonts">
                                {$forum.threads} {if $forum.threads == 1}thread{else}threads{/if}, {$forum.posts} {if $forum.posts == 1}post{else}posts{/if}<br>

                                {if $forum.lastpostdate}Last post: {$forum.lastpostdate}, by {$forum.lastpostuser} <a href="{$forum.lastpostlink}">&raquo;</a>{else}&mdash;{/if}
                            </div>

                            {if $forum.subforums}<br>Subforums: {$forum.subforums}<br>{/if}
                        </td>
                    {/foreach}
                </tr></table>
            </td>
        </tr>
    </table>
{/foreach}
