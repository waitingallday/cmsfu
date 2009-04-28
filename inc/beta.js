$(function() {
	$('#searchterm').focus(function() { if ($(this).val() == 'Enter keywords') { $(this).val(''); this.style.color = '#000'; } });
	$('#searchterm').blur(function() { if (this.value=='') { this.value='Enter keywords'; this.style.color = '#999'; } });
});

