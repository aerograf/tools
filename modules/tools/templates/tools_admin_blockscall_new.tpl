<{$css}>
<div class="form-nav">
    <ul>
<<<<<<< HEAD
    	<li class="first"><a href="blockscall.php"><{$smarty.const._AM_TOOLS_BC_MANAGE}></a></li>
    	<li class="middle-current"><{$smarty.const._AM_TOOLS_BC_ADDBLOCK}></li>
    	<li class="last"><{$smarty.const._AM_TOOLS_BC_EDITBLOCK}><span class="hook"></span></li>
    </ul>
</div>
<div style="padding:5px;">
<form action="admin.php" method="get"><label><{$smarty.const._AM_TOOLS_BC_SELECTMODULE}></label>
    <select name="selgen" onchange="location='blockscall.php?op=new&amp;selgen='+this.options[this.selectedIndex].value">
        <{foreach key=key item=item from=$modules}>
        <option value="<{$key}>" <{if $selgen == $key}>selected="selected"<{/if}>><{$item}></option>
        <{/foreach}>
    </select>
</form>
=======
        <li class="first"><a href="blockscall.php"><{$smarty.const._AM_TOOLS_BC_MANAGE}></a></li>
        <li class="middle-current"><{$smarty.const._AM_TOOLS_BC_ADDBLOCK}></li>
        <li class="last"><{$smarty.const._AM_TOOLS_BC_EDITBLOCK}><span class="hook"></span></li>
    </ul>
</div>
<div style="padding:5px;">
    <form action="admin.php" method="get"><label><{$smarty.const._AM_TOOLS_BC_SELECTMODULE}></label>
        <select name="selgen"
                onchange="location='blockscall.php?op=new&amp;selgen='+this.options[this.selectedIndex].value">
            <{foreach key=key item=item from=$moduleslist}>
            <option value="<{$key}>"
            <{if $selgen == $key}>selected="selected"<{/if}>><{$item}></option>
            <{/foreach}>
        </select>
    </form>
>>>>>>> 9e3b7476138197acca20e3a302aece912bb07e02
</div>
<{if $blocks}>
<table class='outer'>
    <tr valign='middle' align='center'>
        <th><{$smarty.const._AM_TOOLS_BC_NAME}></th>
        <th><{$smarty.const._AM_TOOLS_BC_TITLE}></th>
        <th><{$smarty.const._AM_TOOLS_BC_MODULE}></th>
        <th><{$smarty.const._AM_TOOLS_BC_ACTION}></th>
    </tr>
<<<<<<< HEAD
    <{foreachq item=block from=$blocks}>
=======
    <{foreach item=block from=$blocks}>
>>>>>>> 9e3b7476138197acca20e3a302aece912bb07e02
    <tr class="<{cycle values='odd, even'}>">
        <td><{$block.name}></td>
        <td><{$block.title}></td>
        <td><{$block.mname}></td>
        <td align=center><a href="blockscall.php?op=create&amp;bid=<{$block.bid}>"><{$smarty.const._AM_TOOLS_BC_CREATE}></a></td>
    </tr>
    <{/foreach}>
</table>
<{else}>
<{$smarty.const._AM_TOOLS_BC_NOBLOCKS}>
<<<<<<< HEAD
<{/if}>
=======
<{/if}>
>>>>>>> 9e3b7476138197acca20e3a302aece912bb07e02
