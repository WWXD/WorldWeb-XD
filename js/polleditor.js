
function addOption()
{
	var i = $('#pollOptions').children('.polloption').length;
	
	var opt = document.createElement('div');
	opt.className = 'polloption';
	opt.innerHTML = '<input type="text" name="pollOption['+i+']" size=48 maxlength=40> '+
		'&nbsp;Color: <input type="text" name="pollColor['+i+']" size=10 maxlength=7 class="color {hash:true,required:false,pickerFaceColor:\'black\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'black\',pickerPosition:\'left\',pickerMode:\'HVS\'}"> '+
		'&nbsp; <input type="submit" name="pollRemove['+i+']" value="&#xD7;" onclick="removeOption(this.parentNode);return false;"> ';
	
	$('#pollOptions').append(opt);
	jscolor.bind();
}

function removeOption(opt)
{
	$(opt).remove();
	
	var i;
	var opts = $('#pollOptions').children('.polloption');
	for (i = 0; i < opts.length; i++)
	{
		$(opts[i]).children("input[name^='pollOption']").attr('name', 'pollOption['+i+']');
		$(opts[i]).children("input[name^='pollColor']").attr('name', 'pollColor['+i+']');
		$(opts[i]).children("input[name^='pollRemove']").attr('name', 'pollRemove['+i+']');
	}
}

function addPoll()
{
	$('.pollModeOff').hide();
	$('.pollModeOn').show();
	$('#pollModeVal').val(1);
}

function removePoll()
{
	$('.pollModeOn').hide();
	$('.pollModeOff').show();
	$('#pollModeVal').val(0);
}
